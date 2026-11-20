# PRODUCT — DentalFlow

## Qué es

SaaS multi-clínica para consultorios dentales: agenda, odontograma, presupuestos, pagos,
inventario y portal del paciente. Cada clínica entra por su propio subdominio y tiene su
propia página pública para reservar cita.

## Para quién

Dueño o director de una clínica pequeña (1–3 sillones) en LATAM, con recepción y uno o dos
odontólogos. Escena real: tablet en el sillón, luz natural, la recepcionista cobrando y el
doctor mirando el odontograma antes de explicar el tratamiento.

## Mecanismo único

**El odontograma es el expediente**: del diente marcado sale el presupuesto, del presupuesto
sale el pago y del pago sale el historial. La clínica no reescribe nada en tres sistemas.

## Superficies

| Superficie | Ruta | Estado |
|---|---|---|
| Panel de plataforma | `/admin` | Filament 4 |
| Panel de la clínica | `/app` | Filament 4 |
| Landing pública de cada clínica + reserva | `/landing/{clinic}` | activa |
| Portal del paciente | `/portal` (tenant) | activo |
| Landing de producto | `/` | **esta entrega** |
| Registro de clínica | `/register` | activo |

## Hechos verificados (no inventar otros)

Planes reales (`config/plans.php`):

| Plan | Precio | Asientos | Pacientes |
|---|---|---|---|
| Starter | 39 USD/mes | 5 | 500 |
| Pro | 89 USD/mes | 15 | sin límite |
| Enterprise | a medida | sin límite | sin límite |
| Trial Pro | 0 USD, 14 días | — | — |

- El registro (`/register`) pide nombre de clínica, subdominio, nombre, correo y contraseña.
  **No pide tarjeta.**
- Funciones por plan: odontograma, presupuestos, citas, inventario, pagos, PDF, portal del
  paciente, reportes BI y alerta de inventario bajo (estas dos últimas en Pro).
- Funciones implementadas: citas, historias clínicas, presupuestos, pagos, recetas, precios
  de procedimientos, inventario, mensajes, notificaciones, valoraciones y feedback de servicio.

## Lo que la landing no puede decir

- Cifras de clínicas, pacientes atendidos, reseñas o ahorros.
- Certificaciones, integraciones o cumplimiento normativo que el código no declara.
- Que el plan Enterprise tiene precio publicado (es a medida).
