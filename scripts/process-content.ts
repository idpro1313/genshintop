/**
 * Перенос gi-database -> src/content (персонажи + гайды), очистка, frontmatter, отчёт.
 * Запуск: npm run content:migrate
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const GI = path.join(ROOT, 'gi-database');
const OUT_CHAR = path.join(ROOT, 'src', 'content', 'characters');
const OUT_GUIDES = path.join(ROOT, 'src', 'content', 'guides');

const ELEMENT_RU_TO_EN: Record<string, string> = {
  Пиро: 'Pyro',
  Гидро: 'Hydro',
  Электро: 'Electro',
  Крио: 'Cryo',
  Анемо: 'Anemo',
  Гео: 'Geo',
  Дендро: 'Dendro',
};

const WEAPON_RU: Record<string, string> = {
  Меч: 'Одноручное',
  'Одноручный меч': 'Одноручное',
  Одноручный: 'Одноручное',
  'Двуручный меч': 'Двуручное',
  Клеймор: 'Двуручное',
  Копьё: 'Древковое',
  Копье: 'Древковое',
  Древковое: 'Древковое',
  Катализатор: 'Катализатор',
  Лук: 'Лук',
};

type ElementEn =
  | 'Pyro'
  | 'Hydro'
  | 'Electro'
  | 'Cryo'
  | 'Anemo'
  | 'Geo'
  | 'Dendro';
type Weapon =
  | 'Одноручное'
  | 'Двуручное'
  | 'Древковое'
  | 'Катализатор'
  | 'Лук'
  | 'Прочее';

function walkMd(dir: string): string[] {
  if (!fs.existsSync(dir)) return [];
  const out: string[] = [];
  for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) out.push(...walkMd(full));
    else if (e.isFile() && e.name.toLowerCase().endsWith('.md')) out.push(full);
  }
  return out;
}

function readFirstHeading(raw: string): string | null {
  const m = raw.match(/^#\s+(.+)$/m);
  return m ? m[1].trim() : null;
}

function extractAfterLabel(body: string, label: string): string | null {
  const re = new RegExp(
    `${label}\\s*:?\\s*\\n+([^\\n#][^\\n]*)`,
    'im',
  );
  const m = body.match(re);
  return m ? m[1].trim() : null;
}

function parseElement(body: string): ElementEn {
  const line =
    extractAfterLabel(body, 'Глаз бога') ||
    extractAfterLabel(body, 'Элемент') ||
    '';
  const ru = line.split(/[\s,]/)[0]?.trim() ?? '';
  const en = ELEMENT_RU_TO_EN[ru];
  if (en && isElement(en)) return en;
  return 'Anemo';
}

function isElement(s: string): s is ElementEn {
  return [
    'Pyro',
    'Hydro',
    'Electro',
    'Cryo',
    'Anemo',
    'Geo',
    'Dendro',
  ].includes(s);
}

function parseWeapon(body: string): Weapon {
  const line = extractAfterLabel(body, 'Оружие') || '';
  const key = line.split(/[\s,]/)[0]?.trim() ?? '';
  for (const [ru, w] of Object.entries(WEAPON_RU)) {
    if (line.includes(ru) || key === ru) return w as Weapon;
  }
  if (line.toLowerCase().includes('копь')) return 'Древковое';
  if (line.toLowerCase().includes('клеймор')) return 'Двуручное';
  return 'Прочее';
}

function parseRarity(body: string): 4 | 5 | undefined {
  if (/★★★★★|5\s*зв(ё|е)зд/i.test(body)) return 5;
  if (/★★★★(?![★])|4\s*зв(ё|е)зд/i.test(body)) return 4;
  const after = extractAfterLabel(body, 'Редкость');
  if (after && /5/.test(after)) return 5;
  if (after && /4/.test(after)) return 4;
  return undefined;
}

function parseRating(body: string): string | undefined {
  const m = body.match(/Общий рейтинг\s*\n+\s*([A-Z][+-]?|S\+|SS)/im);
  return m ? m[1].trim() : undefined;
}

function cleanCharacterMarkdown(raw: string, name: string): string {
  let t = raw;
  // Убрать типичные крошки и мусорные короткие строки после заголовка
  const lines = t.split(/\r?\n/);
  const filtered: string[] = [];
  const crumb = new Set([
    'Главная',
    'Вики',
    'Персонажи',
    'Оружие',
    'Артефакты',
    'Предметы',
    name,
  ]);
  for (const line of lines) {
    const s = line.trim();
    if (s === name && filtered.length < 25) continue;
    if (crumb.has(s) && s.length < 40) continue;
    filtered.push(line);
  }
  t = filtered.join('\n');
  t = t.replace(/\]\(\.\.\/[^)]+\)/g, '](#)');
  t = t.replace(/\]\([^)]*index\.html[^)]*\)/gi, '](#)');
  t = t.replace(/\n{3,}/g, '\n\n');
  return t.trim();
}

function cleanGuideMarkdown(raw: string): string {
  let t = raw.replace(/\]\(\.\.\/[^)]+\)/g, '](#)');
  t = t.replace(/\]\([^)]*index\.html[^)]*\)/gi, '](#)');
  t = t.replace(/\n{3,}/g, '\n\n');
  return t.trim();
}

function slugifyFileBase(base: string): string {
  return base
    .replace(/\.md$/i, '')
    .replace(/#/g, '')
    .replace(/\s+/g, '-')
    .replace(/[^\p{L}\p{N}_-]+/gu, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '')
    .slice(0, 160);
}

type GuideCategory =
  | 'banner'
  | 'patch'
  | 'newbie'
  | 'codes'
  | 'tier'
  | 'hardware'
  | 'general';

function classifyGuide(relPath: string, base: string): GuideCategory {
  const lower = (relPath + base).toLowerCase();
  if (lower.includes('progression') || lower.includes('новичк')) return 'newbie';
  if (lower.includes('banner') || lower.includes('баннер')) return 'banner';
  if (lower.includes('promo') || lower.includes('промокод')) return 'codes';
  if (lower.startsWith('update-') || lower.includes('обновлен')) return 'patch';
  if (lower.includes('тир') || lower.includes('tier')) return 'tier';
  if (lower.includes('pk-') || lower.includes('пк ') || lower.includes('pc'))
    return 'hardware';
  return 'general';
}

function extractDateFromFilename(base: string): Date | undefined {
  const m = base.match(/(\d{2})-(\d{2})-(\d{4})\s*$/);
  if (!m) return undefined;
  const [, dd, mm, yyyy] = m;
  const d = new Date(Number(yyyy), Number(mm) - 1, Number(dd));
  return Number.isNaN(d.getTime()) ? undefined : d;
}

function guideTitle(raw: string, base: string): string {
  const h = readFirstHeading(raw);
  if (h) return h;
  return base.replace(/\.md$/i, '').replace(/-/g, ' ');
}

function guideSummary(raw: string): string | undefined {
  const body = raw.replace(/^#[^\n]+\n+/, '');
  const para = body
    .split(/\n\n+/)
    .map((p) => p.replace(/\s+/g, ' ').trim())
    .find((p) => p.length > 80);
  return para ? para.slice(0, 280) : undefined;
}

function findRelatedInFolder(
  name: string,
  folder: string,
  limit: number,
): string[] {
  const n = name.toLowerCase();
  const out: string[] = [];
  for (const file of walkMd(folder)) {
    if (out.length >= limit) break;
    try {
      const body = fs.readFileSync(file, 'utf8').toLowerCase();
      if (body.includes(n)) out.push(path.basename(file, '.md'));
    } catch {
      /* skip */
    }
  }
  return out;
}

