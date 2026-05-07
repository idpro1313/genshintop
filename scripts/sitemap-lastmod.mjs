/**
 * Карта полного URL → lastmod (Date) для коллекций Markdown при сборке sitemap.
 * Читает frontmatter без зависимости от Astro runtime.
 */
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, '..');

/**
 * @param {string} siteHost без завершающего слэша, напр. https://genshintop.ru
 * @returns {Record<string, Date>}
 */
export function buildSitemapLastmodByUrl(siteHost) {
  const base = siteHost.replace(/\/+$/, '');
  /** @type {Record<string, Date>} */
  const out = {};

  for (const dir of ['guides', 'characters']) {
    const contentDir = path.join(ROOT, 'src', 'content', dir);
    if (!fs.existsSync(contentDir)) continue;
    for (const file of walkMdFiles(contentDir)) {
      const raw = fs.readFileSync(file, 'utf8');
      const fm = extractFrontmatter(raw);
      if (!fm) continue;

      const sourceSlug = getYamlString(fm, 'sourceSlug');
      if (sourceSlug && sourceSlug.startsWith('_placeholder')) continue;

      const d = maxContentDates(fm) ?? fs.statSync(file).mtime;

      const rel = path.relative(contentDir, file).replace(/\\/g, '/').replace(/\.md$/i, '');
      const url = `${base}/${dir}/${rel}`;
      out[normalizeCanonicalUrl(url)] = d;
    }
  }

  return out;
}

/**
 * @param {string} url
 * @returns {string}
 */
function normalizeCanonicalUrl(url) {
  try {
    const u = new URL(url);
    let p = u.pathname;
    if (p.endsWith('/') && p.length > 1) p = p.slice(0, -1);
    return `${u.origin}${p}`;
  } catch {
    return url.replace(/\/+$/, '') || url;
  }
}

/**
 * @param {string} dir
 * @returns {string[]}
 */
function walkMdFiles(dir) {
  /** @type {string[]} */
  const acc = [];
  for (const ent of fs.readdirSync(dir, { withFileTypes: true })) {
    const p = path.join(dir, ent.name);
    if (ent.isDirectory()) acc.push(...walkMdFiles(p));
    else if (ent.name.endsWith('.md')) acc.push(p);
  }
  return acc;
}

/**
 * @param {string} raw
 * @returns {string | null}
 */
function extractFrontmatter(raw) {
  const m = raw.match(/^---\r?\n([\s\S]*?)\r?\n??---/);
  return m ? m[1] : null;
}

/**
 * @param {string} fm
 * @param {string} key
 * @returns {string | undefined}
 */
function getYamlString(fm, key) {
  const re = new RegExp(`^${key}:\\s*(.*)$`, 'm');
  const m = fm.match(re);
  if (!m) return undefined;
  let v = m[1].trim();
  if ((v.startsWith('"') && v.endsWith('"')) || (v.startsWith("'") && v.endsWith("'"))) {
    v = v.slice(1, -1);
  }
  return v || undefined;
}

/**
 * @param {string} lineValue
 * @returns {Date | null}
 */
function parseYamlDateValue(lineValue) {
  const v = lineValue.trim();
  if (!v || v === 'null') return null;
  const unquoted = v.replace(/^["']|["']$/g, '');
  const d = new Date(unquoted);
  return Number.isNaN(d.getTime()) ? null : d;
}

/**
 * @param {string} fm
 * @returns {Date | null}
 */
function maxContentDates(fm) {
  /** @type {Date[]} */
  const dates = [];
  for (const key of ['date', 'updatedAt', 'reviewedAt']) {
    const re = new RegExp(`^${key}:\\s*(.+)$`, 'm');
    const m = fm.match(re);
    if (!m) continue;
    const d = parseYamlDateValue(m[1]);
    if (d) dates.push(d);
  }
  if (dates.length === 0) return null;
  return new Date(Math.max(...dates.map((x) => x.getTime())));
}

/**
 * @param {string} itemUrl
 * @param {Record<string, Date>} map
 * @returns {Date | undefined}
 */
export function lastmodForSitemapItem(itemUrl, map) {
  const n = normalizeCanonicalUrl(itemUrl);
  return map[n] ?? map[`${n}/`];
}
