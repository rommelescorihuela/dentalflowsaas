---
target: landing de producto DentalFlow (v3)
total_score: 26
max_score: 32
na_heuristics: 7,10
p0_count: 0
p1_count: 0
target_identity: "file:/var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php"
target_fingerprint: "sha256:521b1fccab530217dc920c6f3f37466774bae67679026e7917d01c5946d01a76"
target_path: /var/www/html/extra/dentalflowSaas/resources/views/landing.blade.php
timestamp: 2026-11-20T20-36-31Z
slug: resources-views-landing-blade-php
---
## DentalFlow — crítica v3

**Total: 26/32.** Sube 2 puntos. El mecanismo está probado; el envoltorio y el primer clic son lo que resta.

### Problemas

**[P2] El primer clic a "Entrar" rompe la marca.** `/login` redirige a `/admin/login`: otra tipografía, otro teal, "Entre a su cuenta" frente al "tu clínica" de la landing y sin vuelta al sitio.
→ Tema de login con Outfit/Onest, `primary-700`, marca y "Volver a dentalflow.com".

**[P2] El registro confunde el dato clave.** Se llama "Dirección de la clínica" pero es un subdominio; el sufijo fijo no explica el resultado; el error dice "el campo subdomain ya está en uso" (nombre interno) y la regla de 8 caracteres no se muestra hasta fallar.
→ "Subdominio", ayuda "será tu-clinica.dentalflow.com", mensaje en lenguaje humano y "Mínimo 8 caracteres".

**[P2] La foto del consultorio contradice la marca.** Cuarto oscuro, cables, plástico en el sillón: la única imagen sugiere una clínica vieja, no la calma Lagoon.
→ Foto luminosa o retirarla.

**[P2] La tarjeta de página propia se contradice con la matriz.** Dice "Disponible en el plan Pro" e "incluye portal", pero la matriz marca página pública en Starter y portal solo en Pro.
→ Separar "Página pública: desde Starter" y "Portal: Pro".

**[P3]** La franja de hechos no tiene título visible y comparte fondo con las tarjetas; el contacto deja media tarjeta vacía. "Asientos" vs "sillones" a 300 px. La pieza 48 aparece ausente en el odontograma y con extracción en la agenda.

### Personas
Jordan topa con "dirección" y un dominio fijo, y una contraseña que falla sin regla previa. Riley no sabe si 5 asientos son usuarios o sillones ni qué incluye Enterprise. Casey tiene planes en tarjetas y odontograma legible, pero el formulario está a 6.017 px.
