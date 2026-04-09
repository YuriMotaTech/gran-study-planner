import { useI18n } from '../i18n/I18nContext';
import type { DashboardStats, StudyPlanStatus } from '../types/StudyPlan';

type Props = {
  stats: DashboardStats;
};

export function Dashboard({ stats }: Props) {
  const { t } = useI18n();

  return (
    <section className="grid grid-cols-2 gap-3 md:grid-cols-4">
      {Object.entries(stats).map(([status, total]) => (
        <article
          key={status}
          className="rounded border border-slate-200 bg-white p-3 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800"
        >
          <p className="text-xs uppercase text-slate-500 dark:text-slate-400">{t(`status.${status as StudyPlanStatus}`)}</p>
          <p className="text-2xl font-bold text-slate-900 dark:text-slate-100">{total}</p>
        </article>
      ))}
    </section>
  );
}
