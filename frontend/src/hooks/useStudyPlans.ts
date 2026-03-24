import { useCallback, useEffect, useState } from 'react';
import { api } from '../services/api';
import type { DashboardStats, StudyPlan, StudyPlanStatus } from '../types/StudyPlan';

type Filters = {
  status?: StudyPlanStatus;
  page: number;
  perPage: number;
  sortBy: 'deadline' | 'status' | 'created_at';
  sortDirection: 'asc' | 'desc';
};

export function useStudyPlans() {
  const [items, setItems] = useState<StudyPlan[]>([]);
  const [stats, setStats] = useState<DashboardStats>({ pending: 0, in_progress: 0, done: 0, overdue: 0 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [filters, setFilters] = useState<Filters>({ page: 1, perPage: 10, sortBy: 'deadline', sortDirection: 'asc' });

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const [plans, dashboard] = await Promise.all([
        api.listStudyPlans(filters),
        api.dashboard()
      ]);
      setItems(plans.items);
      setStats(dashboard.stats);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error');
    } finally {
      setLoading(false);
    }
  }, [filters]);

  useEffect(() => {
    void load();
  }, [load]);

  const create = async (title: string, deadline: string) => {
    await api.createStudyPlan({ title, deadline });
    await load();
  };

  const updateStatus = async (id: string, status: StudyPlanStatus) => {
    await api.updateStatus(id, status);
    await load();
  };

  const remove = async (id: string) => {
    await api.deleteStudyPlan(id);
    await load();
  };

  return { items, stats, loading, error, filters, setFilters, create, updateStatus, remove, reload: load };
}
