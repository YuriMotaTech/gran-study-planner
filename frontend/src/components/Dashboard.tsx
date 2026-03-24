import type { DashboardStats } from '../types/StudyPlan';

type Props = {
  stats: DashboardStats;
};

export function Dashboard({ stats }: Props) {
  return (
    <section className="grid grid-cols-2 gap-3 md:grid-cols-4">
      {Object.entries(stats).map(([status, total]) => (
        <article key={status} className="rounded border bg-white p-3 text-center shadow-sm">
          <p className="text-xs uppercase text-slate-500">{status}</p>
          <p className="text-2xl font-bold">{total}</p>
        </article>
      ))}
    </section>
  );
}
