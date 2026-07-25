# Build Prompt — ZON BUMIJAYA ENTERPRISE Website
## For use in Claude Code / Cursor / Lovable / Bolt / v0 (AI dev tools)

**How to use:** Paste the **Master Build Prompt** first in a fresh project. Once the base site is scaffolded, use the follow-up prompts to add each page/section. Keep this doc open and paste sections in order — don't dump everything in one giant prompt, the tool will build better in stages.

---

## 0. Before You Start — Assets to Have Ready
- Exported Stitch designs/screenshots (reference images help the AI match your approved look)
- Logo file (SVG/PNG)
- Product photos (pallet, laminated wood, finger joint)
- Final EN copy from the PRD (BM/ZH can follow once EN is locked)
- Contact details: saleszonbumi@gmail.com / +60 11-3651 7252 / No. 95A, Jln Tanjong 1, Tmn Desa Cemerlang, 81800 Ulu Tiram, Johor / SSM Reg. No. 202003035274

---

## 1. Master Build Prompt (paste first)

```
Build a production-grade marketing website for "Zon Bumijaya Enterprise," a timber
supply, processing, and delivery company based in Johor Bahru, Malaysia with 30+
years of experience. This must be a REAL, deployable product — not a static mockup.

TECH STACK
- Next.js 14+ (App Router), TypeScript, Tailwind CSS
- next-intl for i18n, supporting English (en), Bahasa Malaysia (ms), and Chinese (zh)
  with proper locale routing (/en/..., /ms/..., /zh/...) and hreflang tags
- Framer Motion for animations and micro-interactions
- next/image for all images (auto WebP, responsive, lazy-loaded)
- Deployable to Vercel out of the box

DESIGN SYSTEM (match attached Stitch reference designs)
- Palette: deep brown/espresso (#1a120b range) and black backgrounds, warm gold/tan
  accents (#c9a15a range), cream/off-white content sections (#f7f3ec range)
- Bold serif for headings (e.g. Playfair Display or similar), clean sans-serif for
  body copy (e.g. Inter)
- Generous whitespace, industrial-premium feel, mobile-first responsive at every
  breakpoint

SITE STRUCTURE (build as separate routes, not just anchor scrolling)
- / (homepage): hero, who-we-are, how-we-work, products preview, why-choose-us,
  FAQ, contact/footer
- /products/solid-wood-pallet
- /products/laminated-wood
- /products/finger-joint
- /process (production flow, 7-step timeline)
- /contact (quote request form + map)
- /blog (Phase 2 — build the route and layout now, empty state is fine)

INTERACTIVITY / UX
- Sticky header that shrinks/condenses on scroll
- Smooth scroll-triggered fade/slide-in animations on section entry (Framer Motion,
  use viewport-triggered animation, respect prefers-reduced-motion)
- Animated counters for stats (30+ Years, etc.) that count up when scrolled into view
- Product cards with subtle hover lift/shadow transitions
- FAQ accordion with smooth expand/collapse
- Floating WhatsApp button (bottom-right, persistent across all pages, deep-links to
  wa.me with a pre-filled message)
- Mobile menu: full-screen slide-in drawer with smooth transition
- Language switcher in header that preserves the current page when switching locale
- Form validation with inline error states (no full page reload on submit)
- Page transitions should feel smooth, not jarring — no layout shift (maintain
  strong Cumulative Layout Shift score)

SEO REQUIREMENTS
- Proper semantic HTML5 (one H1 per page, logical heading hierarchy)
- Dynamic <title> and <meta description> per page/locale via Next.js metadata API
- Open Graph + Twitter Card meta tags per page
- XML sitemap (sitemap.xml) auto-generated including all locale variants
- robots.txt
- Canonical URLs + hreflang alternate links for en/ms/zh on every page
- Descriptive, keyword-relevant alt text on every image (not stuffed)
- Core Web Vitals target: LCP < 2.5s, CLS < 0.1, INP < 200ms

GEO (GENERATIVE ENGINE OPTIMIZATION) REQUIREMENTS
- Add JSON-LD structured data:
  - LocalBusiness schema on homepage (name, address, phone, geo coordinates,
    priceRange, areaServed: Johor Bahru + Malaysia)
  - Product schema on each product page (name, description, material)
  - FAQPage schema on the FAQ section with the actual Q&A content
  - BreadcrumbList schema on product and process pages
- Write the "Who We Are" and each product overview as clear, self-contained,
  factual paragraphs near the top of the page (not buried under marketing copy) —
  this content should be easy for an AI system to extract and cite accurately
- Include a concise "Company Facts" summary block (founded/years active, location,
  core products, delivery coverage) in plain text near the footer, formatted so it
  reads well even without styling
- Keep NAP (Name, Address, Phone) identical, word-for-word, across every page

ACCESSIBILITY
- WCAG 2.1 AA: sufficient color contrast on the brown/gold/cream palette, all
  interactive elements keyboard-navigable and focus-visible, alt text on all
  images, form labels properly associated with inputs

PERFORMANCE
- Optimize and lazy-load all images below the fold
- Font subsetting/preloading for the serif and sans-serif fonts
- No unused JS shipped to the client; keep bundle size lean

Scaffold the project now with this structure, the design system, the homepage route
fully built, and placeholder content pulled from the company profile below. I will
provide page-by-page content and follow-up prompts after reviewing the homepage.
```

