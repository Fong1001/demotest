---
name: nextjs-static-cloudflare-pages
description: Guidelines for building, troubleshooting, and deploying Next.js Static Exports (output: 'export') with internationalization (next-intl) on Cloudflare Pages.
---

# Next.js Static Export & Cloudflare Pages Deployment

This skill provides workflow patterns, configuration rules, and troubleshooting instructions for deploying Next.js applications using Static HTML Export on Cloudflare Pages, especially when using multilingual routing (i.e. `next-intl` with `[locale]` dynamic routing).

---

## 1. Core Configuration (`next.config.ts`)

For Cloudflare Pages static hosting, Next.js must be configured to generate flat static files. 

### Key Settings:
- **`output: "export"`**: Activates static export mode. Use an environment variable check (like `process.env.NODE_ENV === "production"`) to only enable this during production builds so that middleware/proxies still function during local development.
- **`trailingSlash: true`**: Crucial for static hosts. Converts URLs like `/en/shop` to directory files `/en/shop/index.html`. This ensures subpages resolve cleanly on refresh or direct URL access without throwing 404s.
- **`images.unoptimized: true`**: Next.js native image optimization requires a running Node.js server. For static hosting, it must be disabled/bypassed.

### Recommended `next.config.ts` Template:
```typescript
import type { NextConfig } from "next";
import createNextIntlPlugin from "next-intl/plugin";

const withNextIntl = createNextIntlPlugin("./src/i18n/request.ts");
const isStaticExport = process.env.NODE_ENV === "production";

const nextConfig: NextConfig = {
  // Static export only for production builds
  ...(isStaticExport && { output: "export" }),
  trailingSlash: true,

  images: {
    unoptimized: true, // Required for static hosting
  },
};

export default withNextIntl(nextConfig);
```

---

## 2. Multilingual Static Routing (`next-intl`)

Under static export, server middleware (`middleware.ts` / `proxy.ts`) **cannot execute** at runtime. Therefore, all routing must be pre-rendered and statically localized.

### Rules:
1. **Define `generateStaticParams` in Layouts**:
   Your layout `[locale]/layout.tsx` must export a `generateStaticParams()` function that lists all supported locales:
   ```typescript
   export function generateStaticParams() {
     return routing.locales.map((locale) => ({ locale }));
   }
   ```
2. **Call `setRequestLocale` in Server Components**:
   To prevent `next-intl` from falling back to dynamic runtime HTTP headers/cookies (which fails static builds with a `headers()` error), you must call `setRequestLocale(locale)` in **all** Layout and Page server components under `[locale]`:
   ```typescript
   import { setRequestLocale } from 'next-intl/server';

   export default async function Page({ params }: { params: Promise<{ locale: string }> }) {
     const { locale } = await params;
     setRequestLocale(locale);
     // ... rest of component
   }
   ```
3. **Use Localized Links**:
   Do **not** import raw `Link` from `next/link`. Instead, define navigation helpers in `src/i18n/navigation.ts`:
   ```typescript
   import { createNavigation } from 'next-intl/navigation';
   import { routing } from './routing';
   export const { Link, redirect, usePathname, useRouter } = createNavigation(routing);
   ```
   Import `Link` from this file in all pages. This automatically prefixes links with the current locale (e.g. `/en/shop` instead of `/shop`), preventing 404s.

---

## 3. Handling Dynamic Fetches during Build

If your pages fetch data from a local API (e.g. `http://localhost:8000`), the build on Cloudflare will fail because `localhost` is not running in the build environment.

### Pattern:
Detect `process.env.NODE_ENV === 'production'` inside your API clients and bypass localhost network requests, immediately returning static mock data or pre-cached objects instead:
```typescript
export async function fetchData() {
  if (process.env.NODE_ENV === 'production') {
    return MOCK_DATA; // Bypass local backend at build-time
  }
  
  const res = await fetch('http://localhost:8000/api/data');
  return res.json();
}
```

---

## 4. Troubleshooting Build Errors

### A. Lockfile Mismatch (`npm ci` fails)
- **Symptom**: Cloudflare build fails with `npm ci can only install packages when your package.json and package-lock.json are in sync`.
- **Cause**: Different Node/npm versions on your local machine and Cloudflare build machine.
- **Fix**: Remove `package-lock.json` from git tracking. Cloudflare Pages will automatically fall back to `npm install` and dynamically resolve matching versions.
  ```bash
  git rm package-lock.json
  git commit -m "chore: remove package-lock.json to let Cloudflare install dependencies dynamically"
  ```

### B. Route `dynamic = "error"` used `headers()`
- **Symptom**: Build fails during page generation complaining about `headers()` or `cookies()`.
- **Cause**: A Server Component is calling a `next-intl` translation hook or message getter without having configured static rendering.
- **Fix**: Verify `setRequestLocale(locale)` is being called at the very top of the layout and the failing page.