function findRelatedGuides(charName: string, guideFiles: string[]): string[] {
  const n = charName.toLowerCase();
  const out: string[] = [];
  for (const f of guideFiles) {
    if (out.length >= 8) break;
    const base = path.basename(f, '.md');
    const raw = fs.readFileSync(f, 'utf8');
    if (raw.toLowerCase().includes(n)) out.push(slugifyFileBase(base));
  }
  return [...new Set(out)];
}

function yamlEscape(s: string): string {
  if (/[:#\n"']/.test(s)) return JSON.stringify(s);
  return s;
}

function frontmatterBlock(obj: Record<string, unknown>): string {
  const lines = ['---'];
  for (const [k, v] of Object.entries(obj)) {
    if (v === undefined) continue;
    if (Array.isArray(v)) {
      lines.push(`${k}:`);
      for (const item of v) lines.push(`  - ${yamlEscape(String(item))}`);
    } else if (v instanceof Date) {
      lines.push(`${k}: ${v.toISOString().slice(0, 10)}`);
    } else if (typeof v === 'number' || typeof v === 'boolean') {
      lines.push(`${k}: ${v}`);
    } else {
      lines.push(`${k}: ${yamlEscape(String(v))}`);
    }
  }
  lines.push('---');
  return lines.join('\n');
}

function main() {
  if (!fs.existsSync(GI)) {
    console.error('gi-database не найден:', GI);
    process.exit(1);
  }

  fs.mkdirSync(OUT_CHAR, { recursive: true });
  fs.mkdirSync(OUT_GUIDES, { recursive: true });

  for (const f of fs.readdirSync(OUT_CHAR)) {
    if (f.endsWith('.md')) fs.unlinkSync(path.join(OUT_CHAR, f));
  }
  for (const f of fs.readdirSync(OUT_GUIDES)) {
    if (f.endsWith('.md')) fs.unlinkSync(path.join(OUT_GUIDES, f));
  }

  const guideFilesAll = walkMd(path.join(GI, '06_guides'));

  const report = {
    generatedAt: new Date().toISOString(),
    characters: { ok: 0, skipped: [] as string[], errors: [] as string[] },
    guides: { ok: 0, skipped: [] as string[], errors: [] as string[] },
  };

  const usedCharSlugs = new Set<string>();
  const charPaths = walkMd(path.join(GI, '01_characters'));
  for (const file of charPaths) {
    const base = path.basename(file);
    const sourceSlug = path.basename(file, '.md');
    try {
      const raw = fs.readFileSync(file, 'utf8');
      const name = readFirstHeading(raw) || sourceSlug;
      const element = parseElement(raw);
      const weapon = parseWeapon(raw);
      const rarity = parseRarity(raw);
      const rating = parseRating(raw);
      const body = cleanCharacterMarkdown(raw, name);
      let slug = slugifyFileBase(base);
      if (!slug) {
        report.characters.skipped.push(base);
        continue;
      }
      let n = 2;
      while (usedCharSlugs.has(slug)) {
        slug = `${slugifyFileBase(base)}-${n++}`;
      }
      usedCharSlugs.add(slug);
      const relatedWeapons = findRelatedInFolder(
        name,
        path.join(GI, '02_weapons'),
        12,
      );
      const relatedArtifacts = findRelatedInFolder(
        name,
        path.join(GI, '03_artifacts'),
        12,
      );
      const relatedGuides = findRelatedGuides(name, guideFilesAll);

      const fm = frontmatterBlock({
        name,
        title: `${name} — гайд и билд Genshin Impact`,
        element,
        weapon,
        ...(rarity !== undefined ? { rarity } : {}),
        ...(rating ? { rating } : {}),
        sourceSlug,
        ...(relatedWeapons.length ? { relatedWeapons } : {}),
        ...(relatedArtifacts.length ? { relatedArtifacts } : {}),
        ...(relatedGuides.length ? { relatedGuides } : {}),
      });
      fs.writeFileSync(path.join(OUT_CHAR, `${slug}.md`), `${fm}\n\n${body}\n`, 'utf8');
      report.characters.ok++;
    } catch (e) {
      report.characters.errors.push(`${base}: ${String(e)}`);
    }
  }

  const usedGuideSlugs = new Set<string>();
  for (const file of guideFilesAll) {
    const base = path.basename(file);
    const rel = path.relative(path.join(GI, '06_guides'), file);
    const sourceSlug = path.basename(file, '.md');
    try {
      const raw = fs.readFileSync(file, 'utf8');
      let slug = slugifyFileBase(base);
      if (!slug) {
        report.guides.skipped.push(base);
        continue;
      }
      let n = 2;
      while (usedGuideSlugs.has(slug)) {
        slug = `${slugifyFileBase(base)}-${n++}`;
      }
      usedGuideSlugs.add(slug);
      const title = guideTitle(raw, base);
      const category = classifyGuide(rel, base);
      const date =
        extractDateFromFilename(base) ?? extractDateFromFilename(slug);
      const summary = guideSummary(raw);
      const body = cleanGuideMarkdown(raw);
      const fm = frontmatterBlock({
        title,
        category,
        ...(date ? { date } : {}),
        ...(summary ? { summary } : {}),
        sourceSlug,
        sourcePath: rel.replace(/\\/g, '/'),
      });
      fs.writeFileSync(path.join(OUT_GUIDES, `${slug}.md`), `${fm}\n\n${body}\n`, 'utf8');
      report.guides.ok++;
    } catch (e) {
      report.guides.errors.push(`${base}: ${String(e)}`);
    }
  }

  const outDir = path.join(ROOT, 'reports');
  fs.mkdirSync(outDir, { recursive: true });
  fs.writeFileSync(
    path.join(outDir, 'migration-report.json'),
    JSON.stringify(report, null, 2),
    'utf8',
  );

  console.log('Миграция завершена.');
  console.log('Персонажи:', report.characters.ok, 'гайды:', report.guides.ok);
  console.log('Отчёт:', path.relative(ROOT, path.join(outDir, 'migration-report.json')));
}

main();
