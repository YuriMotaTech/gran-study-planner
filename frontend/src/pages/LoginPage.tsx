import { FormEvent, useState } from 'react';
import { LocaleToggle } from '../components/LocaleToggle';
import { ThemeToggle } from '../components/ThemeToggle';
import { useI18n } from '../i18n/I18nContext';

type Props = {
  onLogin: (email: string, password: string) => Promise<void>;
};

export function LoginPage({ onLogin }: Props) {
  const { t } = useI18n();
  const [email, setEmail] = useState('candidate@gran.com');
  const [password, setPassword] = useState('gran123');

  async function submit(event: FormEvent) {
    event.preventDefault();
    await onLogin(email, password);
  }

  return (
    <main className="mx-auto mt-20 max-w-md rounded border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
      <div className="mb-4 flex flex-wrap items-start justify-between gap-2">
        <h1 className="text-2xl font-bold text-slate-900 dark:text-slate-100">{t('app.title')}</h1>
        <div className="flex flex-wrap items-center gap-2">
          <ThemeToggle />
          <LocaleToggle />
        </div>
      </div>
      <form onSubmit={submit}>
        <input
          className="mb-3 w-full rounded border border-slate-200 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder={t('login.emailPlaceholder')}
        />
        <input
          className="mb-3 w-full rounded border border-slate-200 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-400 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder={t('login.passwordPlaceholder')}
        />
        <button className="w-full rounded bg-slate-900 px-3 py-2 text-white hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-200" type="submit">
          {t('login.submit')}
        </button>
      </form>
    </main>
  );
}
