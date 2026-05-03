/**
 * GRACE[M-CONTENT-PIPELINE][audit-guides-content][BLOCK_AUDIT]
 * PURPOSE: Статический аудит коллекции гайдов без запуска Astro — метрики качества и риски для редакции.
 * Запуск: npm run content:audit-guides
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import type { GuideCategory } from '../src/lib/guide-taxonomy';
import {
  extractGameVersion,
  inferAudience,
  inferStatus,
  inferTopic,
  topicFromCategory,
} from '../src/lib/guide-taxonomy';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.join(__dirname, '..');
const GUIDES = path.join(ROOT, 'src', 'content', 'guides');

function parseFmKeys(fm: string): Record<string, string> {
  const out: Record<string, string> = {};
  for (const line of fm.split(/\r?\n/)) {
    const m = line.match(/^([a-zA-Z0-9_]+):\s*(.*)$/);
    if (!m) continue;
    let v = m[2].trim();
    if ((v.startsWith('"') && v.endsWith('"')) || (v.startsWith("'") && v.endsWith("'")))
      v = v.slice(1, -1);
    out[m[1]] = v;
  }
  return out;
}

function splitGuide(raw: string): { fm: string; body: string } | null {
  const m = raw.match(/^---\r?\n([\s\S]*?)\r?\n---\r?\n([\s\S]*)$/);
  if (!m) return null;
  return { fm: m[1], body: m[2] };
}

function main() {
  if (!fs.existsSync(GUIDES)) {
    console.error('Каталог гайдов не найден:', GUIDES);
    process.exit(1);
  }

  const files = fs.readdirSync(GUIDES).filter((f) => f.endsWith('.md'));
  const rows: Array<{
    file: string;
    category: string;
    topicInFm?: string;
    topicEffective: string;
    hasDate: boolean;
    stubLinks: number;
    cyrillicSlug: boolean;
    summaryStartsToc: boolean;
    summaryStartsBanner: boolean;
    title: string;
  }> = [];

  const titleGroups = new Map<string, string[]>();
  let totalStub = 0;
  let withDate = 0;
  let topicInFm = 0;
  let statusInFm = 0;

  for (const file of files) {
    const full = path.join(GUIDES, file);
    const raw = fs.readFileSync(full, 'utf8');
    const sp = splitGuide(raw);
    if (!sp) continue;
    const kv = parseFmKeys(sp.fm);
    const category = (kv.category ?? 'general') as GuideCategory;
    const topicEff =
      kv.topic && kv.topic.length
        ? kv.topic
        : inferTopic(category, '', file, sp.body).toString();
    const stub = (sp.body.match(/\]\(#\)/g) ?? []).length;
    totalStub += stub;
    if (kv.date) withDate++;
    if (kv.topic) topicInFm++;
    if (kv.status) statusInFm++;

    const title = kv.title ?? '';
    const arr = titleGroups.get(title) ?? [];
    arr.push(file);
    titleGroups.set(title, arr);

    rows.push({
      file,
      category,
      topicInFm: kv.topic,
      topicEffective: topicEff,
      hasDate: Boolean(kv.date),
      stubLinks: stub,
      cyrillicSlug: /[а-яёА-ЯЁ]/i.test(file),
      summaryStartsToc: Boolean(kv.summary?.startsWith('"Содержание') || kv.summary?.startsWith('Содержание')),
      summaryStartsBanner: Boolean(kv.summary?.includes('Молитва события')),
      title,
    });
  }

  const dupTitles = [...titleGroups.entries()].filter(([, a]) => a.length > 1);
  const byCategory: Record<string, number> = {};
  const byTopic: Record<string, number> = {};
  for (const r of rows) {
    byCategory[r.category] = (byCategory[r.category] ?? 0) + 1;
    byTopic[r.topicEffective] = (byTopic[r.topicEffective] ?? 0) + 1;
  }

  const topStub = [...rows].sort((a, b) => b.stubLinks - a.stubLinks).slice(0, 15);

  const heuristicsSample = rows.slice(0, 8).map((r) => {
    const raw = fs.readFileSync(path.join(GUIDES, r.file), 'utf8');
    const sp = splitGuide(raw);
    const body = sp?.body ?? '';
    const cat = r.category as GuideCategory;
    const slug = r.file.replace(/\.md$/i, '');
    const gv = extractGameVersion(slug, body);
    return {
      file: r.file,
      gameVersion: gv,
      inferredAudience: inferAudience(cat, body),
      inferredStatus: inferStatus(cat, undefined, gv),
      topicFromCategoryOnly: topicFromCategory(cat),
    };
  });

  const report = {
    generatedAt: new Date().toISOString(),
    guidesDir: path.relative(ROOT, GUIDES),
    totals: {
      files: rows.length,
      withDate,
      withoutDate: rows.length - withDate,
      topicInFrontmatter: topicInFm,
      statusInFrontmatter: statusInFm,
      stubLinkTotal: totalStub,
      filesWithStubLinks: rows.filter((r) => r.stubLinks > 0).length,
      cyrillicSlugFiles: rows.filter((r) => r.cyrillicSlug).length,
      duplicateTitleGroups: dupTitles.length,
      summariesStartingToc: rows.filter((r) => r.summaryStartsToc).length,
      summariesStartingBanner: rows.filter((r) => r.summaryStartsBanner).length,
    },
    byCategory,
    byTopicEffective: byTopic,
    topStubLinkFiles: topStub.map((r) => ({
      file: r.file,
      category: r.category,
      stubLinks: r.stubLinks,
    })),
    duplicateTitlesSample: dupTitles.slice(0, 20).map(([t, fl]) => ({
      title: t,
      count: fl.length,
      files: fl.slice(0, 5),
    })),
    heuristicsSample,
  };

  const outDir = path.join(ROOT, 'reports');
  fs.mkdirSync(outDir, { recursive: true });
  const outPath = path.join(outDir, 'guides-audit.json');
  fs.writeFileSync(outPath, JSON.stringify(report, null, 2), 'utf8');
  console.log('Аудит гайдов:', path.relative(ROOT, outPath));
  console.log(JSON.stringify(report.totals, null, 2));
}

main();
