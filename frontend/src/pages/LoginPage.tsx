import { FormEvent, useState } from 'react';

type Props = {
  onLogin: (email: string, password: string) => Promise<void>;
};

export function LoginPage({ onLogin }: Props) {
  const [email, setEmail] = useState('candidate@gran.com');
  const [password, setPassword] = useState('gran123');

  async function submit(event: FormEvent) {
    event.preventDefault();
    await onLogin(email, password);
  }

  return (
    <main className="mx-auto mt-20 max-w-md rounded border bg-white p-6 shadow-sm">
      <h1 className="mb-4 text-2xl font-bold">Gran Study Planner</h1>
      <form onSubmit={submit}>
        <input className="mb-3 w-full rounded border px-3 py-2" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="email" />
        <input className="mb-3 w-full rounded border px-3 py-2" type="password" value={password} onChange={(e) => setPassword(e.target.value)} placeholder="password" />
        <button className="w-full rounded bg-slate-900 px-3 py-2 text-white" type="submit">Login</button>
      </form>
    </main>
  );
}
