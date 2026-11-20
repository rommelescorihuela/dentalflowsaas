<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DentalFlow — Agenda, odontograma, presupuestos y cobros en una ficha</title>
    <meta name="description" content="Software para clínicas dentales: el odontograma abre el presupuesto, el presupuesto abre el cobro y todo queda en la historia del paciente. 14 días de prueba, sin tarjeta.">
    <meta name="theme-color" content="#faf9f6">
    <meta property="og:type" content="website">
    <meta property="og:title" content="DentalFlow — Del diente marcado al pago cobrado">
    <meta property="og:description" content="Agenda, odontograma, presupuestos, inventario y portal del paciente en un solo sistema.">
    <meta property="og:locale" content="es_LA">
    <meta property="og:image" content="{{ asset('og.png') }}">
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    @vite('resources/css/app.css')
</head>
<body class="landing antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-primary-700 focus:px-4 focus:py-2 focus:text-white">
        Saltar al contenido
    </a>

    <header class="ld-wrap flex items-center justify-between gap-6 py-5">
        <a href="/" class="flex items-center gap-2.5" aria-label="DentalFlow, inicio">
            <svg width="32" height="36" viewBox="0 0 48 54" aria-hidden="true" focusable="false">
                <g transform="rotate(-6 21 25)">
                    <rect x="2" y="4" width="38" height="42" rx="8" fill="#1f9e8f"/>
                    <rect x="7" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>
                    <rect x="22" y="11" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.92"/>
                    <rect x="7" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>
                    <rect x="22" y="28" width="13" height="13" rx="3.5" fill="#ffffff" opacity="0.72"/>
                </g>
            </svg>
            <span class="text-[1.1875rem] font-extrabold tracking-[-0.025em] text-sand-900">Dental<span class="text-primary-500">Flow</span></span>
        </a>

        <nav class="hidden items-center gap-6 text-[0.9375rem] text-sand-700 md:flex" aria-label="Secciones">
            <a href="#dia" class="ld-navlink transition-colors hover:text-sand-900">El día a día</a>
            <a href="#ficha" class="ld-navlink transition-colors hover:text-sand-900">La ficha</a>
            <a href="#planes" class="ld-navlink transition-colors hover:text-sand-900">Planes</a>
            <a href="{{ url('/login') }}" class="ld-btn ld-btn-ghost !px-4 !text-sm">Entrar</a>
            <a href="{{ url('/register') }}" class="ld-btn ld-btn-primary !px-4 !text-sm">Crear mi clínica</a>
        </nav>

        <div class="flex items-center gap-2 md:hidden">
            <a href="{{ url('/register') }}" class="ld-btn ld-btn-primary !px-3.5 !text-sm">Crear clínica</a>
            <details class="ld-menu">
                <summary class="ld-menu-toggle" aria-label="Abrir menú">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                        <path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </summary>
                <nav class="ld-menu-panel" aria-label="Secciones">
                    <a href="#dia">El día a día</a>
                    <a href="#ficha">La ficha</a>
                    <a href="#planes">Planes</a>
                    <a href="{{ url('/login') }}">Entrar</a>
                </nav>
            </details>
        </div>
    </header>

    <main id="contenido">
        {{-- Primer viewport: el odontograma es la pieza central --}}
        <section class="ld-wrap grid items-start gap-10 pb-16 pt-6 lg:grid-cols-12 lg:gap-x-12 lg:pb-24 lg:pt-12">
            <div class="lg:col-span-6">
                <h1 class="ld-display text-[clamp(2.4rem,5vw,4.25rem)]">
                    Del diente marcado al cobro.
                </h1>

                <p class="mt-6 max-w-[46ch] text-[1.0625rem] leading-[1.65] text-sand-700">
                    DentalFlow une agenda, odontograma, presupuestos y cobros en la misma ficha.
                    La clínica deja de escribir el mismo dato tres veces.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ url('/register') }}" class="ld-btn ld-btn-primary">Crear mi clínica</a>
                    <a href="#planes" class="ld-btn ld-btn-ghost">Ver planes</a>
                </div>

                <p class="mt-4 text-[0.8125rem] text-sand-600">14 días de prueba · el registro no pide tarjeta</p>

                <dl class="ld-rule mt-10 hidden gap-px pt-0 sm:grid sm:grid-cols-3">
                    <div class="py-4 pr-4">
                        <dt class="ld-label">Historia</dt>
                        <dd class="mt-1.5 text-[0.9375rem] font-semibold text-sand-900">Una por paciente</dd>
                    </div>
                    <div class="py-4 pr-4 sm:border-l sm:border-sand-200 sm:pl-4">
                        <dt class="ld-label">Presupuesto</dt>
                        <dd class="mt-1.5 text-[0.9375rem] font-semibold text-sand-900">Sale del odontograma</dd>
                    </div>
                    <div class="py-4 pr-4 sm:border-l sm:border-sand-200 sm:pl-4">
                        <dt class="ld-label">Cobro</dt>
                        <dd class="mt-1.5 text-[0.9375rem] font-semibold text-sand-900">Queda en la ficha</dd>
                    </div>
                </dl>
            </div>

            <aside class="lg:col-span-6" aria-labelledby="odontograma-titulo">
                <div class="ld-card overflow-hidden">
                    <div class="flex items-center justify-between gap-3 border-b border-sand-200 px-5 py-4">
                        <div>
                            <p id="odontograma-titulo" class="text-sm font-semibold text-sand-900">Ana R. · Historia 0042</p>
                            <p class="mt-0.5 text-[0.75rem] text-sand-600">Control de 6 meses</p>
                        </div>
                        <span class="ld-chip ld-chip-teal">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="m3 8.5 3.2 3.2L13 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            En tratamiento
                        </span>
                    </div>

                    <div class="px-5 py-5">
                        @php
                            $arriba = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28];
                            $abajo = [48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];
                            $estados = [16 => 'caries', 26 => 'tratado', 36 => 'caries', 48 => 'ausente'];
                            $i = 0;
                        @endphp

                        <div class="ld-arch" role="img" aria-label="Odontograma de ejemplo: 32 piezas, con caries en 16 y 36, tratamiento en 26 y ausencia del 48">
                            @foreach ($arriba as $pieza)
                                <span class="ld-tooth" data-state="{{ $estados[$pieza] ?? 'sano' }}" style="--d: {{ $i++ }}">{{ $pieza }}</span>
                            @endforeach
                            @foreach ($abajo as $pieza)
                                <span class="ld-tooth" data-state="{{ $estados[$pieza] ?? 'sano' }}" style="--d: {{ $i++ }}">{{ $pieza }}</span>
                            @endforeach
                        </div>

                        <div class="ld-legend mt-4">
                            <span><i class="ld-swatch"></i> Sano</span>
                            <span><i class="ld-swatch ld-swatch-caries"></i> Caries</span>
                            <span><i class="ld-swatch ld-swatch-tratado"></i> Tratado</span>
                            <span><i class="ld-swatch ld-swatch-ausente"></i> Ausente</span>
                        </div>
                    </div>

                    <div class="border-t border-sand-200 px-5 py-4">
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="text-sm font-semibold text-sand-900">Presupuesto #12</p>
                            <p class="text-[0.75rem] text-sand-600">Aprobado por la paciente</p>
                        </div>
                        <ul class="mt-3 space-y-2 text-[0.875rem]">
                            <li class="flex items-baseline justify-between gap-3">
                                <span class="text-sand-700">Obturación · pieza 16</span>
                                <span class="font-semibold">60 USD</span>
                            </li>
                            <li class="flex items-baseline justify-between gap-3">
                                <span class="text-sand-700">Endodoncia · pieza 36</span>
                                <span class="font-semibold">180 USD</span>
                            </li>
                        </ul>
                        <div class="mt-3 flex items-baseline justify-between gap-3 border-t border-sand-200 pt-3">
                            <span class="text-sm font-semibold text-sand-900">Total</span>
                            <span class="text-[1.25rem] font-bold tracking-[-0.02em] text-sand-900">240 USD</span>
                        </div>
                    </div>
                </div>
                <p class="mt-3 text-right text-[0.75rem] text-sand-600">Odontograma y presupuesto de ejemplo.</p>
            </aside>
        </section>

        {{-- El día: agenda real de una clínica --}}
        <section id="dia" class="border-y border-sand-200 bg-sand-50 py-16 lg:py-20" aria-labelledby="dia-titulo">
            <div class="ld-wrap grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <h2 id="dia-titulo" class="ld-display text-[clamp(1.8rem,3vw,2.4rem)]">
                        Un día en la clínica
                    </h2>
                    <p class="mt-4 max-w-[38ch] text-[0.9375rem] leading-[1.65] text-sand-700">
                        La agenda ordena el sillón; el cobro y la historia se resuelven sin salir de la ficha.
                    </p>
                    <p class="mt-6 text-[0.8125rem] text-sand-600">Agenda de ejemplo.</p>
                </div>

                <div class="lg:col-span-8">
                    <div class="ld-card px-5 py-2">
                        <ul class="ld-agenda">
                            <li>
                                <span class="text-[0.875rem] font-semibold text-sand-900">09:00</span>
                                <span class="text-[0.9375rem] text-sand-800">Ana R. · limpieza y control</span>
                                <span class="ld-chip ld-chip-teal">Confirmada</span>
                            </li>
                            <li>
                                <span class="text-[0.875rem] font-semibold text-sand-900">10:30</span>
                                <span class="text-[0.9375rem] text-sand-800">Luis M. · endodoncia, pieza 36</span>
                                <span class="ld-chip ld-chip-amber">Por cobrar</span>
                            </li>
                            <li>
                                <span class="text-[0.875rem] font-semibold text-sand-900">12:00</span>
                                <span class="text-[0.9375rem] text-sand-800">Sofía G. · primera consulta</span>
                                <span class="ld-chip ld-chip-teal">Confirmada</span>
                            </li>
                            <li>
                                <span class="text-[0.875rem] font-semibold text-sand-900">15:30</span>
                                <span class="text-[0.9375rem] text-sand-800">Marco P. · control de ortodoncia</span>
                                <span class="ld-chip ld-chip-teal">Confirmada</span>
                            </li>
                            <li>
                                <span class="text-[0.875rem] font-semibold text-sand-900">17:00</span>
                                <span class="text-[0.9375rem] text-sand-800">Elena V. · extracción, pieza 48</span>
                                <span class="ld-chip ld-chip-red">Reprogramar</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- La cadena: odontograma → presupuesto → pago --}}
        <section id="ficha" class="ld-wrap py-16 lg:py-24" aria-labelledby="ficha-titulo">
            <div class="grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <h2 id="ficha-titulo" class="ld-display text-[clamp(1.8rem,3vw,2.4rem)]">
                        La ficha no se reescribe
                    </h2>
                    <p class="mt-4 max-w-[40ch] text-[0.9375rem] leading-[1.65] text-sand-700">
                        Cada paso deja el siguiente listo. No hay una planilla aparte para el
                        presupuesto ni un cuaderno para los pagos.
                    </p>
                </div>

                <ol class="lg:col-span-7">
                    <li class="grid grid-cols-[2rem_1fr] gap-3 border-t border-sand-200 py-4">
                        <span class="pt-0.5 text-[0.75rem] font-bold text-primary-700">01</span>
                        <div>
                            <h3 class="font-semibold text-sand-900">Se marca el diente</h3>
                            <p class="mt-1 text-[0.9375rem] leading-[1.6] text-sand-700">El odontograma registra caries, tratamientos y ausencias sobre la pieza exacta.</p>
                        </div>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3 border-t border-sand-200 py-4">
                        <span class="pt-0.5 text-[0.75rem] font-bold text-primary-700">02</span>
                        <div>
                            <h3 class="font-semibold text-sand-900">El presupuesto toma esos ítems</h3>
                            <p class="mt-1 text-[0.9375rem] leading-[1.6] text-sand-700">Con precios de procedimiento de la clínica, en PDF y con aprobación del paciente.</p>
                        </div>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3 border-t border-sand-200 py-4">
                        <span class="pt-0.5 text-[0.75rem] font-bold text-primary-700">03</span>
                        <div>
                            <h3 class="font-semibold text-sand-900">El pago cierra la historia</h3>
                            <p class="mt-1 text-[0.9375rem] leading-[1.6] text-sand-700">Cada cobro queda asociado al tratamiento y al paciente, no a una hoja suelta.</p>
                        </div>
                    </li>
                    <li class="grid grid-cols-[2rem_1fr] gap-3 border-y border-sand-200 py-4">
                        <span class="pt-0.5 text-[0.75rem] font-bold text-primary-700">04</span>
                        <div>
                            <h3 class="font-semibold text-sand-900">El paciente lo ve</h3>
                            <p class="mt-1 text-[0.9375rem] leading-[1.6] text-sand-700">Su portal muestra citas, presupuestos y pagos sin llamar a recepción.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        {{-- Inventario y página propia --}}
        <section class="border-y border-sand-200 bg-sand-50 py-16 lg:py-20" aria-labelledby="extra-titulo">
            <h2 id="extra-titulo" class="sr-only">Inventario, agenda online y portal del paciente</h2>
            <div class="ld-wrap grid gap-6 md:grid-cols-2">
                <article class="ld-card p-6">
                    <h3 class="ld-display text-[1.375rem]">El inventario avisa antes de faltar</h3>
                    <p class="mt-2 text-[0.9375rem] leading-[1.65] text-sand-700">
                        Cada insumo tiene mínimo y consumo. Cuando baja del mínimo, la alerta aparece
                        en el panel antes de que falte en el sillón.
                    </p>
                    <p class="mt-2 text-[0.8125rem] text-sand-600">Disponible en el plan Pro.</p>
                    <ul class="mt-4 space-y-2 text-[0.875rem]">
                        <li class="flex items-baseline justify-between gap-3 border-t border-sand-200 pt-2">
                            <span class="text-sand-800">Anestesia 2 %</span>
                            <span class="ld-chip ld-chip-amber">Quedan 6</span>
                        </li>
                        <li class="flex items-baseline justify-between gap-3 border-t border-sand-200 pt-2">
                            <span class="text-sand-800">Guantes M</span>
                            <span class="ld-chip ld-chip-red">Bajo mínimo</span>
                        </li>
                    </ul>
                </article>

                <article class="ld-card p-6">
                    <h3 class="ld-display text-[1.375rem]">Tu clínica tiene su propia página</h3>
                    <p class="mt-2 text-[0.9375rem] leading-[1.65] text-sand-700">
                        Cada clínica recibe su dirección y una página pública con sus servicios, para
                        que el paciente reserve en línea y la cita entre sola a la agenda.
                    </p>
                    <p class="mt-4 text-[0.875rem] text-sand-700">
                        Incluye portal del paciente para ver citas, presupuestos y pagos.
                    </p>
                    <p class="mt-3 text-[0.8125rem] text-sand-600">Disponible en el plan Pro.</p>
                </article>
            </div>
        </section>

        {{-- Planes: matriz real de config/plans.php --}}
        <section id="planes" class="ld-wrap py-16 lg:py-24" aria-labelledby="planes-titulo">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <div>
                    <h2 id="planes-titulo" class="ld-display text-[clamp(1.8rem,3vw,2.4rem)]">
                        Qué incluye cada plan
                    </h2>
                    <p class="mt-4 max-w-[46ch] text-[0.9375rem] leading-[1.65] text-sand-700">
                        Precios en USD por mes. La prueba de 14 días empieza al crear la clínica.
                    </p>
                </div>
                <p class="text-[0.8125rem] text-sand-600">Enterprise se cotiza según la clínica</p>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="ld-matrix">
                    <caption class="sr-only">Comparación de planes Starter, Pro y Enterprise</caption>
                    <thead>
                        <tr>
                            <th scope="col">Incluye</th>
                            <th scope="col">Starter<br><span class="font-bold text-sand-900">39 USD</span></th>
                            <th scope="col" data-plan="pro">Pro<br><span class="font-bold text-primary-700">89 USD</span></th>
                            <th scope="col">Enterprise<br><span class="font-bold text-sand-900">A medida</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Pacientes</th>
                            <td data-plan="Starter · 39 USD">500</td>
                            <td data-plan="Pro · 89 USD">Sin límite</td>
                            <td data-plan="Enterprise · A medida">Sin límite</td>
                        </tr>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Asientos</th>
                            <td data-plan="Starter · 39 USD">5</td>
                            <td data-plan="Pro · 89 USD">15</td>
                            <td data-plan="Enterprise · A medida">Sin límite</td>
                        </tr>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Odontograma, citas y presupuestos</th>
                            <td data-plan="Starter · 39 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Pro · 89 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Enterprise · A medida"><span class="ld-yes">Sí</span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Pagos y PDF</th>
                            <td data-plan="Starter · 39 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Pro · 89 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Enterprise · A medida"><span class="ld-yes">Sí</span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Portal del paciente</th>
                            <td data-plan="Starter · 39 USD"><span class="ld-no" aria-label="No incluido">—</span></td>
                            <td data-plan="Pro · 89 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Enterprise · A medida"><span class="ld-yes">Sí</span></td>
                        </tr>
                        <tr>
                            <th scope="row" class="font-semibold text-sand-900">Reportes y alerta de inventario</th>
                            <td data-plan="Starter · 39 USD"><span class="ld-no" aria-label="No incluido">—</span></td>
                            <td data-plan="Pro · 89 USD"><span class="ld-yes">Sí</span></td>
                            <td data-plan="Enterprise · A medida"><span class="ld-yes">Sí</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ url('/register') }}" class="ld-btn ld-btn-primary">Crear mi clínica</a>
                <p class="text-[0.8125rem] text-sand-600">Sin tarjeta. Puedes cambiar de plan después.</p>
            </div>
        </section>

        {{-- Cierre --}}
        <section class="ld-wrap pb-16 lg:pb-24">
            <div class="ld-band rounded-2xl px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
                <div class="grid items-end gap-8 lg:grid-cols-12">
                    <div class="lg:col-span-8">
                        <h2 class="ld-display !text-white text-[clamp(1.8rem,3.4vw,2.8rem)]">
                            Abre la ficha de tu primera paciente hoy.
                        </h2>
                        <p class="mt-4 max-w-[46ch] text-[0.9375rem] leading-[1.65] text-white/75">
                            Creas la clínica, cargas tus precios de procedimiento y la agenda queda lista.
                        </p>
                    </div>
                    <div class="lg:col-span-4 lg:text-right">
                        <a href="{{ url('/register') }}" class="ld-btn bg-white !text-primary-800 hover:!bg-sand-100">
                            Crear mi clínica
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-sand-200">
        <div class="ld-wrap flex flex-col gap-4 py-8 text-[0.8125rem] text-sand-600 sm:flex-row sm:items-center sm:justify-between">
            <p>DentalFlow · gestión para clínicas dentales</p>
            <nav class="flex items-center gap-5" aria-label="Pie de página">
                <a href="{{ url('/terms') }}" class="ld-footerlink transition-colors hover:text-sand-900">Términos</a>
                <a href="{{ url('/privacy') }}" class="ld-footerlink transition-colors hover:text-sand-900">Privacidad</a>
                <a href="{{ url('/login') }}" class="ld-footerlink transition-colors hover:text-sand-900">Entrar</a>
                <span>{{ date('Y') }}</span>
            </nav>
        </div>
    </footer>
</body>
</html>
