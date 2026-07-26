# Global Agent Directives

## Design & UI Constraints
- **NEVER produce default "empty" or "broken" template layouts.** Do not leave massive empty gaps, misaligned dropdowns, or broken CSS grids.
- **Always adhere to the `taste-skill` standards.** Do not produce "slop" UIs with clashing CSS frameworks (e.g., Aimeos Bootstrap fighting with Tailwind).
- If a 3rd-party system (like Aimeos) injects broken HTML that breaks the layout, **strip it out or completely override it**. Do not leave broken injected components (like default search bars or locale selectors) visible to the user.
- The UI must look like a premium, finished product at every step. Ensure dark mode consistency.
