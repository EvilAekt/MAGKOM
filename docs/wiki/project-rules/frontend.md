# Frontend Architecture & Quality Guidelines

## 1. Accessibility (a11y)
- **Semantic HTML**: Prioritize semantic tags (`<header>`, `<nav>`, `<main>`, `<article>`, `<section>`, `<aside>`, `<footer>`) over generic `<div>` soup.
- **Form Controls**: Every input must have an associated `<label>` (using `for` or wrapping). Provide `aria-describedby` for error messages and helper texts.
- **Keyboard Navigation**: Ensure all interactive elements (`button`, `a`, input controls) are focusable with visible focus rings (`focus:ring-2 focus:outline-none`).
- **Images**: All `<img>` tags must have descriptive `alt` attributes or `alt=""` if purely decorative.

## 2. Responsive & Mobile-First Design
- **Breakpoints**: Design mobile-first using Tailwind CSS default breakpoints (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`).
- **Touch Targets**: Minimum interactive touch target size is 44x44px on mobile devices.
- **Fluid Layouts**: Use flexbox and CSS grid with fluid gaps and padding to prevent content overflow on varying viewport sizes.

## 3. Performance & Core Web Vitals (CWV)
- **Asset Bundling**: Leverage Vite for rapid HMR in development and tree-shaken, minified builds in production (`@vite(['resources/css/app.css', 'resources/js/app.js'])`).
- **LCP Optimization**: Preload critical hero images or web fonts. Avoid lazy-loading above-the-fold images.
- **CLS Prevention**: Always specify explicit `width` and `height` (or aspect-ratio classes) on image and video elements to avoid layout shifts.
- **Font Optimization**: Self-host fonts or load Google Fonts with `font-display: swap`.

## 4. Basic SEO & Meta Standards
- Every page must render a distinct `<title>` and meaningful `<meta name="description">`.
- Provide Open Graph tags (`og:title`, `og:description`, `og:image`) for shared pages.
- Ensure strict heading hierarchy: exactly one `<h1>` per view, followed by sequentially ordered `<h2>` and `<h3>`.
