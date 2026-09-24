---
target: welcome landing
total_score: 22
max_score: 32
na_heuristics: 7,10
p0_count: 0
p1_count: 2
p2_count: 3
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php"
target_fingerprint: "sha256:07f4546c39afb4c182d10c458e46b016d6c6210e50d3594469b880179a05f5af"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php
timestamp: 2026-09-23T13-15-52Z
slug: resources-views-welcome-blade-php
---
# Critique — `resources/views/welcome.blade.php` (Persuade landing)

Method: dual-agent (A: general/design review · B: general/detector+browser)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | FAQ/toggle/menu states confirmed; no active-section state; reveals hidden until scroll (noscript fallback exists) |
| 2 | Match System / Real World | 4 | Fluent dentist Spanish + Pago móvil/Zelle/Binance |
| 3 | User Control and Freedom | 3 | All reversible; no skip-link/back-to-top |
| 4 | Consistency and Standards | 2 | "Empezar gratis" renders black/teal/beige/teal/white across locations; "Entrar" vs "Iniciar sesión" |
| 5 | Error Prevention | 3 | No forms; toggle prevents price confusion; no signal of what /register demands |
| 6 | Recognition Rather Than Recall | 3 | Labeled nav; hamburger icon-only (has aria-label) |
| 7 | Flexibility and Efficiency | n/a | Persuade surface |
| 8 | Aesthetic and Minimalist Design | 2 | Hero ~60% empty right column with a 558×157 letterbox; bento void; heavy decorative motion budget |
| 9 | Error Recovery | 2 | No error surface; contact mailto silently dead-ends if domain isn't live |
| 10 | Help and Documentation | n/a | FAQ is objection-handling, not docs |
| **Total** | | **22/32** | **Acceptable (69%)** |

## Design Specificity Verdict

**LLM:** A tasteful authored skin (paper/ink/teal/terracotta, Outfit+Onest, ES-VE copy) over a category-interchangeable skeleton (default SaaS section order). Specificity lives in palette/type/copy, not composition/interaction. The one unmistakably dental asset — the odontogram — is the least credible thing on the page: under-scaled and visually glitched.

**Deterministic scan:** detector `[]` (0 findings, exit 0); B confirms `detect` **is** a documented verb (engine 0.1.5). Blade is regex-mode, so `[]` is a null result, not rendered-DOM validation. Browser: **only first-party hosts**; 0 console errors/warnings; all assets 200; **2 `<img>`, 0 missing alt**; `<noscript>` reveal works (29 reveals, 0 hidden); mobile **no overflow**, `<picture>` serves the mobile crop (`currentSrc …odontograma-mobile.png`, naturalWidth 1000), nav CTA 44px no overlap; pricing toggle works ($39/$89 → $31/$71, aria-checked flips); contrast all pass (eyebrow 4.68, muted 5.55, white/teal 4.93); reduced-motion disables `.animate-blob`.

**Visual overlays:** skipped — no `impeccable live` session; fallback: Playwright DOM/computed-style probes.

## Overall Impression

Craft floor is solid (self-hosted, accessible, honest claims, working pricing toggle, first-party only). The score drops because the hero — the strongest credibility slot — shows a glitched, under-scaled screenshot, and the proof layer (anonymous testimonials) reads unverifiable. Fix the hero artifact and the proof, and the page jumps.

## What's Working

1. **Palette + type system** — paper/ink/teal/terracotta + Outfit/Onest, fully self-hosted; reads "calm medical premium".
2. **Credibility-first structure around conversion** — "Tus datos, tuyos / Hecho para Venezuela / Soporte en español" + payment chips beside pricing.
3. **Pricing interaction + founder reframe** — working annual toggle with aria-checked, Pro clearly signaled, "Programa de clínicas fundadoras" turns thin proof into an offer.

## Priority Issues

