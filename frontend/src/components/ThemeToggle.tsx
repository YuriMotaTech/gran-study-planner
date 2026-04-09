import { useI18n } from '../i18n/I18nContext';
import { useTheme } from '../theme/ThemeContext';

export function ThemeToggle() {
  const { t } = useI18n();
  const { preference, setPreference } = useTheme();

  return (
    <div
      className="flex items-center gap-0.5 rounded border border-slate-200 bg-white p-0.5 shadow-sm dark:border-slate-600 dark:bg-slate-800"
      role="group"
      aria-label={t('theme.group')}
    >
      <button
        type="button"
        className={`rounded px-2 py-1 text-xs ${preference === 'light' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'}`}
        aria-pressed={preference === 'light'}
        aria-label={t('theme.ariaLight')}
        onClick={() => setPreference('light')}
      >
        {t('theme.light')}
      </button>
      <button
        type="button"
        className={`rounded px-2 py-1 text-xs ${preference === 'dark' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'}`}
        aria-pressed={preference === 'dark'}
        aria-label={t('theme.ariaDark')}
        onClick={() => setPreference('dark')}
      >
        {t('theme.dark')}
      </button>
      <button
        type="button"
        className={`rounded px-2 py-1 text-xs ${preference === 'system' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'}`}
        aria-pressed={preference === 'system'}
        aria-label={t('theme.ariaSystem')}
        onClick={() => setPreference('system')}
      >
        {t('theme.system')}
      </button>
    </div>
  );
}
