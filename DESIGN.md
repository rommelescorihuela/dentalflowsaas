# DESIGN — DentalFlow

Sistema "**Lagoon Clinic**": paleta calma y desaturada, superficies cálidas tipo hueso, un
solo acento (teal lagoon) y arena como neutro. Definido en `resources/css/app.css` y usado
por el panel y por la landing. Este documento fija lo que la landing añade.

## Principio

La landing se comporta como una **ficha clínica**: el odontograma es la pieza central y el
resto de la página explica la cadena que sale de él (diente → presupuesto → pago → historia).
Nada de fotos de sonrisas ni iconos de dientes: el instrumento es la interfaz.

## Tokens (existentes, reutilizados)

| Grupo | Token | Valor |
|---|---|---|
| Acento | `--color-primary-500 / 600 / 700` | `#1f9e8f` / `#157e73` / `#14645c` |
| Neutro cálido | `--color-sand-50 … 900` | `#faf9f6` … `#211f1b` |
| Alerta | `--color-warning-*` | ámbar de inventario bajo |
| Peligro | `--color-danger-*` | caries y estados vencidos |

Superficies: `--df-surface` (#fff), `--df-canvas` (#faf9f6), borde `--color-sand-200`
(#e8e4da). Sombra suave `--df-shadow-soft` (la del sistema, no nueva).

## Tipografía

- **Outfit** para display y **Onest** para texto e interfaz, ambas self-hosted en
  `public/fonts/` y declaradas en `resources/css/fonts.css` (licencia OFL).
- Display: 600–700, `letter-spacing: -0.03em`, `line-height: 1`.
- Cuerpo: 17 px / 1.65, máximo 46 ch.

## Componentes nuevos (capa `.ld-*`)

- `.ld-card` — superficie de 16 px de radio con borde arena y sombra del sistema.
- `.ld-arch` + `.ld-tooth` — odontograma de 32 piezas en dos arcos; 16 columnas en escritorio,
  8 en móvil; estados `sano`, `caries`, `tratado`, `ausente` (borde discontinuo + tachado).
- `.ld-legend` — leyenda con muestras de color y etiqueta textual (no solo color).
- `.ld-agenda` — lista de citas con hora, paciente y chip de estado.
- `.ld-matrix` — matriz de planes; en móvil cada fila se apila con `data-plan`.
- `.ld-band` — cierre en `primary-950` con texto blanco.
- `.ld-btn-primary` / `.ld-btn-ghost` — acciones de 46 px de alto.

## Composición

Hero 6/6: explicación a la izquierda, ficha con odontograma y presupuesto a la derecha. En
móvil la ficha entra en el primer viewport (el rail de tres hechos se oculta). Después:
agenda del día, cadena de la ficha (4 pasos numerados porque el orden importa), inventario y
página por clínica, matriz de planes y cierre.

## Movimiento

Las 32 piezas del odontograma aparecen en cascada (16 ms por celda, escala 0.72→1) una sola
vez al cargar. Bajo `prefers-reduced-motion` no hay animación.

## Accesibilidad

El odontograma es `role="img"` con una descripción que enumera los hallazgos; los estados no
dependen solo del color (leyenda y borde discontinuo). Foco visible en teal con offset 2 px,
skip link, tablas con `caption`/`scope` y contraste ≥ 4.5:1 (sand-600 sobre canvas ≈ 6.4:1).

## Assets

| Asset | Origen |
|---|---|
| `public/fonts/outfit.woff2`, `onest.woff2` | Google Fonts, subset latin, OFL |
| Marca del header (SVG inline) | Autoría propia a partir del icono de `public/images/logo.svg` |
| `public/images/favicon.svg` | Existente del proyecto |
| `public/images/consultorio.webp` | Foto de Harrison Keely, CC BY 4.0, Wikimedia Commons (recortada y convertida a WebP) |

La foto del consultorio se usa como miniatura de la tarjeta «Tu clínica tiene su propia
página», con el crédito en el pie.

El `logo.svg` original no se usa en la landing: su wordmark depende de una tipografía que no
se puede cargar dentro de un `<img>` y su cian (#06b6d4) no es el acento de la marca.
