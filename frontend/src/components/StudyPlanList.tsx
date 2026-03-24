import type { StudyPlan, StudyPlanStatus } from '../types/StudyPlan';

type Props = {
  plans: StudyPlan[];
  onStatusChange: (id: string, status: StudyPlanStatus) => Promise<void>;
  onDelete: (id: string) => Promise<void>;
};

const statuses: StudyPlanStatus[] = ['pending', 'in_progress', 'done', 'overdue'];

export function StudyPlanList({ plans, onStatusChange, onDelete }: Props) {
  if (plans.length === 0) {
    return <p className="rounded border border-dashed p-4 text-slate-600">No plans yet.</p>;
  }

  return (
    <ul className="space-y-3">
      {plans.map((plan) => (
        <li key={plan.id} className="rounded border bg-white p-4 shadow-sm">
          <div className="mb-2 flex items-center justify-between gap-2">
            <strong>{plan.title}</strong>
            <span className="rounded bg-slate-100 px-2 py-1 text-xs uppercase">{plan.status}</span>
          </div>
          <p className="mb-3 text-sm text-slate-600">Deadline: {new Date(plan.deadline).toLocaleString()}</p>
          <div className="flex gap-2">
            <select
              aria-label={`status-${plan.id}`}
              className="rounded border px-2 py-1"
              defaultValue={plan.status}
              onChange={(event) => void onStatusChange(plan.id, event.target.value as StudyPlanStatus)}
            >
              {statuses.map((status) => (
                <option key={status} value={status}>{status}</option>
              ))}
            </select>
            <button type="button" className="rounded bg-red-600 px-3 py-1 text-white" onClick={() => void onDelete(plan.id)}>
              Delete
            </button>
          </div>
        </li>
      ))}
    </ul>
  );
}
