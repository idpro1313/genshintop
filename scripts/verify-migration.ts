/**
 * Сверка: сколько .md в gi-database (MVP-категории) vs сколько в content/ после миграции.
 * Запуск: npx tsx scripts/verify-migration.ts
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');

function countMd(dir: string): number {
  if (!fs.existsSync(dir)) return -1;
  let n = 0;
  const walk = (d: string) => {
    for (const e of fs.readdirSync(d, { withFileTypes: true })) {
      const p = path.join(d, e.name);
      if (e.isDirectory()) walk(p);
      else if (e.name.toLowerCase().endsWith('.md')) n++;
    }
  };
  walk(dir);
  return n;
}

function main() {
  const giChars = countMd(path.join(ROOT, 'gi-database', '01_characters'));
  const giGuides = countMd(path.join(ROOT, 'gi-database', '06_guides'));
  const outChars = countMd(path.join(ROOT, 'content', 'characters'));
  const outGuides = countMd(path.join(ROOT, 'content', 'guides'));

  const repPath = path.join(ROOT, 'reports', 'migration-report.json');
  let migrated = { characters: 0, guides: 0 };
  if (fs.existsSync(repPath)) {
    try {
      const j = JSON.parse(fs.readFileSync(repPath, 'utf8')) as {
        characters?: { ok?: number };
        guides?: { ok?: number };
      };
      migrated = {
        characters: j.characters?.ok ?? 0,
        guides: j.guides?.ok ?? 0,
      };
    } catch {
      /* ignore */
    }
  }

  console.log('--- Верификация переноса (MVP) ---');
  console.log('gi-database/01_characters:', giChars);
  console.log('gi-database/06_guides (включая подпапки):', giGuides);
  console.log('content/characters:', outChars);
  console.log('content/guides:', outGuides);
  console.log('migration-report.json ok:', migrated);

  const charOk = giChars >= 0 && outChars === giChars && migrated.characters === outChars;
  const guideOk = giGuides >= 0 && outGuides === giGuides && migrated.guides === outGuides;

  if (charOk && guideOk) {
    console.log('Результат: совпадение количеств и отчёта — gi-database можно удалять после ручной выборочной проверки качества.');
    process.exit(0);
  }

  console.log(
    'Результат: расхождения или нет migration-report.json. Выполните npm run content:migrate и повторите.',
  );
  process.exit(1);
}

main();
