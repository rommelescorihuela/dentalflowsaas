---
target: welcome landing
total_score: 26
max_score: 32
na_heuristics: 7,10
p0_count: 0
p1_count: 2
p2_count: 3
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php"
target_fingerprint: "sha256:4709a9d82b300c5644236f1514dec3595544c5e12039ec0838ba277e6f083046"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php
timestamp: 2026-09-23T20-40-39Z
slug: resources-views-welcome-blade-php
---
# Critique — `resources/views/welcome.blade.php` (Persuade landing)

Method: dual-agent (A: general/design review · B: general/detector+browser)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | Demo counter/legend/toggle update; no per-tooth state name; "N en el plan" unnamed findings |
| 2 | Match System / Real World | 4 | Fluent dentist vocabulary + FDI numbering + local payments |
| 3 | User Control and Freedom | 3 | Reset/FAQ/toggle reversible; per-tooth undo = cycle 6 states |
| 4 | Consistency and Standards | 3 | Nav "Beta" vs heading "Programa de clínicas fundadoras"; FAQ "todo Pro" vs Starter "Odontograma básico" |
| 5 | Error Prevention | 3 | Low surface; no-JS pricing rendering risk |
| 6 | Recognition Rather Than Recall | 3 | Color-only encoding; "Sano" missing from legend |
| 7 | Flexibility and Efficiency | n/a | Persuade surface |
| 8 | Aesthetic and Minimalist Design | 4 | Disciplined palette/type/whitespace |
| 9 | Error Recovery | 3 | No error states to test |
| 10 | Help and Documentation | n/a | Persuade surface |
| **Total** | | **26/32** | **Good (81%)** |

## Design Specificity Verdict

**LLM:** Authored in patches; category-interchangeable as a whole. Specificity lives in three places — the interactive odontogram (FDI numbering, 6 states, live counter), the Venezuela-localized copy/payments, and the warm editorial palette. Problem: the one non-reusable asset is the *smallest* thing on the page, subordinated to stock scaffolding.

**Deterministic scan:** detector `[]` (0 findings, exit 0; engine 4.0.0). Browser: **only first-party hosts**; 0 console errors/warnings; assets 200 (webp + woff2 + og.jpg); **32 tooth buttons**; cycle verified Sano→Caries→Obturación→Endodoncia→Corona→Ausente→Sano with counter 0→1→0; **tilt removed** (`.tilt` transform none) and `.shine` 0 elements; toggle works via switch AND labels ($39↔$31, $89↔$71); mobile **page overflow 0**, internal demo scroll 828 vs 308, tooth **44.9px**, right-edge fade + "Desliza" cue present; skip-link + `<main id="main-content">`; 1 `<img>` 0 missing alt; heading order valid; noscript reveal works; OG/Twitter/canonical/favicon present (og.jpg 200); contrast pass (4.68/5.55/4.93); reduced-motion disables `.animate-blob`.

**Note (B):** the dev server was down at start; B restarted it with `php8.4 artisan serve` (left running).

**Visual overlays:** skipped — no live session; fallback: Playwright probes.

## Overall Impression

Strong craft and honest positioning; the demo is real product proof and the imagery is now on-brand. What holds it back: unearned trust (no human/named proof), color-only state encoding (a11y), an over-engineered pricing toggle, and an undiscoverable demo interaction. Score 27 → 26 (A weights the trust valley + a11y more heavily now).

## What's Working

1. **The odontogram demo is real** — 32 focusable buttons with `aria-label="Diente N: estado"`, FDI numbering, six states, working reset, live counter.
2. **Copy speaks Venezuelan dental practice fluently** — "tasa del día", Pago móvil/Transferencia/Zelle/Binance, "presupuestos", "arcada".
3. **Visual + technical discipline** — self-hosted Onest/Outfit, one `<img>` with explicit dimensions (no CLS), 0 console errors, no page overflow at 390px, reduced-motion + noscript honored.

## Priority Issues

- **[P1] The page asks for trust it never earns.** No named clinic, founder identity, testimonial, or hosting/backup/legal detail, while the founders section says you're a beta participant. Fix: a compact "Quiénes estamos detrás" (founder + a pilot-clinic quote or waitlist count) + a real security row (hosting, backups, export/deletion, privacy link next to the CTAs). Requires real input. `$impeccable harden`
- **[P1] The odontogram encodes state by color alone.** Caries/Obturación/Endodoncia/Corona differ only by hue; only "Ausente" has a glyph; ~10px swatches. Fix: letter/pattern inside each tooth (C/O/E/Co/X), state name on hover/focus + `aria-live`, larger swatches (WCAG 1.4.1). `$impeccable audit`
- **[P2] Pricing toggle is redundant, opaque, undersized.** One state controlled by 3 controls (Mensual/switch/Anual) at 24px; no annual total; no-JS rendering risk. Fix: single segmented control, show "$31/mes · facturado $372/año · ahorras $96", enlarge targets. `$impeccable clarify`
- **[P2] Demo interaction undiscoverable and monotonic.** 5 taps to reach Corona; no per-tooth state label; counter unnamed; "Sano" absent. Fix: state label/popover on hover-focus or direct picker, name the counter ("5 hallazgos: 2 caries, 1 corona…"), add "Sano" legend. `$impeccable delight`
- **[P2] Mobile hides login and crowds the logo.** "Iniciar sesión" only inside the hamburger; top bar pairs logo with a large CTA. Fix: keep a compact "Entrar" visible in the mobile bar or promote it in the menu. `$impeccable adapt`

## Persona Red Flags

**Jordan:** security FAQ has no hosting/backup/law detail; no support path near hero CTAs; "Toca" on desktop; "Empezar gratis" ×5 with no post-click microcopy.

**Riley:** possible no-JS pricing `$39$31`; white tooth has no legend entry; nav "Beta" vs "Programa de clínicas fundadoras"; FAQ "todo Pro" vs Starter "Odontograma básico"; no locale toggle.

**Casey:** targets under 44px (Reiniciar 50×16, toggle labels 24px, switch 48×24, footer links 18–20px, logo 40×40); login buried; only ~6 of 16 teeth visible in the demo.

## Minor Observations
Dead code after removing tilt/shine: `.tilt` on the card with no bound rx/ry, unused `.shine`/`.chip-float`/`float-slow` CSS and Alpine `rx/ry/tx/ty`. `photo-soft` at 5% effectively invisible. Hero texture keeps an unnecessary `grayscale/sepia/hue-rotate` filter. `og:image` lacks `width/height`. No legal entity/RIF/address/WhatsApp in footer. `scroll-smooth` stays under reduced-motion.

## Questions to Consider
1. What if the odontogram *were* the hero — full-bleed, readable on mobile — instead of a card with 6 visible teeth?
2. Where is the human face of the team for a product holding patient records?
3. What if you proved "sin fricción" with a 60s "create your clinic" preview instead of a 4-step claim list?
4. Is "beta privada" a selling point or an objection?
5. Should the annual toggle exist before pricing is validated?
