# Washington Luis BJJ — Design System (Master)

Style direction: **Poster Editorial** (near-black + brand yellow + warm white), chosen to match "bold, poster-style imagery" and the school's existing logo, which was sampled directly for brand color accuracy.

## Colors (sampled from `assets/logo.jpg`)

| Token | Value | Use |
|---|---|---|
| `--color-yellow` | `#FCEF0B` | Primary accent, CTAs, headline highlights |
| `--color-yellow-hover` | `#E0D400` | Hover/active state for yellow elements |
| `--color-bg` | `#0A0A0A` | Page background |
| `--color-bg-alt` | `#111111` | Section alternation, footer |
| `--color-card` | `#151515` | Card surfaces |
| `--color-fg` | `#FAFAFA` | Primary text |
| `--color-fg-muted` | `#A6A6A6` | Secondary text |
| `--color-border` | `#2A2A2A` | Hairline borders |

Contrast: yellow-on-black and black-on-yellow both exceed WCAG AAA (18:1+). Body text uses white/light-gray on black, never yellow (yellow reserved for accents/CTAs).

## Typography

- **Display** (hero statements): `Anton` — bold poster-impact headlines
- **Heading** (section titles, nav, buttons, cards): `Barlow Condensed` 600/700 — athletic, condensed
- **Body**: `Inter` 400/500 — clean readability

## Motion

Subtle Scroll Reveal tier: opacity + 12px translateY, ~350ms, `power1.out`-equivalent easing. Implemented in vanilla JS via `IntersectionObserver` (`js/main.js`) rather than a GSAP dependency, to keep the site free of any animation library — matches the "lightweight, load smoothly, no performance cost" requirement. `prefers-reduced-motion` is fully respected (animations disabled at the CSS level).

## Structure

Multi-page static site: `index.html`, `programs.html`, `schedule.html`, `about.html`, `contact.html`. Shared `css/styles.css` + `js/main.js`, no build step, no framework — deploy anywhere (Netlify, GitHub Pages, any static host).

## Backend / integrations

- **Primary CTA** is `tel:` / `sms:` links (header, hero, mobile sticky bar, footer) — works with zero backend.
- **Lead form** (`contact.html`, `data-form="lead-capture"`) submits via `fetch()` to FormSubmit.co (`https://formsubmit.co/ajax/washingtonluisbjj@gmail.com`) — a free, account-free email-forwarding endpoint. FormSubmit sends a one-time confirmation email to that address on first submission; it must be clicked ("Activate Form") before delivery works. To move to Gymdesk later: swap the form's `action` for Gymdesk's lead-capture endpoint, or replace the `<form>` block with Gymdesk's embeddable widget.
- **Schedule page** (`schedule.html`) is a static list of color-coded day rows (`.day-schedule` > `.day-row.day-row--gi/--nogi/--striking/--closed`), matching the brand's printed schedule poster. When Gymdesk scheduling goes live, replace the `.day-schedule` block with Gymdesk's embeddable widget.
- No membership pricing is hard-coded on the site by design — CTA is "call for pricing," which stays accurate once Gymdesk membership tiers are finalized.
- **Analytics**: a commented-out GA4 snippet sits just before `</head>` on every page (`<!-- GA4 ANALYTICS ... -->`). Drop in a real Measurement ID and remove the comment markers to activate.

## SEO / technical infrastructure (added 2026-09-03)

- `robots.txt` + `sitemap.xml` at the project root (sitemap assumes the site deploys to `washingtonluisbjj.com` — update if that changes).
- `site.webmanifest` + full favicon set (`assets/favicon.ico`, `favicon-16x16.png`, `favicon-32x32.png`, `apple-touch-icon.png`, `icon-192.png`, `icon-512.png`) generated from the logo, replacing the old setup that served the raw 3.1MB `logo.jpg` as the tab icon.
- `assets/logo-nav.png` (77KB) is used for the visible header/footer logo `<img>` — also previously the 3.1MB original.
- `assets/og-image.jpg` (1200×630) is the real social-share card on every page (`og:image` + `twitter:card`), replacing a cropped square logo.
- `LocalBusiness` (`SportsActivityLocation`) JSON-LD on `index.html` with real name/address/phone/email — `sameAs` is an empty array pending social links (see below).
- `404.html` — branded custom error page.

## Real content status (updated 2026-09-03)

Phone, address, email, instructor bio, and several real photos are now live (sourced from the client directly and from washingtonluisbjj.com/instructors). Still outstanding:

- **Business hours** on `contact.html` — still a `REPLACE` comment. Attempted to pull from the client's Google Maps listing but the browser tool's Maps-specific policy check was down for the whole session; retry next session, or ask the client directly.
- **Testimonials** on `index.html` — still fabricated placeholders (marked `REPLACE`), same Google Maps blocker (the listing shows 5.0★ from 95 reviews, confirmed via the map embed, but review text wasn't accessible).
- **Social links** — footer icons still `href="#"`. Client said they'll provide these later; a project memory note exists to bring this up again.
