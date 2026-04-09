import { FormEvent, useState } from 'react';
import { LocaleToggle } from '../components/LocaleToggle';
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
    <main className="mx-auto mt-20 max-w-md rounded border bg-white p-6 shadow-sm">
      <div className="mb-4 flex items-start justify-between gap-3">
        <h1 className="text-2xl font-bold">{t('app.title')}</h1>
        <LocaleToggle />
      </div>
      <form onSubmit={submit}>
        <input
          className="mb-3 w-full rounded border px-3 py-2"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder={t('login.emailPlaceholder')}
        />
        <input
          className="mb-3 w-full rounded border px-3 py-2"
          type="password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder={t('login.passwordPlaceholder')}
        />
        <button className="w-full rounded bg-slate-900 px-3 py-2 text-white" type="submit">
          {t('login.submit')}
        </button>
      </form>
    </main>
  );
}
