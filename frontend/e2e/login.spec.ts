import { expect, test } from '@playwright/test';

test('demo login reaches authenticated shell', async ({ page }) => {
  await page.goto('/');

  await page.locator('form input').first().fill('candidate@gran.com');
  await page.locator('input[type="password"]').fill('gran123');
  await page.getByRole('button', { name: /^Login$|^Entrar$/ }).click();

  await expect(page.getByRole('button', { name: /Logout|Sair/ })).toBeVisible();
});
