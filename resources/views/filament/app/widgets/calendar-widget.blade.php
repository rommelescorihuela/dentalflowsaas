<x-filament::widget>
    <x-filament::section>
        <div x-data="{
                init() {
                    let checkInterval = setInterval(() => {
                        if (typeof FullCalendar !== 'undefined') {
                            clearInterval(checkInterval);
                            let calendarEl = this.$refs.calendar;
                            let calendar = new FullCalendar.Calendar(calendarEl, {
                                initialView: 'timeGridWeek',
                                headerToolbar: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                                },
                                events: @js($this->getEvents()),
                                editable: true,
                                selectable: true,
                                eventDrop: (info) => {
                                    $wire.updateAppointment(
                                        info.event.id,
                                        info.event.start.toISOString(),
                                        info.event.end ? info.event.end.toISOString() : info.event.start.toISOString()
                                    );
                                },
                                eventResize: (info) => {
                                    $wire.updateAppointment(
                                        info.event.id,
                                        info.event.start.toISOString(),
                                        info.event.end.toISOString()
                                    );
                                }
                            });
                            calendar.render();
                        }
                    }, 100);
                }
            }" wire:ignore>
            <div x-ref="calendar"></div>
        </div>

        <!-- Load FullCalendar from CDN for V2 Prototype Simplicity -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    </x-filament::section>
</x-filament::widget>