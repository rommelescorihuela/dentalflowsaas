<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:process')->dailyAt('06:00')->description('Procesar suscripciones: expirar trials, suspender morosos, enviar notificaciones');
Schedule::command('appointments:send-reminders')->dailyAt('08:00')->description('Enviar recordatorios de citas programadas para mañana');
