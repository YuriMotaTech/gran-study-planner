import { fireEvent, render, screen } from '@testing-library/react';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { WeeklyGoalsPanel } from './WeeklyGoalsPanel';
import { api } from '../services/api';

vi.mock('../services/api', () => ({
  api: {
    weeklyGoals: vi.fn(),
    weeklyProgress: vi.fn(),
    upsertWeeklyGoals: vi.fn()
  }
}));

describe('WeeklyGoalsPanel', () => {
  beforeEach(() => {
    vi.resetAllMocks();
  });

  it('loads and saves weekly goals', async () => {
    vi.mocked(api.weeklyGoals).mockResolvedValue({
      week: '2026-W13',
      goals: { pending: 1, in_progress: 2, done: 3, overdue: 4 }
    });
    vi.mocked(api.weeklyProgress).mockResolvedValue({
      week: '2026-W13',
      goals: { pending: 1, in_progress: 2, done: 3, overdue: 4 },
      counts: { pending: 0, in_progress: 1, done: 1, overdue: 0 },
      percentages: { pending: 0, in_progress: 50, done: 33, overdue: 0 }
    });
    vi.mocked(api.upsertWeeklyGoals).mockResolvedValue({
      status: 'ok',
      week: '2026-W13',
      goals: { pending: 5, in_progress: 2, done: 3, overdue: 4 }
    });

    render(<WeeklyGoalsPanel />);

    expect(await screen.findByText('Week: 2026-W13')).toBeInTheDocument();

    const pendingInput = screen.getByLabelText('Pending') as HTMLInputElement;
    fireEvent.change(pendingInput, { target: { value: '5' } });

    fireEvent.click(screen.getByRole('button', { name: 'Save' }));

    expect(api.upsertWeeklyGoals).toHaveBeenCalledWith(
      { pending: 5, in_progress: 2, done: 3, overdue: 4 },
      '2026-W13'
    );
  });
});

