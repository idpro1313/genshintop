/**
 * Генератор OG-картинок 1200×630 PNG для страниц коллекций.
 *
 * Сканирует `src/content/{characters,guides}/*.md`, рендерит уникальный SVG-шаблон
 * и конвертирует в PNG через `sharp`. Идемпотентен: пропускает запись, если PNG
 * уже есть и его mtime новее MD.
 *
 * Запуск: `npm run og:generate`
 * Зависимости: `sharp` (devDependency, ставится `npm install`).
 */
import {
  existsSync,
  mkdirSync,
  readFileSync,
  readdirSync,
  statSync,
  writeFileSync,
} from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(fileURLToPath(import.meta.url), '..', '..');
const PUBLIC_DIR = join(ROOT, 'public');
const OG_DIR = join(PUBLIC_DIR, 'og');
const MANIFEST_PATH = join(ROOT, 'src', 'data', 'og-manifest.json');

interface Frontmatter {
  title?: string;
  name?: string;
  category?: string;
  element?: string;
  weapon?: string;
  rarity?: number;
  rating?: string;
  status?: string;
  topic?: string;
  gameVersion?: string;
}

const ELEMENT_RU: Record<string, string> = {
  Pyro: 'Пиро',
  Hydro: 'Гидро',
  Electro: 'Электро',
  Cryo: 'Крио',
  Anemo: 'Анемо',
  Geo: 'Гео',
  Dendro: 'Дендро',
};

const CATEGORY_RU: Record<string, string> = {
  banner: 'Баннер',
  patch: 'Патч',
  newbie: 'Новичкам',
  codes: 'Промокоды',
  tier: 'Тир-лист',
  hardware: 'Железо',
  general: 'Гайд',
};

function parseFrontmatter(raw: string): Frontmatter {
  if (!raw.startsWith('---')) return {};
  const end = raw.indexOf('\n---', 3);
  if (end === -1) return {};
  const yaml = raw.slice(3, end).trim();
  const fm: Record<string, unknown> = {};
  for (const line of yaml.split(/\r?\n/)) {
    const m = /^([A-Za-z][A-Za-z0-9_]*)\s*:\s*(.*)$/.exec(line);
    if (!m) continue;
    let val = m[2].trim();
    if (val === '' || val.startsWith('[')) continue;
    if (
      (val.startsWith('"') && val.endsWith('"')) ||
      (val.startsWith("'") && val.endsWith("'"))
    ) {
      val = val.slice(1, -1);
    }
    if (/^\d+$/.test(val)) fm[m[1]] = Number(val);
    else fm[m[1]] = val;
  }
  return fm as Frontmatter;
}

