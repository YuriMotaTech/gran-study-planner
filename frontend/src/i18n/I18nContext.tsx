import { createContext, useCallback, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';
import en from './locales/en.json';
import pt from './locales/pt.json';
import { LOCALE_STORAGE_KEY, type Locale } from './types';
import { resolveMessage, type Messages } from './tResolve';

const dictionaries: Record<Locale, Messages> = { en, pt };

type I18nContextValue = {
  locale: Locale;
  setLocale: (locale: Locale) => void;
  t: (path: string) => string;
  localeTag: string;
};

const I18nContext = createContext<I18nContextValue | null>(null);

function readInitialLocale(): Locale {
  if (typeof window === 'undefined') {
    return 'en';
  }
  const stored = localStorage.getItem(LOCALE_STORAGE_KEY);
  if (stored === 'en' || stored === 'pt') {
    return stored;
  }
  if (typeof navigator !== 'undefined' && navigator.language.toLowerCase().startsWith('pt')) {
    return 'pt';
  }
  return 'en';
}

export function I18nProvider({ children, initialLocale }: { children: ReactNode; initialLocale?: Locale }) {
  const [locale, setLocaleState] = useState<Locale>(() => initialLocale ?? readInitialLocale());

  const setLocale = useCallback((next: Locale) => {
    setLocaleState(next);
    localStorage.setItem(LOCALE_STORAGE_KEY, next);
  }, []);

  const t = useCallback(
    (path: string) => resolveMessage(dictionaries[locale], path, dictionaries.en),
    [locale]
  );

  const localeTag = locale === 'pt' ? 'pt-BR' : 'en';

  useEffect(() => {
    document.documentElement.lang = localeTag;
  }, [localeTag]);

  const value = useMemo(
    (): I18nContextValue => ({
      locale,
      setLocale,
      t,
      localeTag
    }),
    [locale, setLocale, t, localeTag]
  );

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

export function useI18n(): I18nContextValue {
  const ctx = useContext(I18nContext);
  if (!ctx) {
    throw new Error('useI18n must be used within I18nProvider');
  }
  return ctx;
}
