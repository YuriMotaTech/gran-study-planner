import { useI18n } from '../i18n/I18nContext';

export function LocaleToggle() {
  const { locale, setLocale, t } = useI18n();

  return (
    <div className="flex items-center gap-1 rounded border border-slate-200 bg-white p-0.5 shadow-sm dark:border-slate-600 dark:bg-slate-800">
      <button
        type="button"
        className={`rounded px-2 py-1 text-sm ${locale === 'pt' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'}`}
        aria-label={t('lang.switchToPt')}
        aria-pressed={locale === 'pt'}
        onClick={() => setLocale('pt')}
      >
        BR
      </button>
      <button
        type="button"
        className={`rounded px-2 py-1 text-sm ${locale === 'en' ? 'bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700'}`}
        aria-label={t('lang.switchToEn')}
        aria-pressed={locale === 'en'}
        onClick={() => setLocale('en')}
      >
        US
      </button>
    </div>
  );
}
