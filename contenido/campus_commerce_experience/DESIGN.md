---
name: Campus Commerce Experience
colors:
  surface: '#f9f9ff'
  surface-dim: '#cfdaf2'
  surface-bright: '#f9f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f3ff'
  surface-container: '#e7eeff'
  surface-container-high: '#dee8ff'
  surface-container-highest: '#d8e3fb'
  on-surface: '#111c2d'
  on-surface-variant: '#474554'
  inverse-surface: '#263143'
  inverse-on-surface: '#ecf1ff'
  outline: '#787586'
  outline-variant: '#c8c4d7'
  surface-tint: '#5746d6'
  primary: '#422ec2'
  on-primary: '#ffffff'
  primary-container: '#5b4bdb'
  on-primary-container: '#e0dbff'
  inverse-primary: '#c6c0ff'
  secondary: '#9a4600'
  on-secondary: '#ffffff'
  secondary-container: '#fe893c'
  on-secondary-container: '#672c00'
  tertiary: '#005636'
  on-tertiary: '#ffffff'
  tertiary-container: '#007148'
  on-tertiary-container: '#82f5b8'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e4dfff'
  primary-fixed-dim: '#c6c0ff'
  on-primary-fixed: '#150066'
  on-primary-fixed-variant: '#3e28be'
  secondary-fixed: '#ffdbc9'
  secondary-fixed-dim: '#ffb68d'
  on-secondary-fixed: '#321200'
  on-secondary-fixed-variant: '#763300'
  tertiary-fixed: '#86f9bc'
  tertiary-fixed-dim: '#69dca1'
  on-tertiary-fixed: '#002112'
  on-tertiary-fixed-variant: '#005233'
  background: '#f9f9ff'
  on-background: '#111c2d'
  surface-variant: '#d8e3fb'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-xl-mobile:
    fontFamily: Inter
    fontSize: 26px
    fontWeight: '700'
    lineHeight: 34px
  headline-lg:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  title-sm:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-lg-medium:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '500'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  price-display:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 24px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  margin: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

The design system embodies a modern, energetic, and trustworthy peer-to-peer commerce hub built specifically for university communities across Colombia. It balances institutional reliability with youthful collegiate vitality. The aesthetic draws from modern digital commerce patterns: clean structural whitespace, crisp functional framing, and purposeful high-energy accents.

- **Brand Tone:** Approachable, secure, dynamic, and community-driven.
- **Visual Style:** Modern clean interface with subtle dimensional elevation. Clean white container cards sit on cool light gray utility canvases, using crisp micro-borders to ground dense product information.
- **Emotional Target:** Trust in transactions, clarity during exchange negotiations, and frictionless navigation across diverse student needs (academic materials, gadgets, services, snacks).

## Colors

The palette establishes structural distinction between persistent navigation, urgent transactional conversion, and status visibility.

- **Primary Canvas & Surfaces:** Pure White (`#FFFFFF`) for elevated cards, inputs, and top navigation. Off-canvas background uses Ultra-Light Slate (`#F7F8FC`) to define crisp spatial separation.
- **Primary Indigo (`#5B4BDB`):** Governs system identity, top-level navigation, structural active states, primary links, and standard interface controls.
- **Accent Orange (`#FF8A3D`):** Reserved strictly for high-conversion triggers, checkout funnels, "Agregar al carrito", "Solicitar pedido", and promotional tags ("Destacado").
- **Text & Hierarchy:** 
  - Primary text: Deep Charcoal (`#1E293B`) for high-contrast legibility.
  - Secondary text: Slate Medium (`#64748B`) for metadata, campus location markers, and subtitles.
  - Borders & Dividers: Cool Slate Border (`#E2E8F0`).
- **Semantic & Order Lifecycle Signals:**
  - Success (`#22A06B`): "Entregado", "Disponible".
  - Warning (`#F5A623`): "Pendiente", en espera de confirmación.
  - Information (`#3B82F6`): "Confirmado", en preparación.
  - Danger (`#E5484D`): "Cancelado", "Rechazado", "Agotado".
  - Neutral / Inactive (`#94A3B8`): Publicaciones pausadas o archivadas.
  - Ratings: Amber Gold (`#F59E0B`) specifically for student review stars.

## Typography

The typography scale utilizes **Inter** for its neutral geometric structure, vertical metrics, and tabular numeral precision—vital for pricing in Colombian Pesos (COP).

- **Price Formatting Rule:** Prices must always display currency symbol prefix and period-separated thousands formatting (e.g., `$12.000` or `$150.000 COP`). Use `price-display` for product card prices and bold transactional summaries.
- **Hierarchy Structure:**
  - `headline-xl`: Page hero headers, main marketplace section banners.
  - `headline-lg`: Category headers, modal titles, seller dashboard section titles.
  - `headline-md`: Card titles on detail views, drawer sheet headlines.
  - `title-sm`: Product card titles, order grouping titles.
  - `body-lg` / `body-lg-medium`: Main descriptions, checkout input values, table primary columns.
  - `body-sm`: Secondary descriptions, timestamps, campus department references.
  - `label-sm`: Micro-badges, notification pill counters, order status tokens.

## Layout & Spacing

The layout is built upon an 8px modular spacing framework optimized desktop-first at 1440px with a central content constraint.

- **Container Constraints:**
  - Standard desktop canvas width: `1440px`.
  - Max inner container width: `1200px` centered via auto horizontal margins.
- **Grid Structure:**
  - Desktop (1024px+): 12-column grid with `1.5rem` (24px) gutters and `2rem` (32px) outer page margins.
  - Product Catalog Layout: 4 columns on desktop (span 3 per card), 3 columns on small desktop / landscape tablets (span 4), 2 columns on mobile.
