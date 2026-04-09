import { useI18n } from '../i18n/I18nContext';
import { useWeeklyGoals } from '../hooks/useWeeklyGoals';
import type { StudyPlanStatus } from '../types/StudyPlan';

const panel = 'rounded border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800';
const subCard = 'rounded border border-slate-200 p-3 dark:border-slate-600 dark:bg-slate-900/40';

export function WeeklyGoalsPanel() {
  const { t } = useI18n();
  const { week, goals, progress, loading, saving, error, ordered, updateGoal, save } = useWeeklyGoals();

  return (
    <section className={panel}>
      <div className="mb-3 flex items-center justify-between gap-3">
        <div>
          <h2 className="text-lg font-semibold text-slate-900 dark:text-slate-100">{t('weeklyGoals.heading')}</h2>
          <p className="text-sm text-slate-600 dark:text-slate-400">
            {t('weeklyGoals.weekPrefix')}: {week ?? t('weeklyGoals.weekLoading')}
          </p>
        </div>
        <button
          type="button"
          className="rounded bg-slate-900 px-3 py-2 text-white hover:bg-slate-800 disabled:opacity-50 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200"
          onClick={() => void save()}
          disabled={saving || loading}
        >
          {saving ? t('weeklyGoals.saving') : t('weeklyGoals.save')}
        </button>
      </div>

      {error && <p className="mb-3 text-sm text-red-600 dark:text-red-400">{error}</p>}
      {loading && <p className="mb-3 text-sm text-slate-600 dark:text-slate-400">{t('weeklyGoals.loading')}</p>}

      <div className="grid gap-3 md:grid-cols-2">
        <div className={subCard}>
          <h3 className="mb-2 font-medium text-slate-900 dark:text-slate-100">{t('weeklyGoals.targets')}</h3>
          <div className="space-y-2">
            {ordered.map((status) => (
              <label key={status} className="flex items-center justify-between gap-3 text-sm">
                <span className="text-slate-700 dark:text-slate-300">{t(`status.${status as StudyPlanStatus}`)}</span>
                <input
                  type="number"
                  min={0}
                  className="w-28 rounded border border-slate-200 bg-white px-2 py-1 text-right text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                  aria-label={t(`status.${status as StudyPlanStatus}`)}
                  value={goals[status]}
                  onChange={(e) => updateGoal(status, Number(e.target.value))}
                />
              </label>
            ))}
          </div>
        </div>

        <div className={subCard}>
          <h3 className="mb-2 font-medium text-slate-900 dark:text-slate-100">{t('weeklyGoals.progress')}</h3>
          <div className="space-y-2">
            {ordered.map((status) => {
              const current = progress?.counts?.[status] ?? 0;
              const target = progress?.goals?.[status] ?? goals[status];
              const pct = progress?.percentages?.[status] ?? 0;
              const width = target > 0 ? Math.min(100, Math.round((current / target) * 100)) : 0;
              return (
                <div key={status} className="text-sm">
                  <div className="mb-1 flex items-center justify-between">
                    <span className="text-slate-700 dark:text-slate-300">{t(`status.${status as StudyPlanStatus}`)}</span>
                    <span className="text-slate-700 dark:text-slate-300">
                      {current}/{target} ({pct}%)
                    </span>
                  </div>
                  <div className="h-2 w-full rounded bg-slate-100 dark:bg-slate-700">
                    <div className="h-2 rounded bg-slate-900 dark:bg-slate-100" style={{ width: `${width}%` }} />
                  </div>
                </div>
              );
            })}
          </div>
          <p className="mt-3 text-xs text-slate-500 dark:text-slate-400">{t('weeklyGoals.footnote')}</p>
        </div>
      </div>
    </section>
  );
}

