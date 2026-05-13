/**
 * GRACE[M-CONTENT-PIPELINE][enrich-guides][BLOCK_ENRICH]
 * PURPOSE: Авто-нормализация уже мигрированных гайдов в content/guides/*.md.
 * SCOPE:
 *  - транслитерация slug (имена файлов с кириллицей);
 *  - заполнение недостающих frontmatter-полей: topic, status, audience, gameVersion,
 *    updatedAt, reviewedAt, sources, relatedCharacters, relatedGuides, summary;
 *  - чистка тела: ссылки `[label](#)` -> просто `label`;
 *  - запись карты редиректов в reports/slug-redirects.json и deploy/genshintop-redirects.conf
 *    для подключения через nginx-docker.conf.
 * Запуск: npm run content:enrich
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import type { GuideCategory } from './guide-taxonomy.js';
import {
  extractGameVersion,
  inferAudience,
  inferStatus,
  inferTopic,
} from './guide-taxonomy.js';
import { cleanMetaDescription } from './seo-helpers.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const GUIDES_DIR = path.join(ROOT, 'content', 'guides');
const CHAR_DIR = path.join(ROOT, 'content', 'characters');
const REPORTS_DIR = path.join(ROOT, 'reports');
const REDIRECTS_JSON = path.join(REPORTS_DIR, 'slug-redirects.json');
const REDIRECTS_CONF = path.join(ROOT, 'deploy', 'genshintop-redirects.conf');

// ---------- Транслитерация ----------
const TRANSLIT_RU: Record<string, string> = {
  а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'e', ж: 'zh', з: 'z',
  и: 'i', й: 'y', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r',
  с: 's', т: 't', у: 'u', ф: 'f', х: 'h', ц: 'ts', ч: 'ch', ш: 'sh', щ: 'sch',
  ъ: '', ы: 'y', ь: '', э: 'e', ю: 'yu', я: 'ya',
};

function transliterateForSlug(s: string): string {
  let out = '';
  for (const ch of s) {
    const lo = ch.toLowerCase();
    const tr = TRANSLIT_RU[lo];
    out += tr !== undefined ? tr : lo;
  }
  return out;
}

function slugifyFileBase(base: string): string {
  let s = base.replace(/\.md$/i, '').replace(/#/g, '');
  s = transliterateForSlug(s);
  return s
    .replace(/\s+/g, '-')
    .replace(/[^\p{L}\p{N}_-]+/gu, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '')
    .toLowerCase()
    .slice(0, 160);
}

// ---------- FM парсер ----------
type FmValue = string | number | boolean | string[] | undefined;

interface ParsedFm {
  data: Record<string, FmValue>;
  /** Исходный порядок ключей. */
  order: string[];
}

function splitFm(raw: string): { fm: string; body: string } | null {
  const m = raw.match(/^---\r?\n([\s\S]*?)\r?\n---\r?\n?([\s\S]*)$/);
  if (!m) return null;
  return { fm: m[1], body: m[2] ?? '' };
}