- **[P1] Hero proof looks broken and under-scaled.** The odontogram export has a colliding legend, a duplicate "Odontograma" title (chrome + image), a clipped frame, and the "Próxima cita" chip covers the lower-right teeth; renders 558×157 in a tall column leaving ~60% empty. Fix: re-capture a real app viewport (~16:10, with patient header/toolbar), no overlapping legend, drop fake Mac dots, size to dominate the column, move the chip off the chart. `$impeccable document`
- **[P1] Anonymized testimonials read as unverifiable.** Two quotes attributed only to "Clínica fundadora · Ciudad" with a generic pin avatar. Fix: one on-record attribution (Dr./Dra. + clinic + consent) or a clinic logo; else replace with a verifiable metric or a named clinical advisor. `$impeccable clarify`
- **[P2] Feature overload + flagship bento cell wastes space / illegible mobile.** 6 cards; featured 2×2 cell ~40% empty and `historia.png` unreadable at ~290px. Fix: cut to 3–4 features or make the featured one a full-width showcase with a legible screenshot + `srcset`/`sizes`. `$impeccable distill`
- **[P2] Primary CTA inconsistent and duplicated.** "Empezar gratis" changes color 5×; 3 identical CTAs visible at once on mobile drawer; "Entrar" vs "Iniciar sesión". Fix: one primary style (teal) + one secondary everywhere; hide nav/hero CTA while drawer open; unify login label. `$impeccable layout`
- **[P2] Weak acquisition plumbing for a WhatsApp-first market.** Contact falls back to `mailto:`; no `og:*`/`twitter:*`/canonical/favicon; uncompressed backgrounds (hero.jpg 278KB @18%, soft.jpg 324KB @5%). Fix: set `CONTACT_WHATSAPP` and default to WhatsApp; add OG card (1200×630) + favicon; convert backgrounds to AVIF/WebP or CSS. `$impeccable optimize`
- **[P3] Hero `<picture>` aspect mismatch (B evidence).** `<img width="1932" height="544">` but the mobile source is 1000×544 → reserved ratio 3.55 vs served 1.84 = CLS risk on mobile. Fix: responsive `aspect-ratio` via CSS or two `<img>` variants. `$impeccable adapt`

## Persona Red Flags

**Jordan (first-timer):** only proof is a glitched unreadable odontogram; anonymous testimonials give no peers; mild jargon ("beta privada", "pasarela") at the doubt point; no explanation of what "Empezar gratis" does; no contact in nav.

**Riley (stress tester):** spots the colliding legend + duplicate "Odontograma" → "staged"; notices -20% rounding ($39→$31, $89→$71 don't match exactly); Enterprise "Contactar" is a mailto dead-end; copy promises unshown features; no OG preview when sharing.

**Casey (mobile):** footer links 18–20px; annual switch 48×24 and only the switch toggles (labels not tappable); flagship `historia.png` illegible ~290px; drawer shows 3 identical CTAs; ~750KB decorative backgrounds on 3G; positives: full-width thumb-reachable CTAs, no overflow.

## Minor Observations
No favicon/canonical; no skip-to-content; ~34 decorative SVGs lack `aria-hidden`; footer heading order h2→h4; trust bar has no heading; large hero dead space at 1440; `historia.png`/`odontograma.png` lack `srcset`; FAQ says trial includes "todas las funciones Pro" while Starter CTA is also "Empezar gratis". Reduced motion + noscript + alt + focus outlines are solid.

## Questions to Consider
1. What if the hero *were* the product — embedded/live odontogram or a 12s clip — instead of a picture of it?
2. If the fake Mac chrome disappeared and you showed one honest app window with a patient name, would honesty outperform the dots?
3. If testimonials stay anonymized, is the section earning its place vs one named advisor + the founder invite?
4. Does the homepage need six features, or three deep legible product moments?
5. Can "sin fricción" be quantified at the hero ("de Excel a primera cita en 10 minutos")?
6. For a WhatsApp-first market, why is the only contact channel a mailto?
