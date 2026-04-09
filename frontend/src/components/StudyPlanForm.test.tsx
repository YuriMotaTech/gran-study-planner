import { fireEvent, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';
import { renderWithI18n } from '../test/renderWithI18n';
import { StudyPlanForm } from './StudyPlanForm';

describe('StudyPlanForm', () => {
  it('submits title and deadline', async () => {
    const onSubmit = vi.fn().mockResolvedValue(undefined);

    renderWithI18n(<StudyPlanForm onSubmit={onSubmit} />);

    fireEvent.change(screen.getByLabelText('Title'), { target: { value: 'Algorithms' } });
    fireEvent.change(screen.getByLabelText('Deadline'), { target: { value: '2026-04-01T12:00' } });
    fireEvent.click(screen.getByRole('button', { name: 'Create' }));

    expect(onSubmit).toHaveBeenCalledWith('Algorithms', '2026-04-01T12:00');
  });
});
