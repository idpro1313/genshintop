/**
 * Аудит gi-database: реестр файлов, дубли по базовому имени, счётчики по категориям.
 * Запуск: npm run content:audit
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const GI = path.join(ROOT, 'gi-database');

type CategoryKey =
  | '01_characters'
  | '02_weapons'
  | '03_artifacts'
  | '04_locations'
  | '05_quests'
  | '06_guides'
  | '07_items'
  | '08_misc';

const CATEGORIES: CategoryKey[] = [
  '01_characters',
  '02_weapons',
  '03_artifacts',
  '04_locations',
  '05_quests',
  '06_guides',
  '07_items',
  '08_misc',
];

function walkMd(dir: string): string[] {
  if (!fs.existsSync(dir)) return [];
  const out: string[] = [];
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  for (const e of entries) {
    const full = path.join(dir, e.name);
    if (e.isDirectory()) out.push(...walkMd(full));
    else if (e.isFile() && e.name.toLowerCase().endsWith('.md')) out.push(full);
  }
  return out;
}

function basenameKey(file: string): string {
  return path.basename(file).toLowerCase();
}

function main() {
  if (!fs.existsSync(GI)) {
    console.error('gi-database не найден:', GI);
    process.exit(1);
  }

  const byCategory: Record<string, string[]> = {};
  for (const cat of CATEGORIES) {
    byCategory[cat] = walkMd(path.join(GI, cat));
  }

  const indexPath = path.join(GI, 'INDEX.md');
  if (fs.existsSync(indexPath)) {
    byCategory['INDEX'] = [indexPath];
  }

  const allFiles = Object.values(byCategory).flat();
  const keyToPaths = new Map<string, string[]>();

  for (const f of allFiles) {
    const k = basenameKey(f);
    if (!keyToPaths.has(k)) keyToPaths.set(k, []);
    keyToPaths.get(k)!.push(f);
  }

  const duplicates = [...keyToPaths.entries()].filter(([, paths]) => paths.length > 1);

  /** Пересечения имён между персонажами и предметами (типичные дубли контента) */
  const charBasenames = new Set(
    byCategory['01_characters'].map((p) => basenameKey(p)),
  );
  const itemBasenames = new Set(
    byCategory['07_items'].map((p) => basenameKey(p)),
  );
  const charItemOverlap = [...charBasenames].filter((b) => itemBasenames.has(b));

  const report = {
    generatedAt: new Date().toISOString(),
    root: GI,
    totals: {
      allMd: allFiles.length,
      byCategory: Object.fromEntries(
        Object.entries(byCategory).map(([k, v]) => [k, v.length]),
      ),
    },
    duplicateBasenames: duplicates.map(([name, paths]) => ({
      basename: name,
      paths: paths.map((p) => path.relative(ROOT, p)),
    })),
    charactersItemsOverlap: {
      count: charItemOverlap.length,
      samples: charItemOverlap.slice(0, 50),
    },
  };

  const outDir = path.join(ROOT, 'reports');
  fs.mkdirSync(outDir, { recursive: true });
  const outFile = path.join(outDir, 'content-audit.json');
  fs.writeFileSync(outFile, JSON.stringify(report, null, 2), 'utf8');

  console.log('Аудит завершён:', path.relative(ROOT, outFile));
  console.log('Всего .md:', report.totals.allMd);
  console.log('Дублей по имени файла:', duplicates.length);
  console.log('Пересечение 01_characters / 07_items (имена файлов):', charItemOverlap.length);
}

main();
