import { render, type RenderOptions } from '@testing-library/react';
import type { ReactElement, ReactNode } from 'react';
import { I18nProvider } from '../i18n/I18nContext';

export function renderWithI18n(ui: ReactElement, options?: Omit<RenderOptions, 'wrapper'>) {
  function Wrapper({ children }: { children: ReactNode }) {
    return <I18nProvider initialLocale="en">{children}</I18nProvider>;
  }
  return render(ui, { wrapper: Wrapper, ...options });
}
