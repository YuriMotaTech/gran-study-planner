import { useCallback, useEffect, useMemo, useState } from 'react';
import { api } from '../services/api';
import type { StudyPlanStatus, WeeklyGoals, WeeklyProgress } from '../types/StudyPlan';

const statusOrder: StudyPlanStatus[] = ['pending', 'in_progress', 'done', 'overdue'];

export function useWeeklyGoals() {
  const [week, setWeek] = useState<string | undefined>(undefined);
  const [goals, setGoals] = useState<WeeklyGoals>({ pending: 0, in_progress: 0, done: 0, overdue: 0 });
  const [progress, setProgress] = useState<WeeklyProgress | null>(null);
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const [g, p] = await Promise.all([api.weeklyGoals(week), api.weeklyProgress(week)]);
      setGoals(g.goals);
      setProgress(p);
      setWeek(g.week);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error');
    } finally {
      setLoading(false);
    }
  }, [week]);

  useEffect(() => {
    void load();
  }, [load]);

  const updateGoal = (status: StudyPlanStatus, value: number) => {
    setGoals((prev) => ({ ...prev, [status]: Math.max(0, Math.trunc(value)) }));
  };

  const save = useCallback(async () => {
    setSaving(true);
    setError(null);
    try {
      const res = await api.upsertWeeklyGoals(goals, week);
      setWeek(res.week);
      await load();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unknown error');
    } finally {
      setSaving(false);
    }
  }, [goals, load, week]);

  const ordered = useMemo(() => statusOrder, []);

  return { week, goals, progress, loading, saving, error, ordered, setWeek, updateGoal, save, reload: load };
}