---

## 2. Follow-Up Prompt — Product Pages

```
Now build the three product detail pages at /products/solid-wood-pallet,
/products/laminated-wood, and /products/finger-joint, matching the homepage design
system exactly. Each page needs:
- Hero banner with product photo and title
- Three-column section: Overview, Product Features (checklist icons), Available
  Sizes/Customisation (bulleted list)
- Sticky "Request a Quote" CTA button that scrolls to or links to /contact
- Breadcrumb navigation (Home > Products > [Product Name])
- Related-products carousel linking to the other two product pages
- Full Product JSON-LD schema and per-page metadata (title, description, OG tags)
- Same scroll-triggered animations and hover states as the homepage product cards
```

---

## 3. Follow-Up Prompt — Process Page

```
Build the /process page: a vertical step-by-step timeline titled "From Intake to
Delivery" with 7 steps (Log Selection, Cutting & Sizing, Processing, Treatment,
Assembly, Quality Check, Delivery). Each step has a numbered badge, short
description, and photo, alternating left/right down the page, animating into view
as the user scrolls (Framer Motion, respect prefers-reduced-motion). Match the
existing design system.
```

---

## 4. Follow-Up Prompt — Contact Page

```
Build the /contact page with a two-column responsive layout:
- Left: quote request form (Name, Company, Phone, Product Interest dropdown,
  Message, Submit) with client-side validation and inline error states, submitting
  via a Next.js API route to [email service — e.g. Resend]
- Right: embedded Google Map pinned to No. 95A, Jln Tanjong 1, Tmn Desa Cemerlang,
  81800 Ulu Tiram, Johor, plus address, WhatsApp button, phone, email, and business
  hours
- Trust badges below the fold: 30+ Years, SSM Registered (202003035274), Johor
  Bahru Based
- Include LocalBusiness JSON-LD on this page as well
```

---

## 5. Follow-Up Prompt — i18n Content Pass

```
Add Bahasa Malaysia (ms) and Chinese (zh) translations for every page using
next-intl message files. Use the English copy I've approved as the source of
truth. Flag any translated strings you're not fully confident in so I can have a
native speaker review them before launch. Ensure the language switcher preserves
the current route when changing locale, and that hreflang tags update correctly
per page.
```

---

## 6. Follow-Up Prompt — Pre-Launch QA Pass

```
Run through the site and fix:
- Any Cumulative Layout Shift issues
- Any missing alt text or heading hierarchy problems
- Any broken links or missing hreflang/canonical tags
- Validate all JSON-LD schema blocks are well-formed (LocalBusiness, Product,
  FAQPage, BreadcrumbList)
- Confirm mobile menu, WhatsApp button, and forms work correctly on small screens
- Confirm the site builds and deploys cleanly on Vercel with no console errors
```

---

*Prompts drafted to turn the approved Stitch designs and PRD into a real, deployable Next.js product with SEO and GEO built in from the start.*
