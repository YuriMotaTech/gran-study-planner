import { useState } from 'react';
import { Dashboard } from './components/Dashboard';
import { StudyPlanForm } from './components/StudyPlanForm';
import { StudyPlanList } from './components/StudyPlanList';
import { WeeklyGoalsPanel } from './components/WeeklyGoalsPanel';
import { useStudyPlans } from './hooks/useStudyPlans';
import { LoginPage } from './pages/LoginPage';
import { api, authTokenStore } from './services/api';
import type { StudyPlanStatus } from './types/StudyPlan';

export default function App() {
  const [isAuthenticated, setIsAuthenticated] = useState(Boolean(authTokenStore.get()));
  const { items, stats, loading, error, filters, setFilters, create, updateStatus, remove } = useStudyPlans();

  if (!isAuthenticated) {
    return (
      <LoginPage
        onLogin={async (email, password) => {
          const response = await api.login(email, password);
          authTokenStore.set(response.token);
          setIsAuthenticated(true);
        }}
      />
    );
  }

  return (
    <main className="mx-auto max-w-5xl space-y-6 p-4">
      <header className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">Gran Study Planner</h1>
        <button
          type="button"
          className="rounded border px-3 py-1"
          onClick={() => {
            authTokenStore.clear();
            setIsAuthenticated(false);
          }}
        >
          Logout
        </button>
      </header>

      <Dashboard stats={stats} />
      <WeeklyGoalsPanel />

      <section className="grid gap-4 md:grid-cols-2">
        <StudyPlanForm onSubmit={create} />
        <div className="rounded border bg-white p-4 shadow-sm">
          <h2 className="mb-3 text-lg font-semibold">Filters</h2>
          <select
            className="mb-3 w-full rounded border px-2 py-1"
            defaultValue={filters.status ?? ''}
            onChange={(event) => setFilters((prev) => ({ ...prev, status: (event.target.value || undefined) as StudyPlanStatus | undefined }))}
          >
            <option value="">All statuses</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In progress</option>
            <option value="done">Done</option>
            <option value="overdue">Overdue</option>
          </select>
          <div className="grid grid-cols-2 gap-2">
            <select
              className="rounded border px-2 py-1"
              value={filters.sortBy}
              onChange={(event) => setFilters((prev) => ({ ...prev, sortBy: event.target.value as 'deadline' | 'status' | 'created_at' }))}
            >
              <option value="deadline">Sort by deadline</option>
              <option value="status">Sort by status</option>
              <option value="created_at">Sort by created at</option>
            </select>
            <select
              className="rounded border px-2 py-1"
              value={filters.sortDirection}
              onChange={(event) => setFilters((prev) => ({ ...prev, sortDirection: event.target.value as 'asc' | 'desc' }))}
            >
              <option value="asc">Asc</option>
              <option value="desc">Desc</option>
            </select>
          </div>
        </div>
      </section>

      {loading && <p>Loading...</p>}
      {error && <p className="text-red-600">{error}</p>}
      <StudyPlanList plans={items} onStatusChange={updateStatus} onDelete={remove} />
    </main>
  );
}
