---
target: welcome landing
total_score: 33
max_score: 36
na_heuristics: 7
p0_count: 0
p1_count: 2
p2_count: 2
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php"
target_fingerprint: "sha256:48d0605826a5ad9a2b309ed5b455221e6dc174729e49c67e59312b4e28804a0e"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php
timestamp: 2026-09-24T14-06-46Z
slug: resources-views-welcome-blade-php
---
# Critique — `resources/views/welcome.blade.php` (Persuade landing)

Method: dual-agent (A: general/design review · B: general/detector+browser)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 4 | aria-live counter, aria-pressed toggle/segmented, scrolled nav, active FAQ |
| 2 | Match to Real World | 4 | FDI numbering, realistic occlusion, "hallazgos", Pago móvil/Zelle/Binance |
| 3 | User Control & Freedom | 3.5 | Reset, reversible toggle, collapsible FAQ, skip link |
| 4 | Consistency & Standards | 3.5 | Legend advertises 5 states but default cycle exposes 3; historia pills all teal |
| 5 | Error Prevention | 3.5 | Harmless clicks; legend/cycle mismatch invites "broken" misread |
| 6 | Recognition over Recall | 3.5 | Legend + labels good; gated states behind "Más estados" |
| 7 | Flexibility & Efficiency | n/a | Persuade surface |
| 8 | Aesthetic & Minimalist | 4 | Generous whitespace, strong type scale |
| 9 | Error Recovery | 3 | onerror hides broken image silently |
| 10 | Help & Documentation | 3.5 | FAQ answers trial/data/migration/payment |
| **Total** | | **33/36** | **Excellent (92%)** |

Note: this run scored heuristic 10 (applicable max 36) whereas earlier runs marked 7+10 n/a (max 32) — denominators differ, so the trend line is not like-for-like.

## Design Specificity Verdict

**LLM:** High specificity concentrated in the hero; middle third is generic SaaS. Distinctive identity (bone/ink/teal + terracotta, custom DF mark, grid + tooth-arch motif, real interactive odontogram). Risk isn't genericness; it's that the best asset (the demo) is under-exposed and proof is absent.

**Deterministic scan:** detector `[]` (0 findings, exit 0; regex mode for blade). Browser: **only first-party**; 0 console errors/warnings; assets 200; **initial counter "6 hallazgos en el plan"**; **32 tooth buttons**; seed verified (18 Ausente, 16/47 Caries, 26 Obturación, 36 Endodoncia, 46 Corona); "Más estados" OFF cycle `Sano→Caries→Obturación→Sano`, ON cycle adds Endodoncia/Corona/Ausente (6); letters white and backgrounds match spec; **letter contrast now passes AA** (Caries 6.27, Obturación 4.93, Endodoncia 5.93, Corona 5.70, Ausente 16.45); segmented control aria-pressed + $39↔$31/$89↔$71 + totals, pills 44px; mobile no overflow, internal scroll 828/308, tooth 45px, "Iniciar sesión" single-line 100×36, nav CTA 44px; skip-link + `<main>`; 1 img alt; noscript; heading order valid; OG + width/height; reduced-motion (blob none, spotlight hidden, scroll-behavior auto).

**Visual overlays:** skipped — no live session; fallback: Playwright probes.

## Overall Impression

Big jump (22 → 33): the demo is now credible (realistic occlusion, short cycle, contrast-passing letters) and the honesty holds. The ceiling is no longer craft — it's proof (no named clinic) and a couple of self-inflicted mismatches (legend vs cycle, mobile fold).

## What's Working

1. **The odontogram is real product** — seeded mixed occlusion, FDI arches, AA-darkened colors, legend above arches, grammar-correct aria-live counter.
2. **Honest, scam-aware positioning** — "beta privada", founders program, real contact email instead of fake logos.
3. **Localization depth** — Pago móvil/transferencia/Zelle/Binance, USD + "tasa del día", Spanish microcopy, FDI.

## Priority Issues

- **[P1] No proof, no name (intentional, conversion ceiling).** All claims, no named clinic/testimonial/logo/count. Fix: one verifiable human fact (permissioned pilot quote, named founder + city/photo, or "construido con N clínicas en Caracas/Maracaibo/Valencia"). Requires real data. `$impeccable clarify`
- **[P1] Legend promises 5 states, default cycle delivers 3.** Fix: make the legend reflect the active cycle (show only reachable chips, or reveal as the toggle expands), or expose all 6 by default, or relabel "Estados avanzados (6)". `$impeccable clarify`
- **[P2] Demo toggles are 16px tap targets on mobile** ("Más estados", "Reiniciar"). Fix: `min-h-[44px] px-3` in a flex row. `$impeccable adapt`
- **[P2] Trust claims generic for medical data.** Fix: specifics — export guarantee, role-based access (both true today); avoid overclaiming hosting/backups if unverified. `$impeccable harden`
- **[P3] `historia.png` pills all teal vs the new color system.** Fix: regenerate with the new pill colors or caption "ilustrativo"; reconcile Starter "Odontograma básico". `$impeccable document`
- **[P3] Hero hook below the fold on mobile** (demo top ≈ y703 / 844). Fix: tighten mobile hero spacing so the first tooth row peeks. `$impeccable adapt`

## Persona Red Flags

**Jordan:** legend/cycle mismatch → "demo broken"; "hallazgos/plan" assumes context; never says if "Empezar gratis" grants access now or joins a waitlist; no post-trial explanation.

**Riley:** no legal entity/named clinic → vaporware; aspirational features invite a test; annual monthly-equivalent headline flagged as dark-ish; "Ver cómo pagar" lands on a collapsed FAQ (panel 6 not opened).

**Casey:** 16px demo toggles; hero demo mostly below fold; teeth need a swipe before first success; crowded 390px header (~36px buttons); no sticky CTA.

## Minor Observations
"DEMO" badge `#157e73` on ~`#dfe9e1` ≈ 3.95:1 at 10px → fails AA small text. Hero H1 teal "sin fricción." ≈3.19 (passes large only). Desktop teeth 32px with cramped "Co". `onerror` hides image silently. Missing `og:locale`. Footer "Producto" omits "Fundadoras" present in nav. Mobile nav repeats header CTAs.

## Questions to Consider
1. If a dentist asks "¿quién más lo usa?", what does the page honestly answer — convert or kill the deal?
2. Best asset (demo) is least visible on the most common device — why is the hook below the fold?
3. Colorblind dentists: do C/O/E/Co/X letters suffice?
4. "Hecho para Venezuela" behind a generic email and no legal identity — safest or riskiest?
5. Legend teaches 5 states but lets you touch 3 — teaching your model or its limits?
6. "Precio de fundador de por vida" — sustainable, or bargain-hunter bait?
