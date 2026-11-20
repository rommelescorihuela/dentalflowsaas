---
version: 1
slug: "resources-views-landing-blade-php"
primary_target: "resources/views/landing.blade.php"
related_targets: []
---

# Landing de producto — DentalFlow

- **Modo:** Persuade. Visitante: dueño de clínica dental pequeña.
- **Trabajo:** que vea el odontograma funcionando como centro del expediente y entienda el
  precio y la prueba de 14 días sin tarjeta.
- **Acción primaria:** crear la clínica (`/register`). Secundaria: ver planes (ancla).

## Direction contract

**THESIS:** la landing muestra el odontograma real como pieza central: del diente marcado
sale el presupuesto y del presupuesto el cobro. Se rechaza el hero con foto de sonrisa y
tarjetas de beneficios.

**OWN-WORLD:** hueso cálido (#f7f5f0) con tinta verde-teal (#1f9e8f) y grafito (#12211f);
líneas finas, aire clínico, esquinas de 14 px solo en las piezas que son instrumento.
Schibsted Grotesk 400/600/700; cifras tabulares en importes y fechas.

**STORY:** el visitante ve su propio sillón (agenda, odontograma, presupuesto, inventario),
comprueba que la clínica entera vive en un solo lugar, compara planes y empieza la prueba.

**FIRST VIEWPORT:** retícula de 12 columnas, 6/6. Izquierda: titular de dos líneas, bajada,
CTA teal + "14 días, sin tarjeta", y una línea de hechos verificables. Derecha: tarjeta de
paciente con odontograma de 32 piezas en dos arcos, leyenda y un presupuesto de ejemplo
etiquetado como ejemplo. En móvil la tarjeta va después del CTA.

**FORM:** tercera de siete (odontograma, agenda, receta, plano de clínica, historia clínica,
inventario, tabla de precios). Elegida porque el odontograma es el mecanismo. `seed: none (unavailable)`.

**FINISH:** unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance.
