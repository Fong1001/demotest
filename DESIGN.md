---
name: Industrial Heritage
colors:
  surface: '#131313'
  surface-dim: '#131313'
  surface-bright: '#3a3939'
  surface-container-lowest: '#0e0e0e'
  surface-container-low: '#1c1b1b'
  surface-container: '#201f1f'
  surface-container-high: '#2a2a2a'
  surface-container-highest: '#353534'
  on-surface: '#e5e2e1'
  on-surface-variant: '#d1c5b4'
  inverse-surface: '#e5e2e1'
  inverse-on-surface: '#313030'
  outline: '#9a8f80'
  outline-variant: '#4e4639'
  surface-tint: '#e9c176'
  primary: '#e9c176'
  on-primary: '#412d00'
  primary-container: '#c5a059'
  on-primary-container: '#4e3700'
  inverse-primary: '#775a19'
  secondary: '#ddc0ba'
  on-secondary: '#3e2c28'
  secondary-container: '#56423d'
  on-secondary-container: '#caafa9'
  tertiary: '#c8c6c2'
  on-tertiary: '#30312e'
  tertiary-container: '#a6a5a1'
  on-tertiary-container: '#3b3b38'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#ffdea5'
  primary-fixed-dim: '#e9c176'
  on-primary-fixed: '#261900'
  on-primary-fixed-variant: '#5d4201'
  secondary-fixed: '#fadcd5'
  secondary-fixed-dim: '#ddc0ba'
  on-secondary-fixed: '#271814'
  on-secondary-fixed-variant: '#56423d'
  tertiary-fixed: '#e4e2dd'
  tertiary-fixed-dim: '#c8c6c2'
  on-tertiary-fixed: '#1b1c19'
  on-tertiary-fixed-variant: '#474744'
  background: '#131313'
  on-background: '#e5e2e1'
  surface-variant: '#353534'
  espresso-dark: '#1A100E'
  gold-muted: '#A8884B'
  cream-surface: '#FDFCFB'
  border-low-light: rgba(197, 160, 89, 0.2)
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 64px
    fontWeight: '700'
    lineHeight: 72px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.01em
  headline-xl:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.1em
  button:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 32px
  margin-mobile: 20px
  section-padding: 120px
---

## Brand & Style

The design system is built upon a "Industrial-Premium" narrative, blending the raw, tactile nature of timber manufacturing with the sophisticated reliability of a 30-year enterprise. It targets B2B stakeholders who value both artisanal quality and industrial-scale logistics.

The visual style is a hybrid of **Minimalism** and **Corporate Modernism**, utilizing heavy whitespace to balance the weight of high-contrast backgrounds. The aesthetic evokes the atmosphere of a high-end showroom within a pristine warehouse: structured, clean, and physically grounded. The contrast between deep espresso tones and warm cream sections creates a rhythmic "breathing" effect as users scroll through the narrative of the company.

## Colors

The palette is anchored by the interplay of organic wood tones and industrial shadows.

- **Primary (Gold/Tan):** Used for critical CTAs, iconography, and decorative accents. It represents quality and the "premium" finish of the products.
- **Secondary (Deep Espresso):** The primary background color for high-impact brand sections (Hero, Section Dividers). It provides a sense of depth and heritage.
- **Tertiary (Cream):** Used for content-heavy sections where high legibility is required. It provides a warm, accessible contrast to the darker brand colors.
- **Neutral (Black/Near-Black):** Used for footer backgrounds and deep UI elements to ground the layout.

The design defaults to a dark mode aesthetic for impact, but utilizes the Cream color as a secondary "light" mode for information-dense areas like product specifications and articles.

## Typography

The typography strategy relies on the contrast between the authoritative, literary feel of **Playfair Display** and the functional, modern clarity of **Inter**.

- **Headlines:** Use Playfair Display for all major headings. For "Display" roles, use tighter letter spacing to emphasize the editorial look.
- **Body:** Inter is used for all descriptive text. Maintain a generous line height (1.5x minimum) to ensure readability against dark backgrounds.
- **Utility:** Use uppercase labels with increased tracking for breadcrumbs, category tags, and small badges to maintain an organized, catalog-like feel.

## Layout & Spacing

The layout follows a **fixed-grid** system on desktop (12 columns) and a fluid 4-column system on mobile. 

A "Generous Whitespace" philosophy is applied to section padding (120px on desktop) to ensure the heavy imagery and dark colors do not feel claustrophobic. Components are organized using an 8px base unit. 

For the "How We Work" and "Why Choose Us" grids, use asymmetrical layouts or staggered grid placements to break the corporate monotony while maintaining structural integrity. Section transitions should be sharp and horizontal, reinforcing the industrial "stacking" metaphor of timber.

## Elevation & Depth

This design system avoids traditional drop shadows in favor of **Tonal Layering** and **Low-Contrast Outlines**.

- **Stacked Surfaces:** Depth is created by placing Cream containers on Espresso backgrounds. Elements within these containers use a thin `1px` border (using `border-low-light`) instead of shadows to define their boundaries.
- **Backdrop Blurs:** For the sticky header, use a subtle backdrop blur (12px) with a semi-transparent Espresso tint (80% opacity) to maintain legibility over the scrolling content.
- **The "Industrial" Shadow:** If elevation is strictly required for interactivity (e.g., a card hover), use a sharp, non-diffused 4px offset shadow in the Primary Gold color to mimic a structural offset rather than a soft glow.

## Shapes

To reinforce the "Timber/Warehouse" aesthetic, the design system utilizes **Sharp (0px)** corners for all primary containers, buttons, and images. This mimics the clean-cut edges of processed wood and industrial machinery.

In specific cases where "Humanity" or "Contact" is emphasized (like the floating WhatsApp button), a full circle (pill-shape) is permitted to differentiate personal communication from industrial product data. All other UI elements—cards, input fields, and images—must remain strictly rectangular.

## Components

### Buttons
- **Primary:** Solid Gold (#C5A059) with Espresso text. No border. Sharp corners. All-caps typography.
- **Secondary:** Transparent with a 2px Gold border. Gold text.
- **Tertiary:** Underlined text in Gold or Cream, depending on the background.

### Cards
- **Product Cards:** Image-first layout. The bottom 30% of the card is a solid Espresso or Cream block containing the title and "View Details" link. No rounded corners.
- **Feature Cards:** Use a thin Gold outline. Icons should be "Outline" style and rendered in the Primary Gold color.

### Input Fields
- **Form Fields:** Solid Cream background on Espresso sections, or white background on Cream sections. 1px Espresso border on focus. Sharp corners.
- **Labels:** Small caps, positioned strictly above the field.

### Accordions (FAQ)
- Clean horizontal lines separating questions. Use a "+" and "-" icon in Primary Gold. The background should remain flat; do not use shadows on open states.

### Process Timeline
- A vertical "Line" in Primary Gold connects the 7 steps. Badges are sharp-edged squares containing the step number in Gold with Espresso text.