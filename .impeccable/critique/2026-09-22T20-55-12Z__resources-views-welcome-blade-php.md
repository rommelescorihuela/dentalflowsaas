---
target: welcome landing (final)
total_score: 26
max_score: 32
na_heuristics: 7,10
p0_count: 1
p1_count: 2
p2_count: 2
p3_count: 2
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php"
target_fingerprint: "sha256:8c39887509b821eb5d9a32c167a33964d59c8dfc99c2cf54a44e70386684967c"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/welcome.blade.php
timestamp: 2026-09-22T20-55-12Z
slug: resources-views-welcome-blade-php
---
# Critique (final) — `resources/views/welcome.blade.php`

Method: dual-agent (A: general/design review · B: general/detector+browser)

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 3 | FAQ aria-expanded + nav scroll state; mobile menu toggle exposes no state |
| 2 | Match System / Real World | 4 | Best dimension: correct Venezuelan dental + payment vocabulary |
| 3 | User Control and Freedom | 3 | Mobile menu doesn't close after tapping a link; no Esc/outside-tap |
| 4 | Consistency and Standards | 3 | CTA labels unified; but nav "Testimonios" ≠ section "Clínicas fundadoras en beta"; footer "Términos" twice |
| 5 | Error Prevention | 3 | No forms; low-risk surface |
| 6 | Recognition Rather Than Recall | 4 | Every icon labeled; anchors everywhere |
| 7 | Flexibility and Efficiency | n/a | Persuade surface |
| 8 | Aesthetic and Minimalist Design | 3 | Decorative overload in hero (noise + orbs + spotlight + arches + shine + duotone) |
| 9 | Error Recovery | 3 | Nothing can fail on-page |
| 10 | Help and Documentation | n/a | FAQ is content, not a help system |
| **Total** | | **26/32** | **Good (81%)** |

## Design Specificity Verdict

**LLM:** Competent but generic. Specificity lives in copy/data (dental legend, payment rails, city labels), not in form (sand+lagoon, orbs, noise, spotlight, tilted mock, bento, masonry). The two brand assets just invested in — self-hosted duotone photography and Instrument Serif — are invisible or unused. No real face, real UI, or real clinic on the page.

**Deterministic scan:** **0 findings (exit 0)** across 6 files. No ignores/suppression. Detector false-negative class unchanged (only reads inline `font-family`, not the `--font-sans` token). Console 0 errors/0 warnings; **all requests first-party** (only `127.0.0.1:8000`; no Google Fonts, no picsum); `/fonts/onest.woff2` and `/images/landing/hero.jpg` → 200; mobile no overflow; nav CTA 131×44; FAQ 6/6 `aria-expanded`+`aria-controls`+ids; reduced-motion no layout break.

**Visual overlays:** skipped — CLI exposes no live-server command; fallback: detector JSON + Playwright evidence.

## Overall Impression

Honesty and accessibility are now solid and verifiable (self-hosted assets, no external calls, AA on all text, FAQ wired). Score rose 23 → 26. What remains is proof: the page is built to be trusted but shows no real product, real person, or real clinic, and it leans on beta framing five times. Biggest opportunity: one real screenshot + one hard proof line. One P0: verify or soften the security/backup claims.

## What's Working

1. **Domain fluency in the product mock** — correct 8-tooth arch, real legend, correct anatomy labels; strongest single trust device.
2. **Locally-honest trust bar** — "Pago manual, sin pasarela ni comisiones ocultas. Activas tu plan el mismo día." + four rails: the most non-generic copy on the page.
3. **FAQ accessible and concrete** — aria-expanded/controls/id + x-transition, with real specifics.

## Priority Issues

- **[P0] Verify or soften security/backup claims.** FAQ asserts "cifrado en reposo" and "respaldos diarios"; provider line says "Soporte con acuerdo de servicio por escrito". `AGENTS.md` documents a single shared PostgreSQL with no evidence of at-rest encryption or a backup SLA. Fix: confirm against infra; if unverifiable, rewrite to what is true ("conexión cifrada HTTPS/TLS, acceso por roles, exportación completa de tus datos") and drop the unproven claims. `$impeccable harden`
- **[P1] No real product proof.** Hero "app window" is a hand-coded div, not a screenshot; social proof is 4 anonymous quotes. Fix: one genuine anonymized screenshot of the real odontogram/history screen + a hard proof line ("N clínicas en beta desde {mes}") or a short muted product tour. `$impeccable document`
- **[P1] `.reveal` hides ~80% of the page if JS fails.** `.reveal{opacity:0}` revealed only by IntersectionObserver. Fix: render visible by default and add the hidden state from JS, or `<noscript>` override. `$impeccable harden`
- **[P2] Pricing CTAs not vertically aligned.** Measured top stagger Starter/Enterprise/Pro ≈ 76px. Fix: card `flex flex-col`, `<ul>` `flex-1`, CTA `mt-auto`. `$impeccable layout`
- **[P2] Mobile menu: no state semantics and doesn't close on navigation.** Toggle lacks `aria-expanded`; links don't set `mobileOpen=false`. Fix: `x-bind:aria-expanded`, `aria-controls` + id, and `x-on:click` close on each link. `$impeccable adapt`
- **[P3] Instrument Serif shipped but unused on the landing** (~43 KB). Fix: use it (e.g. italic accent) or remove the `@font-face`. `$impeccable typeset`
- **[P3] Testimonials layout + invented monograms.** `columns-*` gives zig-zag order + orphan card; CC/VL/MC/BQ read as invented entities. Fix: 2×2 grid preserving order; drop initials or use a neutral mark; add a lead line under the bare H2. `$impeccable distill`

## Persona Red Flags

**Jordan (first-timer):** never sees the real UI; two co-equal hero buttons stall the decision; trust bar answers a payment question before he knows what he's buying; anonymous beta quotes + no count = "does anyone use this?".

**Riley (stress tester):** tries to falsify "cifrado en reposo", "respaldos diarios", "ahorra 20%... automáticamente", "pacientes ilimitados", "acuerdo de servicio por escrito"; nav label vs section name mismatch; footer "Términos" twice; disables JS → page blank below hero.

**Casey (mobile):** good — no overflow, 44px targets, AA. Bad — scrolls past headline + 2 full-width buttons before any product visual; mobile menu stays open after tapping a link; "Próxima cita 10:30" chip is `hidden sm:flex` so mobile never sees it; CTA duotone reads as noisy blobs at 390px.

## Minor Observations
No `<main>` landmark / skip link; zero `<img>` (all CSS backgrounds, no alt text); `photo-soft` reused 3×; FAQ opacity-only transition (snaps); no FAQ open by default; "downgrades" anglicism; footer boilerplate generic; `animate-blob` not disabled under `prefers-reduced-motion`; `--df-text-muted:#6b665c` token vs inline `#6b6459` mismatch; body color renders `#23282a` (app.css) not the `text-[#211f1b]` class.

## Questions to Consider
1. If the odontogram mock is the best trust asset, why is it hand-coded instead of a real screenshot?
2. You removed invented clinic names — why keep invented monograms?
3. "beta" five times + "sin tarjeta" four times: for a clinic putting patient records in your DB, is opportunity or risk winning?
4. Will "sin fricción" hold if JS fails and 80% of the page renders blank on a slow VE mobile?
5. Is Instrument Serif a real decision not yet applied, or a leftover you're still paying 43 KB for?
