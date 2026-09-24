---
target: welcome landing
total_score: 27
max_score: 32
na_heuristics: 7,9
p0_count: 0
p1_count: 2
p2_count: 2
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php"
target_fingerprint: "sha256:dbb0d4570804f5d1feed86f386e08d8da127661d1e9b2bb00d958e0c95583af1"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php
timestamp: 2026-09-23T14-30-41Z
slug: resources-views-welcome-blade-php
---
# Critique — `resources/views/welcome.blade.php` (Persuade landing)

Method: dual-agent (A: general/design review · B: general/detector+browser)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 4 | Live demo counter, aria-checked toggle, nav/drawer states, honest "Demo" badge |
| 2 | Match System / Real World | 3 | Correct FDI chart + Spanish; teeth are squares; "Toca un diente" desktop-first |
| 3 | User Control and Freedom | 3 | Reset/FAQ/skip-link; demo cycles only forward; drawer no backdrop/Esc |
| 4 | Consistency and Standards | 4 | Consistent teal CTA/radii/cards; `#testimonials` id reused for founders |
| 5 | Error Prevention | 3 | No forms; demo requires up to 6 clicks to land a state |
| 6 | Recognition Rather Than Recall | 3 | Legend lists only active diagnoses; "Sano" never shown |
| 7 | Flexibility and Efficiency | n/a | Persuade surface |
| 8 | Aesthetic and Minimalist Design | 4 | Strong hierarchy; docked by shine/orbs/noise/tilt |
| 9 | Error Recovery | n/a | No data entry/error states |
| 10 | Help and Documentation | 3 | FAQ, payment methods, security answer |
| **Total** | | **27/32** | **Good (84%)** |

## Design Specificity Verdict

**LLM:** One genuinely specific moment (the embedded odontogram: real FDI numbering, diagnosis cycling, live counter) surrounded by a competent generic template. The imagery is **off-domain**: `hero.webp` is a starry night sky, `soft.webp` B&W mountains, `cta.webp` a vintage alarm clock, and `og.jpg` (the WhatsApp/social card) is the same night sky. The signature visual is a grid of rounded squares, not teeth.

**Deterministic scan:** detector `[]` (0 findings, exit 0; engine 4.0.0, blade parser supported). Browser: **only first-party hosts**; 0 console errors/warnings; assets 200 (webp + woff2); **32 tooth buttons**, all with aria-labels and native focus; click cycle verified Sano→Caries→Obturación→Endodoncia→Corona→Ausente→Sano with counter 0→1→0; pricing toggle `aria-checked` flips and $39↔$31; mobile **page overflow 0** (demo internal scroll 728 vs 308), tooth 38.7px, nav CTA 44px; skip-link + `<main id="main-content">`; 1 `<img>` 0 missing alt; noscript reveal works; OG/Twitter/canonical/favicon present; contrast all pass (4.68/5.55/4.93); reduced-motion disables `.animate-blob`/`.shine`.

**Visual overlays:** skipped — no live session; fallback: Playwright request/console + DOM probes.

## Overall Impression

The hero is now a product, not a promise, and the honesty move (no fake testimonials, founders program) is strong; the score rose 22 → 27. What holds it back: off-domain imagery (especially the OG card) and the demo being truncated/fought by decoration on mobile, plus zero third-party proof.

## What's Working

1. **The hero is interactive product proof** — 32 teeth, 6-state cycle, live "en el plan" counter, reset; kills the "does it exist?" objection in one gesture.
2. **Honest positioning** — no fabricated testimonials, explicit "En beta privada", founders program.
3. **Technical craft** — self-hosted fonts, 0 external requests, 0 console errors, correct heading order, skip-link first tab stop, `role="switch"` + `aria-checked`, reduced-motion, all `<img>` alt.

## Priority Issues

- **[P1] Social/OG image is a starry night sky; backgrounds are off-domain.** `og.jpg` (1200×630, OG+Twitter) is a night sky; `hero/soft/cta.webp` are night sky / mountains / clock. Fix: replace `og.jpg` with a branded card (odontogram UI + logo + "Gestión dental sin fricción" + "Hecho para Venezuela"); replace or delete the placeholder backgrounds (CSS gradients beat irrelevant photos); remove unused `odontograma.png`/`odontograma-mobile.png`. `$impeccable document`
- **[P1] Zero third-party proof.** Trust section is 3 copy-pasteable claims; no clinic name, logo, count, founder, or face. Fix: one verifiable element (named founding clinic with permission, waitlist count, or founder name/city). Requires real input — do not fabricate. `$impeccable clarify`
- **[P2] Mobile demo looks broken.** Scroller 728 vs 308 visible; ~half the chart off-screen with no fade/peek/cue; labels 9px. Fix: right-edge gradient fade + "Desliza →" hint, or reflow to two arches that fit; labels ≥11px; targets toward 44px. `$impeccable adapt`
- **[P2] Decoration fights the demo.** Hero tracks cursor and tilts the card ±8° on every mousemove; `.shine` (z-20) sweeps over content and washes the diagnosis colors. Fix: disable tilt on the demo card (or ±2–3° on decorative layers only) and remove `.shine` from the card body. `$impeccable quieter`
- **[P3] Offer ambiguity (pricing vs founders).** $39/$89 + "14 días gratis" vs undefined "precio preferencial de fundador de por vida"; CTAs alternate "Empezar gratis" / "Unirme a la beta". Fix: state the founder offer concretely and unify the CTA label. `$impeccable clarify`

## Persona Red Flags

**Jordan (first-timer):** "roadmap/multi-sede/integraciones personalizadas" jargon; "0 en el plan" unexplained; "Sano" absent from legend; no post-click microcopy; can't tell if the product is live.

**Riley (stress tester):** 7th click silently wraps to Sano; **with JS disabled the Alpine `x-for` renders nothing → hero card empty** (only noscript reveal, not noscript content); drawer opens without scroll-lock; 32 consecutive tab stops; OG image is a night sky.

**Casey (mobile):** demo half-hidden with no scroll cue; 9px labels; ~39px targets with 6px gaps; page ~9,955px tall; drawer has no backdrop.

## Minor Observations
`#testimonials` id reused for a founders section; `historia.png` alt says "notas" but columns are DIENTE/SUPERFICIE/ESTADO/ACCIÓN; `historia.png` illegible at ~340px on mobile; toggle labels "Mensual/Anual" not clickable; annual math rounds to "−20%"; "A medida" at text-5xl dominates paid tiers; footer "Compañía" thin; only SVG favicon (and `public/favicon.ico` is 0 bytes); unused `odontograma.png`/`odontograma-mobile.png`.

## Questions to Consider
1. If the odontogram is your most persuasive asset, why does it appear once and as colored squares, not a mouth?
2. Can a Caracas dentist trust patient-data software with zero named customers?
3. You deleted fake testimonials — what is the one claim here that is uniquely and verifiably yours?
4. When the link is forwarded on WhatsApp, the preview is a night sky. What does that say before a word is read?
5. "Precios claros" sits above an undefined "precio preferencial de fundador de por vida." Which is the price?