- **Rhythm Rules:**
  - Compact gap (`0.5rem` / 8px): Icon and text pairings, chip lists, input internal row spacing.
  - Component gap (`1rem` / 16px): Input form fields, table cell padding, card content padding.
  - Section gap (`2rem` / 32px): Distance between visual modules, category swimlanes, and dashboard grids.

## Elevation & Depth

Depth is established through soft, low-intensity diffused drop shadows paired with a structural 1px border layer (`#E2E8F0`). This prevents heavy contrast while maintaining tactile separation against `#F7F8FC`.

- **Level 0 (Flat):** Utility backgrounds and secondary layout panels. Borders define separation without elevation.
- **Level 1 (Cards & Inputs):** Default product cards, search bars, and data containers.
  - Shadow: `0px 1px 3px rgba(30, 41, 59, 0.05), 0px 4px 6px -1px rgba(30, 41, 59, 0.03)`
  - Border: `1px solid #E2E8F0`
- **Level 2 (Hover & Popovers):** Card hover states, sticky header drop-shadow, user navigation dropdowns.
  - Shadow: `0px 8px 16px -4px rgba(30, 41, 59, 0.08), 0px 4px 6px -2px rgba(30, 41, 59, 0.04)`
- **Level 3 (Modals & Drawers):** Order confirmation dialogs, payment sheets, filter sidebars.
  - Shadow: `0px 20px 25px -5px rgba(30, 41, 59, 0.12), 0px 10px 10px -5px rgba(30, 41, 59, 0.04)`
  - Backdrop: `#1E293B` at 40% opacity with a `4px` blur.

## Shapes

The design system employs a structured, tripartite corner radius scheme to create visual hierarchy based on component scale:

- **Large Surfaces (12px / 0.75rem):** Applied to product cards, modal dialogs, data table outer containers, and checkout summaries (`rounded-xl` in this baseline).
- **Controls & Elements (8px / 0.5rem):** Applied to buttons, search inputs, text areas, dropdown menus, and standard image previews (`rounded-md` / base roundedness).
- **Fully Rounded (Pill / 9999px):** Category filter chips, order status badges, user avatars, and badge counter indicators.

## Components

### Header (Sticky Top Navigation)
- Height: 72px with sticky positioning (`top: 0`, `z-index: 50`).
- Background: `#FFFFFF` with bottom border `1px solid #E2E8F0`.
- Left: Modern university marketplace monogram/logo and campus selector dropdown.
- Center: Search bar (max-width: 480px, height: 42px, radius 8px) with embedded search icon, category prefix selector, and placeholder `"Buscar libros, batas, calculadoras, snacks..."`.
- Right: Action icons for notifications and messages with red/indigo counter badges (height 18px, min-width 18px, pill-shaped, text 10px bold), cart preview trigger button, and student user profile dropdown (36px circular avatar).

### Buttons
- **Primary:** Solid `#5B4BDB` with white text. Hover state shifts to `#4B3DC4`. Radius 8px, padding 10px 18px.
- **Accent / CTA:** Solid `#FF8A3D` with white text. Used for `"Agregar al carrito"` and checkout operations. Hover shifts to `#E87428`.
- **Outline:** Transparent background, `1.5px solid #E2E8F0`, text `#1E293B`. Hover: `#F7F8FC` surface with border `#94A3B8`.
- **Danger:** Solid `#E5484D` background with white text, or ghost outline `#E5484D` for cancel operations.

### Cards (Product Listing)
- Dimensions & Layout: Vertical stack, background `#FFFFFF`, border `1px solid #E2E8F0`, radius 12px.
- Thumbnail: 1:1 aspect ratio top container with subtle border-bottom, image object-fit cover, featuring floating badge on top-left (e.g., `"Destacado"` in `#FF8A3D` or condition `"Como nuevo"` in `#5B4BDB`).
- Body: 16px padding.
  - Row 1: Category tag in `#64748B` (12px uppercase).
  - Row 2: Product title in `#1E293B` (16px, 2-line clamp).
  - Row 3: Rating row with solid `#F59E0B` star icon, numerical score (e.g., `4.8`), and review count `(24)`.
  - Row 4: Price formatted in COP (e.g., `$35.000`) in `#1E293B` font-bold 18px, with quick-add cart button in `#FF8A3D` (icon-only or compact button).

### Chips & Badges
- **Status Badges:** Fully rounded pills (`border-radius: 9999px`), padding 4px 10px, text 12px bold.
  - "Entregado / Disponible": `#22A06B` text on `#E8F8F0` background.
  - "Pendiente": `#B8740B` text on `#FEF6E9` background.
  - "Confirmado": `#2563EB` text on `#EFF6FF` background.
  - "Cancelado / Agotado": `#D32F2F` text on `#FEECEE` background.
- **Category Filter Chips:** 36px height, rounded-full. Default has white background, border `1px solid #E2E8F0`, text `#64748B`. Active state has `#5B4BDB` background with white text.

### Form Inputs
- Height 42px, radius 8px, border `1px solid #E2E8F0`, padding 8px 12px, font-size 14px.
- Focus: Border shifts to `#5B4BDB` with a 2px outer focus ring in `rgba(91, 75, 219, 0.2)`.

### Data Tables (Orders & Sales Management)
- Border radius 12px, full width, border `1px solid #E2E8F0`. Header row uses `#F7F8FC` background, text 12px uppercase in `#64748B`.
- Row height: 56px with subtle border-bottom `1px solid #E2E8F0`. Alternating row hover: `#F8FAFC`.
- Price column aligned to right with tabular numbers.