function parseFm(fmText: string): ParsedFm {
  const data: Record<string, FmValue> = {};
  const order: string[] = [];
  const lines = fmText.split(/\r?\n/);
  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    const m = /^([A-Za-z][A-Za-z0-9_]*)\s*:\s*(.*)$/.exec(line);
    if (!m) continue;
    const key = m[1];
    const rest = m[2];
    if (rest.trim() === '') {
      // possible list
      const items: string[] = [];
      while (i + 1 < lines.length) {
        const next = lines[i + 1];
        const li = /^\s+-\s+(.*)$/.exec(next);
        if (!li) break;
        let v = li[1].trim();
        if (
          (v.startsWith('"') && v.endsWith('"')) ||
          (v.startsWith("'") && v.endsWith("'"))
        ) {
          try {
            v = JSON.parse(v.replace(/'/g, '"'));
          } catch {
            v = v.slice(1, -1);
          }
        }
        if (v && v !== '-') items.push(v);
        i++;
      }
      data[key] = items;
    } else {
      let v = rest.trim();
      if (v.startsWith('"') || v.startsWith("'")) {
        try {
          v = JSON.parse(v.startsWith("'") ? `"${v.slice(1, -1)}"` : v);
        } catch {
          v = v.replace(/^['"]|['"]$/g, '');
        }
      }
      if (/^\d+$/.test(v)) data[key] = Number(v);
      else if (v === 'true' || v === 'false') data[key] = v === 'true';
      else data[key] = v;
    }
    if (!order.includes(key)) order.push(key);
  }
  return { data, order };
}

function yamlEscape(s: string): string {
  if (/[:#\n"'\\]|^\s|\s$|^\d/.test(s)) return JSON.stringify(s);
  return s;
}

const KEY_ORDER = [
  'title',
  'category',
  'topic',
  'gameVersion',
  'status',
  'audience',
  'date',
  'updatedAt',
  'reviewedAt',
  'summary',
  'sourceSlug',
  'sourcePath',
  'relatedCharacters',
  'relatedGuides',
  'sources',
];

function buildFm(
  data: Record<string, FmValue>,
  preferredOrder: string[],
): string {
  const seen = new Set<string>();
  const lines: string[] = ['---'];
  const writeKey = (k: string) => {
    if (seen.has(k)) return;
    seen.add(k);
    const v = data[k];
    if (v === undefined || v === null) return;
    if (Array.isArray(v)) {
      if (v.length === 0) return;
      lines.push(`${k}:`);
      for (const item of v) {
        lines.push(`  - ${JSON.stringify(String(item))}`);
      }
      return;
    }
    if (typeof v === 'number' || typeof v === 'boolean') {
      lines.push(`${k}: ${v}`);
      return;
    }
    const s = String(v);
    if (s === '') return;
    lines.push(`${k}: ${yamlEscape(s)}`);
  };
  for (const k of preferredOrder) writeKey(k);
  for (const k of Object.keys(data)) writeKey(k);
  lines.push('---');
  return lines.join('\n');
}

// ---------- Эвристики и чистка ----------
function looksBadSummary(raw: string): boolean {
  if (!raw) return true;
  const trimmed = raw.replace(/^[\sㅤ\u200b\ufeff]+/, '');
  if (trimmed.length < 60) return true;
  if (/^содержание/i.test(trimmed)) return true;
  if (/^молитва события/i.test(trimmed)) return true;
  if (/^список обновлений/i.test(trimmed)) return true;
  if (/^глава\s+\d/i.test(trimmed)) return true;
  return false;
}

function deriveSummary(body: string): string | undefined {
  const noHead = body.replace(/^#[^\n]+\n+/, '');
  const para = noHead
    .split(/\n\n+/)
    .map((p) => p.replace(/\s+/g, ' ').trim())
    .find((p) => {
      if (p.length < 80) return false;
      if (/^содержание/i.test(p)) return false;
      if (/^молитва события/i.test(p)) return false;
      if (/^список обновлений/i.test(p)) return false;
      if (/^глава\s+\d/i.test(p)) return false;
      return true;
    });
  if (!para) return undefined;
  const cleaned = cleanMetaDescription(para, para.slice(0, 240), 240);
  return cleaned.length >= 60 ? cleaned : undefined;
}

function cleanStubLinks(body: string): string {
  let t = body;
  t = t.replace(/\[([^\]]+?)\]\(#\)/g, '$1');
  t = t.replace(/\[([^\]]+?)\]\(\s*\)/g, '$1');
  t = t.replace(/\n{3,}/g, '\n\n');
  return t;
}

function loadCharacterIndex(): Map<string, string> {
  /** name(lower) -> slug */
  const map = new Map<string, string>();
  if (!fs.existsSync(CHAR_DIR)) return map;
  for (const f of fs.readdirSync(CHAR_DIR)) {
    if (!f.endsWith('.md')) continue;
    const slug = f.replace(/\.md$/i, '');
    const raw = fs.readFileSync(path.join(CHAR_DIR, f), 'utf8');
    const sp = splitFm(raw);
    if (!sp) continue;
    const fm = parseFm(sp.fm);
    const name = String(fm.data.name ?? '').trim();
    if (!name) continue;
    map.set(name.toLowerCase(), slug);
  }
  return map;
}

function findRelatedCharacters(
  text: string,
  charIndex: Map<string, string>,
  limit: number,
): string[] {
  const lower = text.toLowerCase();
  const found = new Set<string>();
  for (const [name, slug] of charIndex.entries()) {
    if (found.size >= limit) break;
    // граница слова, чтобы «лиза» не поймала «лизать» — но имена в Genshin
    // обычно цельные. ограничение по символам перед/после.
    const re = new RegExp(`(^|[^а-яёa-z])${escapeRe(name)}([^а-яёa-z]|$)`, 'i');
    if (re.test(lower)) found.add(slug);
  }
  return [...found];
}

function escapeRe(s: string): string {
  return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

const CODES_SOURCES = [
  'https://genshin.hoyoverse.com/news',
  'https://www.hoyolab.com/circles/2/',
];

const PATCH_SOURCES = [
  'https://genshin.hoyoverse.com/news',
  'https://www.hoyolab.com/',
];

function todayIso(): string {
  return new Date().toISOString().slice(0, 10);
}

// ---------- Главный проход ----------
interface EnrichRecord {
  oldSlug: string;
  newSlug: string;
  changed: string[];
}

async function main() {
  if (!fs.existsSync(GUIDES_DIR)) {
    console.error('[enrich] нет каталога', GUIDES_DIR);
    process.exit(1);
  }
  fs.mkdirSync(REPORTS_DIR, { recursive: true });
  fs.mkdirSync(path.dirname(REDIRECTS_CONF), { recursive: true });

  const charIndex = loadCharacterIndex();
  const files = fs.readdirSync(GUIDES_DIR).filter((f) => f.endsWith('.md'));

  // Первый проход: соберём индекс slug -> {category, topic, gameVersion, title}
  interface GuideMeta {
    slug: string;
    title: string;
    category: GuideCategory;
    topic: string;
    gameVersion?: string;
    status?: string;
  }
  const metaList: GuideMeta[] = [];
  const slugToFile = new Map<string, string>();

  for (const f of files) {
    const raw = fs.readFileSync(path.join(GUIDES_DIR, f), 'utf8');
    const sp = splitFm(raw);
    if (!sp) continue;
    const fm = parseFm(sp.fm);
    const slug = f.replace(/\.md$/i, '');
    const cat = (String(fm.data.category ?? 'general') as GuideCategory);
    const topic = String(
      fm.data.topic ?? inferTopic(cat, '', f, sp.body.slice(0, 2000)),
    );
    const gv =
      typeof fm.data.gameVersion === 'string' && fm.data.gameVersion
        ? fm.data.gameVersion
        : extractGameVersion(slug, sp.body);
    const status =
      typeof fm.data.status === 'string'
        ? fm.data.status
        : inferStatus(cat, undefined, gv);
    metaList.push({
      slug,
      title: String(fm.data.title ?? slug),
      category: cat,
      topic,
      gameVersion: gv,
      status,
    });
    slugToFile.set(slug, f);
  }

  const records: EnrichRecord[] = [];
  const renames: Array<{ from: string; to: string }> = [];

  // Резервируем уже занятые slug'и для коллизий
  const reserved = new Set(metaList.map((m) => m.slug));

  for (const meta of metaList) {
    const oldFile = slugToFile.get(meta.slug)!;
    const oldPath = path.join(GUIDES_DIR, oldFile);
    const raw = fs.readFileSync(oldPath, 'utf8');
    const sp = splitFm(raw);
    if (!sp) continue;
    const fm = parseFm(sp.fm);
    const changed: string[] = [];

    // 1) Slug-нормализация
    let newSlug = meta.slug;
    const hasCyr = /[а-яёА-ЯЁ]/.test(meta.slug);
    const hasBadChars = /[^a-z0-9-]/.test(meta.slug);
    if (hasCyr || hasBadChars) {
      let candidate = slugifyFileBase(meta.slug);
      if (!candidate) candidate = `guide-${meta.slug.length}`;
      if (candidate !== meta.slug) {
        let n = 2;
        let unique = candidate;
        while (reserved.has(unique) && unique !== meta.slug) {
          unique = `${candidate}-${n++}`;
        }
        newSlug = unique;
        reserved.delete(meta.slug);
        reserved.add(newSlug);
        renames.push({ from: meta.slug, to: newSlug });
        changed.push('slug');
      }
    }

    // 2) Заполнение недостающих полей FM
    const cat = (String(fm.data.category ?? 'general') as GuideCategory);
    const bodyHint = sp.body.slice(0, 4000);

    if (!fm.data.topic) {
      fm.data.topic = inferTopic(cat, '', meta.slug, bodyHint);
      changed.push('topic');
    }
    if (!fm.data.gameVersion) {
      const gv = extractGameVersion(newSlug, sp.body);
      if (gv) {
        fm.data.gameVersion = gv;
        changed.push('gameVersion');
      }
    }
    if (!fm.data.audience) {
      fm.data.audience = inferAudience(cat, sp.body.slice(0, 2000));
      changed.push('audience');
    }
    let dateValue: Date | undefined;
    if (typeof fm.data.date === 'string' && fm.data.date) {
      const d = new Date(fm.data.date);
      if (!Number.isNaN(d.getTime())) dateValue = d;
    }
    if (!fm.data.status) {
      fm.data.status = inferStatus(
        cat,
        dateValue,
        typeof fm.data.gameVersion === 'string' ? fm.data.gameVersion : undefined,
      );
      changed.push('status');
    }
    const status = String(fm.data.status);

    if (!fm.data.updatedAt && fm.data.date) {
      fm.data.updatedAt = String(fm.data.date);
      changed.push('updatedAt');
    }
    if (!fm.data.reviewedAt && status !== 'historical') {
      fm.data.reviewedAt = todayIso();
      changed.push('reviewedAt');
    }

    // 3) Summary
    const currentSummary =
      typeof fm.data.summary === 'string' ? fm.data.summary : '';
    if (looksBadSummary(currentSummary)) {
      const derived = deriveSummary(sp.body);
      if (derived) {
        fm.data.summary = derived;
        changed.push('summary');
      }
    }

    // 4) Sources (для codes/patch)
    if (
      (cat === 'codes' || cat === 'patch') &&
      (!Array.isArray(fm.data.sources) || fm.data.sources.length === 0)
    ) {
      fm.data.sources = cat === 'codes' ? CODES_SOURCES : PATCH_SOURCES;
      changed.push('sources');
    }

    // 5) relatedCharacters
    if (
      !Array.isArray(fm.data.relatedCharacters) ||
      fm.data.relatedCharacters.length === 0
    ) {
      const found = findRelatedCharacters(
        `${String(fm.data.title ?? '')} ${sp.body}`,
        charIndex,
        5,
      );
      if (found.length > 0) {
        fm.data.relatedCharacters = found;
        changed.push('relatedCharacters');
      }
    }

    // 6) relatedGuides — top-5 по совпадению topic + ближайшая gameVersion
    if (
      !Array.isArray(fm.data.relatedGuides) ||
      fm.data.relatedGuides.length === 0
    ) {
      const myTopic = String(fm.data.topic ?? meta.topic);
      const myGv = (fm.data.gameVersion as string | undefined) ?? meta.gameVersion;
      const myGvNum = myGv ? parseFloat(myGv) : undefined;
      const candidates = metaList
        .filter((g) => g.slug !== meta.slug && g.topic === myTopic)
        .map((g) => {
          const gNum = g.gameVersion ? parseFloat(g.gameVersion) : undefined;
          let dist = 999;
          if (myGvNum !== undefined && gNum !== undefined) {
            dist = Math.abs(myGvNum - gNum);
          }
          // активные / dated впереди архивных
          const statusBoost = g.status === 'active' ? -0.1 : g.status === 'historical' ? 0.5 : 0;
          return { g, score: dist + statusBoost };
        })
        .sort((a, b) => a.score - b.score)
        .slice(0, 5)
        .map((x) => x.g.slug);
      if (candidates.length > 0) {
        // если slug будет переименован — мы используем новый slug ниже
        fm.data.relatedGuides = candidates;
        changed.push('relatedGuides');
      }
    }

    // 7) Тело: чистка stub-ссылок
    let newBody = cleanStubLinks(sp.body);
    if (newBody !== sp.body) changed.push('stubLinks');

    // 8) Запись (на новое имя файла, если slug переименован)
    if (changed.length > 0 || newSlug !== meta.slug) {
      const fmText = buildFm(fm.data, KEY_ORDER);
      const out = `${fmText}\n\n${newBody.replace(/^\n+/, '')}\n`;
      const newPath = path.join(GUIDES_DIR, `${newSlug}.md`);
      if (newPath !== oldPath) {
        fs.writeFileSync(newPath, out, 'utf8');
        fs.unlinkSync(oldPath);
      } else {
        fs.writeFileSync(oldPath, out, 'utf8');
      }
      records.push({ oldSlug: meta.slug, newSlug, changed });
    }
  }

  // 9) Перепиcать relatedGuides ссылающиеся на старые slug -> новые
  if (renames.length > 0) {
    const renameMap = new Map(renames.map((r) => [r.from, r.to]));
    const filesNow = fs.readdirSync(GUIDES_DIR).filter((f) => f.endsWith('.md'));
    for (const f of filesNow) {
      const fpath = path.join(GUIDES_DIR, f);
      const raw = fs.readFileSync(fpath, 'utf8');
      const sp = splitFm(raw);
      if (!sp) continue;
      const fm = parseFm(sp.fm);
      const list = fm.data.relatedGuides;
      if (Array.isArray(list)) {
        let dirty = false;
        const remapped = list.map((s) => {
          const r = renameMap.get(s);
          if (r) {
            dirty = true;
            return r;
          }
          return s;
        });
        if (dirty) {
          fm.data.relatedGuides = remapped;
          const fmText = buildFm(fm.data, KEY_ORDER);
          fs.writeFileSync(fpath, `${fmText}\n\n${sp.body.replace(/^\n+/, '')}\n`, 'utf8');
        }
      }
    }

    // characters: relatedGuides тоже исправить
    if (fs.existsSync(CHAR_DIR)) {
      for (const f of fs.readdirSync(CHAR_DIR)) {
        if (!f.endsWith('.md')) continue;
        const fpath = path.join(CHAR_DIR, f);
        const raw = fs.readFileSync(fpath, 'utf8');
        const sp = splitFm(raw);
        if (!sp) continue;
        const fm = parseFm(sp.fm);
        const list = fm.data.relatedGuides;
        if (Array.isArray(list)) {
          let dirty = false;
          const remapped = list.map((s) => {
            const r = renameMap.get(s);
            if (r) {
              dirty = true;
              return r;
            }
            return s;
          });
          if (dirty) {
            fm.data.relatedGuides = remapped;
            const fmText = buildFm(fm.data, [
              'name',
              'title',
              'element',
              'weapon',
              'rarity',
              'rating',
              'sourceSlug',
              'relatedWeapons',
              'relatedArtifacts',
              'relatedGuides',
            ]);
            fs.writeFileSync(fpath, `${fmText}\n\n${sp.body.replace(/^\n+/, '')}\n`, 'utf8');
          }
        }
      }
    }
  }

  // 10) Артефакты
  fs.writeFileSync(
    REDIRECTS_JSON,
    JSON.stringify(
      {
        generatedAt: new Date().toISOString(),
        total: renames.length,
        renames,
      },
      null,
      2,
    ),
    'utf8',
  );

  const confLines: string[] = [
    '# Сгенерировано scripts/enrich-guides.ts — не редактируйте вручную.',
    `# Total: ${renames.length} renames.`,
  ];
  for (const r of renames) {
    confLines.push(
      `rewrite ^/guides/${r.from}/?$ /guides/${r.to} permanent;`,
    );
  }
  fs.writeFileSync(REDIRECTS_CONF, `${confLines.join('\n')}\n`, 'utf8');

  console.log(
    `[enrich] обновлено ${records.length} файлов, переименовано ${renames.length} slug'ов`,
  );
  console.log(`[enrich] карта редиректов: ${path.relative(ROOT, REDIRECTS_CONF)}`);
}

main().catch((err) => {
  console.error('[enrich] ошибка:', err);
  process.exit(1);
});
