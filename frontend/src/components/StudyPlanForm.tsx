import { FormEvent, useState } from 'react';
import { useI18n } from '../i18n/I18nContext';

type Props = {
  onSubmit: (title: string, deadline: string) => Promise<void>;
};

const fieldClass =
  'w-full rounded border border-slate-200 bg-white px-3 py-2 text-slate-900 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100';

export function StudyPlanForm({ onSubmit }: Props) {
  const { t } = useI18n();
  const [title, setTitle] = useState('');
  const [deadline, setDeadline] = useState('');

  async function handleSubmit(event: FormEvent) {
    event.preventDefault();
    if (!title || !deadline) return;
    await onSubmit(title, deadline);
    setTitle('');
    setDeadline('');
  }

  return (
    <form className="rounded border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800" onSubmit={handleSubmit}>
      <h2 className="mb-3 text-lg font-semibold text-slate-900 dark:text-slate-100">{t('studyPlanForm.heading')}</h2>
      <div className="mb-3">
        <label className="mb-1 block text-sm text-slate-700 dark:text-slate-300" htmlFor="title">
          {t('studyPlanForm.title')}
        </label>
        <input id="title" className={fieldClass} value={title} onChange={(e) => setTitle(e.target.value)} />
      </div>
      <div className="mb-3">
        <label className="mb-1 block text-sm text-slate-700 dark:text-slate-300" htmlFor="deadline">
          {t('studyPlanForm.deadline')}
        </label>
        <input id="deadline" type="datetime-local" className={fieldClass} value={deadline} onChange={(e) => setDeadline(e.target.value)} />
      </div>
      <button className="rounded bg-slate-900 px-3 py-2 text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200" type="submit">
        {t('studyPlanForm.create')}
      </button>
    </form>
  );
}
