import { useState } from 'react';
import { Dashboard } from './components/Dashboard';
import { LocaleToggle } from './components/LocaleToggle';
import { ThemeToggle } from './components/ThemeToggle';
import { StudyPlanForm } from './components/StudyPlanForm';
import { StudyPlanList } from './components/StudyPlanList';
import { WeeklyGoalsPanel } from './components/WeeklyGoalsPanel';
import { useI18n } from './i18n/I18nContext';
import { useStudyPlans } from './hooks/useStudyPlans';
import { LoginPage } from './pages/LoginPage';
import { api, authTokenStore } from './services/api';
import type { StudyPlanStatus } from './types/StudyPlan';

export default function App() {
  const { t } = useI18n();
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
    <main className="mx-auto min-h-screen max-w-5xl space-y-6 p-4">
      <header className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">{t('app.title')}</h1>
        <div className="flex flex-wrap items-center gap-2">
          <ThemeToggle />
          <LocaleToggle />
          <button
            type="button"
            className="rounded border border-slate-200 bg-white px-3 py-1 text-slate-800 shadow-sm hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
            onClick={() => {
              authTokenStore.clear();
              setIsAuthenticated(false);
            }}
          >
            {t('app.logout')}
          </button>
        </div>
      </header>

      <Dashboard stats={stats} />
      <WeeklyGoalsPanel />

      <section className="grid gap-4 md:grid-cols-2">
        <StudyPlanForm onSubmit={create} />
        <div className="rounded border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
          <h2 className="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{t('filters.title')}</h2>
          <select
            className="mb-3 w-full rounded border border-slate-200 bg-white px-2 py-1 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
            defaultValue={filters.status ?? ''}
            onChange={(event) => setFilters((prev) => ({ ...prev, status: (event.target.value || undefined) as StudyPlanStatus | undefined }))}
          >
            <option value="">{t('filters.allStatuses')}</option>
            <option value="pending">{t('status.pending')}</option>
            <option value="in_progress">{t('status.in_progress')}</option>
            <option value="done">{t('status.done')}</option>
            <option value="overdue">{t('status.overdue')}</option>
          </select>
          <div className="grid grid-cols-2 gap-2">
            <select
              className="rounded border border-slate-200 bg-white px-2 py-1 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
              value={filters.sortBy}
              onChange={(event) => setFilters((prev) => ({ ...prev, sortBy: event.target.value as 'deadline' | 'status' | 'created_at' }))}
            >
              <option value="deadline">{t('filters.sortByDeadline')}</option>
              <option value="status">{t('filters.sortByStatus')}</option>
              <option value="created_at">{t('filters.sortByCreatedAt')}</option>
            </select>
            <select
              className="rounded border border-slate-200 bg-white px-2 py-1 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
              value={filters.sortDirection}
              onChange={(event) => setFilters((prev) => ({ ...prev, sortDirection: event.target.value as 'asc' | 'desc' }))}
            >
              <option value="asc">{t('filters.asc')}</option>
              <option value="desc">{t('filters.desc')}</option>
            </select>
          </div>
        </div>
      </section>

      {loading && <p className="text-slate-700 dark:text-slate-300">{t('app.loading')}</p>}
      {error && <p className="text-red-600 dark:text-red-400">{error}</p>}
      <StudyPlanList plans={items} onStatusChange={updateStatus} onDelete={remove} />
    </main>
  );
}
