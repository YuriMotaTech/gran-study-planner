import type en from './locales/en.json';

export type Messages = typeof en;

function getNested(obj: unknown, path: string): string | undefined {
  const parts = path.split('.');
  let cur: unknown = obj;
  for (const p of parts) {
    if (cur !== null && typeof cur === 'object' && p in (cur as object)) {
      cur = (cur as Record<string, unknown>)[p];
    } else {
      return undefined;
    }
  }
  return typeof cur === 'string' ? cur : undefined;
}

export function resolveMessage(messages: Messages, path: string, fallback: Messages): string {
  return getNested(messages, path) ?? getNested(fallback, path) ?? path;
}
