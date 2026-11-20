---
target: landing de producto DentalFlow (v2)
total_score: 24
max_score: 32
na_heuristics: 7,10
p0_count: 1
p1_count: 2
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php"
target_fingerprint: "sha256:f43b4e61e5a6308003eaff360d2de14e486347f7460bd11d36ccbdb13e93e1eb"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php
timestamp: 2026-11-20T19-46-28Z
slug: resources-views-landing-blade-php
---
## DentalFlow — crítica v2

**Especificidad:** núcleo autorado (odontograma → presupuesto → pago), carcasa de SaaS genérica (tarjetas, chips pastel, banda teal).

**Total: 24/32 (75 %) — Bueno (borde inferior).** Baja 1 punto: la matriz móvil empeoró al repetir el precio en cada celda y el funnel se evaluó hasta el clic.

### Problemas

**[P0] El único CTA primario lleva a un registro en inglés y sin logo.** `/register` dice "Create your Clinic", "Start your 14-day free trial", "REGISTER CLINIC", con un bloque blanco vacío donde debería ir la marca. La confianza de 3.300 px se pierde en el clic. → traducir el registro y envolverlo en la capa de la landing.

**[P1] En móvil la matriz no permite comparar.** Cada fila se convierte en "Starter · 39 USD: 500 / Pro · 89 USD: …" y se repite 6 veces: 18 líneas con el precio 18 veces. → tres tarjetas de plan o scroll horizontal con primera columna fija.

**[P1] El plan más grande no tiene puerta.** Enterprise "A medida" sin botón, correo ni teléfono. → "Hablar con el equipo" con canal real.

**[P2] La promesa del hero no está en el plan de entrada.** El paso 04 (portal del paciente) es Pro y la matriz no tiene fila de página pública ni reserva online.

**[P2] Faltan las objeciones del dueño:** dónde quedan los datos, qué pasa con lo que tiene en papel, y con quién habla si algo falla.

**[P3]** "En tratamiento" con check; agenda mezcla estado de cita, de cobro y acción; "Crear clínica" vs "Crear mi clínica"; "14 días" y "sin tarjeta" repetidos; el login abre oscuro y con el logo partido.

### Personas
Jordan hace clic en "Crear mi clínica" y recibe una pantalla en inglés sin logo; después le piden un subdominio que nadie explicó. Riley quiere Enterprise y no encuentra cómo pedirlo. Casey ve el odontograma bien, pero no puede comparar planes.
