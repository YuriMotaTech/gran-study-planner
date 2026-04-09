import { useI18n } from '../i18n/I18nContext';
import { useWeeklyGoals } from '../hooks/useWeeklyGoals';
import type { StudyPlanStatus } from '../types/StudyPlan';

export function WeeklyGoalsPanel() {
  const { t } = useI18n();
  const { week, goals, progress, loading, saving, error, ordered, updateGoal, save } = useWeeklyGoals();

  return (
    <section className="rounded border bg-white p-4 shadow-sm">
      <div className="mb-3 flex items-center justify-between gap-3">
        <div>
          <h2 className="text-lg font-semibold">{t('weeklyGoals.heading')}</h2>
          <p className="text-sm text-slate-600">
            {t('weeklyGoals.weekPrefix')}: {week ?? t('weeklyGoals.weekLoading')}
          </p>
        </div>
        <button
          type="button"
          className="rounded bg-slate-900 px-3 py-2 text-white disabled:opacity-50"
          onClick={() => void save()}
          disabled={saving || loading}
        >
          {saving ? t('weeklyGoals.saving') : t('weeklyGoals.save')}
        </button>
      </div>

      {error && <p className="mb-3 text-sm text-red-600">{error}</p>}
      {loading && <p className="mb-3 text-sm text-slate-600">{t('weeklyGoals.loading')}</p>}

      <div className="grid gap-3 md:grid-cols-2">
        <div className="rounded border p-3">
          <h3 className="mb-2 font-medium">{t('weeklyGoals.targets')}</h3>
          <div className="space-y-2">
            {ordered.map((status) => (
              <label key={status} className="flex items-center justify-between gap-3 text-sm">
                <span className="text-slate-700">{t(`status.${status as StudyPlanStatus}`)}</span>
                <input
                  type="number"
                  min={0}
                  className="w-28 rounded border px-2 py-1 text-right"
                  aria-label={t(`status.${status as StudyPlanStatus}`)}
                  value={goals[status]}
                  onChange={(e) => updateGoal(status, Number(e.target.value))}
                />
              </label>
            ))}
          </div>
        </div>

        <div className="rounded border p-3">
          <h3 className="mb-2 font-medium">{t('weeklyGoals.progress')}</h3>
          <div className="space-y-2">
            {ordered.map((status) => {
              const current = progress?.counts?.[status] ?? 0;
              const target = progress?.goals?.[status] ?? goals[status];
              const pct = progress?.percentages?.[status] ?? 0;
              const width = target > 0 ? Math.min(100, Math.round((current / target) * 100)) : 0;
              return (
                <div key={status} className="text-sm">
                  <div className="mb-1 flex items-center justify-between">
                    <span className="text-slate-700">{t(`status.${status as StudyPlanStatus}`)}</span>
                    <span className="text-slate-700">
                      {current}/{target} ({pct}%)
                    </span>
                  </div>
                  <div className="h-2 w-full rounded bg-slate-100">
                    <div className="h-2 rounded bg-slate-900" style={{ width: `${width}%` }} />
                  </div>
                </div>
              );
            })}
          </div>
          <p className="mt-3 text-xs text-slate-500">{t('weeklyGoals.footnote')}</p>
        </div>
      </div>
    </section>
  );
}

