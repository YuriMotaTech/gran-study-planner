import { FormEvent, useState } from 'react';

type Props = {
  onSubmit: (title: string, deadline: string) => Promise<void>;
};

export function StudyPlanForm({ onSubmit }: Props) {
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
    <form className="rounded border bg-white p-4 shadow-sm" onSubmit={handleSubmit}>
      <h2 className="mb-3 text-lg font-semibold">New study plan</h2>
      <div className="mb-3">
        <label className="mb-1 block text-sm text-slate-700" htmlFor="title">Title</label>
        <input id="title" className="w-full rounded border px-3 py-2" value={title} onChange={(e) => setTitle(e.target.value)} />
      </div>
      <div className="mb-3">
        <label className="mb-1 block text-sm text-slate-700" htmlFor="deadline">Deadline</label>
        <input id="deadline" type="datetime-local" className="w-full rounded border px-3 py-2" value={deadline} onChange={(e) => setDeadline(e.target.value)} />
      </div>
      <button className="rounded bg-slate-900 px-3 py-2 text-white" type="submit">Create</button>
    </form>
  );
}
