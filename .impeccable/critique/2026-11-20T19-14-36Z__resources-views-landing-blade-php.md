---
target: landing de producto DentalFlow
total_score: 25
max_score: 32
na_heuristics: 7,10
p0_count: 0
p1_count: 3
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php"
target_fingerprint: "sha256:cf061043741dbfa0ae6950ae05ebf0f358f0a49bd1b021211eb933fd9e7de758"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php
timestamp: 2026-11-20T19-14-36Z
slug: resources-views-landing-blade-php
---
## DentalFlow — landing de producto (Persuade)

### Especificidad

**Específica en el artefacto, genérica en el marco (6/10).** El odontograma + presupuesto con total solo puede existir aquí, pero el resto es un esqueleto SaaS estándar (hero partido + tarjetas + matriz + banda oscura). Además el artefacto central es lo más pequeño de la página: 32 celdas de ~26 px con números de 10 px.

### Puntuación heurística

| # | Heurístico | Punt. | Evidencia |
|---|---|---|---|
| 1 | Visibilidad del estado | 3 | Chips y agenda claros; el estado de cada pieza depende del color |
| 2 | Mundo real | 4 | Odontograma, pieza 16/36, presupuesto, cobro |
| 3 | Control y libertad | 4 | Autoservicio real sin tarjeta; términos y privacidad |
| 4 | Consistencia | 3 | Inventario se presenta general pero es Pro |
| 5 | Prevención de errores | 3 | "Sin tarjeta", "puedes cambiar de plan" |
| 6 | Reconocer antes que recordar | 3 | Leyenda correcta; exige saber qué es "pieza 16" |
| 7 | Flexibilidad | n/a | — |
| 8 | Diseño minimalista | 3 | Limpio; cuatro tarjetas de peso idéntico |
| 9 | Recuperación de errores | 2 | Nada sobre fin de prueba o subdominio tomado |
| 10 | Ayuda y documentación | n/a | — |
| **Total** | | **25/32** | **78 % · Bien** |

### Problemas prioritarios

**[P1] En móvil desaparecen todos los precios.**
`.ld-matrix thead { display: none }` bajo 768 px elimina la fila `Starter 39 / Pro 89 / Enterprise a medida`. A 390 px el lector ve límites y "Sí", pero ningún precio: el dato que decide la compra.
*Arreglo:* inyectar nombre y precio por bloque (`data-plan-price`) o renderizar tres bloques de plan antes de la matriz en móvil.

**[P1] La matriz mide 544 px dentro de un contenedor de 350 px.**
El `min-w-[34rem]` sigue activo al apilarse: 194 px de arrastre muerto y contenido que sale por la izquierda al desplazar.
*Arreglo:* `.ld-matrix { min-width: 0 }` bajo 768 px; el `overflow-x-auto` deja de tener sentido.

**[P1] El odontograma se lee solo por color y es el elemento más pequeño.**
Caries (rosa) y tratado (teal) tienen luminancia casi igual; solo `ausente` añade marca no cromática. Contraste medido: pieza 48 a 2,34:1 y 10–11 px (32 hallazgos de texto funcional bajo el piso de 11 px).
*Arreglo:* marca no cromática por estado (punto, esquina, trama), celdas mayores y numeral de la pieza ausente en `sand-600`.

**[P2] El anillo de foco no llega a 3:1.**
`rgba(31,158,143,0.55)` sobre `#faf9f6` = 1,84:1 (WCAG 1.4.11 exige 3:1).
*Arreglo:* foco sólido `primary-700` (≈4,9:1) con offset.

**[P2] El "—" de "no incluido" es invisible y es el único indicador.**
`#d6d0c2` sobre `#faf9f6` = 1,46:1.
*Arreglo:* `sand-600` o icono de guion con borde; nunca solo color claro.

**[P2] El inventario se vende como capacidad general, pero es Pro.**
La tarjeta vecina sí dice "Disponible en el plan Pro"; esta no.
*Arreglo:* misma nota de plan o reescribir el beneficio para todos los planes.

**[P2] La agenda móvil comprime el texto a tres columnas.**
`4.5rem 1fr auto` deja al chip ~90 px; "pieza 36" cae solo en la última línea.
*Arreglo:* dos filas por cita bajo 480 px, chip en la segunda.

**[P3]** Sin "Entrar" en el header móvil. "Pago cobrado" redundante. "Presupuesto 3-12" ambiguo. Enlaces de nav y pie bajo 24×24. CSS heredado sin uso (`dfBlob`, `.glass`, `animate-shimmer`).

### Personas

- **Jordan:** entiende el hero y encuentra el CTA; tropieza con "pieza 16/36" y con "Presupuesto 3-12", que parece un rango de fechas.
- **Riley:** nombres largos rompen el header de la ficha; procedimientos largos desalinean el importe; en móvil la matriz genera scroll lateral.
- **Casey:** ve el odontograma completo, pero el total queda bajo el pliegue y, al llegar a planes, **no hay precios**.
