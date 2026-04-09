import { useI18n } from '../i18n/I18nContext';
import type { StudyPlan, StudyPlanStatus } from '../types/StudyPlan';

type Props = {
  plans: StudyPlan[];
  onStatusChange: (id: string, status: StudyPlanStatus) => Promise<void>;
  onDelete: (id: string) => Promise<void>;
};

const statuses: StudyPlanStatus[] = ['pending', 'in_progress', 'done', 'overdue'];

export function StudyPlanList({ plans, onStatusChange, onDelete }: Props) {
  const { t, localeTag } = useI18n();
  const dateLocale = localeTag === 'pt-BR' ? 'pt-BR' : 'en-US';

  if (plans.length === 0) {
    return (
      <p className="rounded border border-dashed border-slate-300 p-4 text-slate-600 dark:border-slate-600 dark:text-slate-400">
        {t('studyPlanList.empty')}
      </p>
    );
  }

  return (
    <ul className="space-y-3">
      {plans.map((plan) => (
        <li key={plan.id} className="rounded border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <div className="mb-2 flex items-center justify-between gap-2">
            <strong className="text-slate-900 dark:text-slate-100">{plan.title}</strong>
            <span className="rounded bg-slate-100 px-2 py-1 text-xs uppercase text-slate-800 dark:bg-slate-700 dark:text-slate-100">
              {t(`status.${plan.status}`)}
            </span>
          </div>
          <p className="mb-3 text-sm text-slate-600 dark:text-slate-400">
            {t('studyPlanList.deadlinePrefix')}: {new Date(plan.deadline).toLocaleString(dateLocale)}
          </p>
          <div className="flex gap-2">
            <select
              aria-label={`status-${plan.id}`}
              className="rounded border border-slate-200 bg-white px-2 py-1 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
              defaultValue={plan.status}
              onChange={(event) => void onStatusChange(plan.id, event.target.value as StudyPlanStatus)}
            >
              {statuses.map((status) => (
                <option key={status} value={status}>
                  {t(`status.${status}`)}
                </option>
              ))}
            </select>
            <button type="button" className="rounded bg-red-600 px-3 py-1 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600" onClick={() => void onDelete(plan.id)}>
              {t('studyPlanList.delete')}
            </button>
          </div>
        </li>
      ))}
    </ul>
  );
}