function escapeXml(s: string): string {
  return s
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

function wrapText(text: string, maxChars: number, maxLines: number): string[] {
  const words = text.replace(/\s+/g, ' ').trim().split(' ');
  const lines: string[] = [];
  let cur = '';
  for (const w of words) {
    const next = cur ? `${cur} ${w}` : w;
    if (next.length > maxChars) {
      if (cur) lines.push(cur);
      cur = w;
      if (lines.length === maxLines) break;
    } else {
      cur = next;
    }
  }
  if (cur && lines.length < maxLines) lines.push(cur);
  if (lines.length === maxLines) {
    const lastLine = lines[maxLines - 1];
    const used = lines.join(' ').length;
    if (used < text.length - 1) {
      lines[maxLines - 1] = `${lastLine.replace(/\s*\S{1,3}$/, '')}…`;
    }
  }
  return lines;
}

function buildSvg(opts: {
  title: string;
  badge: string;
  meta: string;
  accent: 'gold' | 'mint';
}): string {
  const lines = wrapText(opts.title, 28, 4);
  const fontSize = lines.length >= 4 ? 56 : lines.length === 3 ? 64 : 72;
  const lineHeight = Math.round(fontSize * 1.18);
  const blockHeight = lineHeight * lines.length;
  const startY = Math.round(315 - blockHeight / 2 + fontSize * 0.85);
  const accentGrad = opts.accent === 'gold' ? 'gold' : 'mint';
  const badgeWidth = Math.min(opts.badge.length * 16 + 40, 480);
  return `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0a0a12"/>
      <stop offset="55%" stop-color="#1a1a2e"/>
      <stop offset="100%" stop-color="#0c4a6e"/>
    </linearGradient>
    <linearGradient id="gold" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#e8b445"/>
      <stop offset="100%" stop-color="#fde68a"/>
    </linearGradient>
    <linearGradient id="mint" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#4ecdc4"/>
      <stop offset="100%" stop-color="#a7f3d0"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="630" fill="url(#bg)"/>
  <circle cx="180" cy="120" r="220" fill="#e8b445" opacity="0.10"/>
  <circle cx="1040" cy="520" r="260" fill="#4ecdc4" opacity="0.08"/>
  <rect x="40" y="40" width="1120" height="550" rx="32" fill="none" stroke="#ffffff22" stroke-width="2"/>
  <text x="80" y="118" font-family="Inter, Segoe UI, Helvetica, Arial, sans-serif" font-size="32" font-weight="700" fill="url(#gold)">GenshinTop</text>
  <text x="1120" y="118" text-anchor="end" font-family="Inter, Segoe UI, Helvetica, Arial, sans-serif" font-size="22" fill="#cbd5e1">genshintop.ru</text>
  <rect x="80" y="160" width="${badgeWidth}" height="48" rx="24" fill="url(#${accentGrad})"/>
  <text x="${80 + 24}" y="194" font-family="Inter, Segoe UI, Helvetica, Arial, sans-serif" font-size="24" font-weight="700" fill="#0a0a12">${escapeXml(opts.badge)}</text>
${lines
  .map(
    (l, i) =>
      `  <text x="80" y="${startY + i * lineHeight}" font-family="Inter, Segoe UI, Helvetica, Arial, sans-serif" font-size="${fontSize}" font-weight="700" fill="#ffffff">${escapeXml(l)}</text>`,
  )
  .join('\n')}
  <text x="80" y="560" font-family="Inter, Segoe UI, Helvetica, Arial, sans-serif" font-size="26" fill="#94a3b8">${escapeXml(opts.meta)}</text>
</svg>`;
}

interface RenderTask {
  collection: 'guides' | 'characters';
  slug: string;
  source: string;
  output: string;
  svg: string;
}

function buildGuideTask(slug: string, fm: Frontmatter, source: string): RenderTask {
  const cat = fm.category ?? 'general';
  const badge = CATEGORY_RU[cat] ?? 'Гайд';
  const title = (fm.title ?? slug).slice(0, 140);
  const versionPart = fm.gameVersion ? `v${fm.gameVersion} · ` : '';
  const meta = `${versionPart}genshintop.ru/guides/${slug}`.slice(0, 90);
  const svg = buildSvg({ title, badge, meta, accent: 'mint' });
  return {
    collection: 'guides',
    slug,
    source,
    output: join(OG_DIR, 'guides', `${slug}.png`),
    svg,
  };
}

function buildCharacterTask(slug: string, fm: Frontmatter, source: string): RenderTask {
  const elem = fm.element ? ELEMENT_RU[fm.element] ?? fm.element : '';
  const weapon = fm.weapon ?? '';
  const rarity = fm.rarity ? '★'.repeat(fm.rarity) : '';
  const badge = [elem, weapon, rarity].filter(Boolean).join(' · ').slice(0, 56);
  const title = (fm.name ?? slug).slice(0, 80);
  const meta = `genshintop.ru/characters/${slug}`.slice(0, 90);
  const svg = buildSvg({ title, badge: badge || 'Персонаж', meta, accent: 'gold' });
  return {
    collection: 'characters',
    slug,
    source,
    output: join(OG_DIR, 'characters', `${slug}.png`),
    svg,
  };
}

function listMd(dir: string): string[] {
  if (!existsSync(dir)) return [];
  return readdirSync(dir)
    .filter((f) => f.endsWith('.md'))
    .map((f) => join(dir, f));
}

async function main() {
  let sharp: typeof import('sharp');
  try {
    sharp = (await import('sharp')).default as unknown as typeof import('sharp');
  } catch {
    console.error(
      '[og:generate] Не найден пакет `sharp`. Установите его: npm i -D sharp',
    );
    process.exit(1);
    return;
  }

  const tasks: RenderTask[] = [];

  for (const file of listMd(join(ROOT, 'src', 'content', 'characters'))) {
    const slug = file.replace(/\\/g, '/').split('/').pop()!.replace(/\.md$/, '');
    const raw = readFileSync(file, 'utf8');
    const fm = parseFrontmatter(raw);
    tasks.push(buildCharacterTask(slug, fm, file));
  }

  for (const file of listMd(join(ROOT, 'src', 'content', 'guides'))) {
    const slug = file.replace(/\\/g, '/').split('/').pop()!.replace(/\.md$/, '');
    const raw = readFileSync(file, 'utf8');
    const fm = parseFrontmatter(raw);
    tasks.push(buildGuideTask(slug, fm, file));
  }

  let generated = 0;
  let skipped = 0;
  const entries: string[] = [];
  for (const t of tasks) {
    const outDir = dirname(t.output);
    if (!existsSync(outDir)) mkdirSync(outDir, { recursive: true });
    const sourceMtime = statSync(t.source).mtimeMs;
    const exists = existsSync(t.output);
    const targetMtime = exists ? statSync(t.output).mtimeMs : 0;
    if (exists && targetMtime > sourceMtime) {
      skipped++;
      entries.push(`${t.collection}/${t.slug}`);
      continue;
    }
    await sharp(Buffer.from(t.svg))
      .resize(1200, 630)
      .png({ compressionLevel: 9 })
      .toFile(t.output);
    generated++;
    entries.push(`${t.collection}/${t.slug}`);
    if (generated % 50 === 0) {
      console.log(`[og:generate] ${generated} сгенерировано, ${skipped} пропущено`);
    }
  }

  // Дефолтный OG: рендерим из существующего og-default.svg в og-default.png рядом.
  const defaultSvgPath = join(PUBLIC_DIR, 'og-default.svg');
  const defaultPngPath = join(PUBLIC_DIR, 'og-default.png');
  if (existsSync(defaultSvgPath)) {
    await sharp(readFileSync(defaultSvgPath))
      .resize(1200, 630)
      .png({ compressionLevel: 9 })
      .toFile(defaultPngPath);
  }

  entries.sort();
  writeFileSync(
    MANIFEST_PATH,
    JSON.stringify(
      {
        generatedAt: new Date().toISOString(),
        entries,
      },
      null,
      2,
    ) + '\n',
    'utf8',
  );

  console.log(
    `[og:generate] готово: ${generated} новых, ${skipped} актуальных, всего ${entries.length}`,
  );
}

main().catch((err) => {
  console.error('[og:generate] ошибка:', err);
  process.exit(1);
});
