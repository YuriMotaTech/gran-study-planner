import { render, type RenderOptions } from '@testing-library/react';
import type { ReactElement, ReactNode } from 'react';
import { I18nProvider } from '../i18n/I18nContext';
import { ThemeProvider } from '../theme/ThemeContext';

export function renderWithI18n(ui: ReactElement, options?: Omit<RenderOptions, 'wrapper'>) {
  function Wrapper({ children }: { children: ReactNode }) {
    return (
      <ThemeProvider>
        <I18nProvider initialLocale="en">{children}</I18nProvider>
      </ThemeProvider>
    );
  }
  return render(ui, { wrapper: Wrapper, ...options });
}
