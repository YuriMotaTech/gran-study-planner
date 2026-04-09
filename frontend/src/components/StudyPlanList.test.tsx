import { fireEvent, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import { renderWithI18n } from '../test/renderWithI18n';
import { StudyPlanList } from './StudyPlanList';

describe('StudyPlanList', () => {
  it('renders list of plans', () => {
    const plans = [
      {
        id: '1',
        userId: 1,
        title: 'PHP',
        status: 'pending' as const,
        deadline: '2026-04-01T00:00:00Z',
        createdAt: '2026-03-01T00:00:00Z',
        updatedAt: '2026-03-01T00:00:00Z'
      }
    ];

    renderWithI18n(<StudyPlanList plans={plans} onStatusChange={vi.fn().mockResolvedValue(undefined)} onDelete={vi.fn().mockResolvedValue(undefined)} />);

    expect(screen.getByText('PHP')).toBeInTheDocument();
  });

  it('triggers status change', () => {
    const onStatusChange = vi.fn().mockResolvedValue(undefined);

    renderWithI18n(
      <StudyPlanList
        plans={[{ id: '2', userId: 1, title: 'React', status: 'overdue', deadline: '2026-03-01T00:00:00Z', createdAt: '', updatedAt: '' }]}
        onStatusChange={onStatusChange}
        onDelete={vi.fn().mockResolvedValue(undefined)}
      />
    );

    fireEvent.change(screen.getByLabelText('status-2'), { target: { value: 'done' } });

    expect(onStatusChange).toHaveBeenCalledWith('2', 'done');
  });
});
