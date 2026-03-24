import type { DashboardStats, StudyPlan, StudyPlanStatus } from '../types/StudyPlan';

const API_BASE_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8080';
const TOKEN_KEY = 'gsp.token';

export const authTokenStore = {
  get: () => localStorage.getItem(TOKEN_KEY),
  set: (token: string) => localStorage.setItem(TOKEN_KEY, token),
  clear: () => localStorage.removeItem(TOKEN_KEY)
};

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const token = authTokenStore.get();
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    ...((init?.headers as Record<string, string> | undefined) ?? {})
  };

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  const response = await fetch(`${API_BASE_URL}${path}`, { ...init, headers });
  if (!response.ok) {
    const payload = await response.json().catch(() => ({}));
    throw new Error(payload.error ?? 'Request failed');
  }

  return response.json() as Promise<T>;
}

export const api = {
  login: async (email: string, password: string) => request<{ token: string }>('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password })
  }),
  listStudyPlans: async (query: { status?: StudyPlanStatus; page?: number; perPage?: number; sortBy?: string; sortDirection?: string }) => {
    const params = new URLSearchParams();
    if (query.status) params.set('status', query.status);
    if (query.page) params.set('page', String(query.page));
    if (query.perPage) params.set('perPage', String(query.perPage));
    if (query.sortBy) params.set('sortBy', query.sortBy);
    if (query.sortDirection) params.set('sortDirection', query.sortDirection);
    return request<{ items: StudyPlan[]; total: number; page: number; perPage: number }>(`/study-plans?${params.toString()}`);
  },
  createStudyPlan: async (input: { title: string; deadline: string }) => request<{ data: StudyPlan }>('/study-plans', {
    method: 'POST',
    body: JSON.stringify(input)
  }),
  updateStatus: async (id: string, status: StudyPlanStatus) => request<{ status: string }>(`/study-plans/${id}`, {
    method: 'PATCH',
    body: JSON.stringify({ status })
  }),
  deleteStudyPlan: async (id: string) => request<{ status: string }>(`/study-plans/${id}`, { method: 'DELETE' }),
  dashboard: async () => request<{ stats: DashboardStats }>('/dashboard')
};
