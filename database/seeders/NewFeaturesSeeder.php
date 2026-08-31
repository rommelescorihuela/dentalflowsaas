<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ClinicSetting;
use App\Models\DashboardBanner;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Rating;
use App\Models\ServiceFeedback;
use App\Models\ToothImage;
use App\Models\ToothNote;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $patients = Patient::all();

        if ($users->isEmpty() || $patients->isEmpty()) {
            $this->command->warn('No hay usuarios o pacientes. Ejecuta primero DatabaseSeeder.');
            return;
        }

        $this->command->info('Sembrando nuevas features...');

        // Obtener la primera clínica para los datos de prueba
        $clinicId = \App\Models\Clinic::first()->id;

        // Mensajes
        $this->command->info('Creando mensajes...');
        for ($i = 0; $i < 10; $i++) {
            Message::factory()->create([
                'clinic_id' => $clinicId,
                'sender_id' => $users->random()->id,
                'receiver_id' => $users->random()->id,
                'patient_id' => $patients->random()->id,
            ]);
        }

        // Calificaciones
        $this->command->info('Creando calificaciones...');
        $appointments = Appointment::all();
        foreach ($appointments->take(5) as $appointment) {
            Rating::create([
                'clinic_id' => $clinicId,
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'rating' => rand(3, 5),
                'comment' => 'Excelente atención, muy profesional',
                'featured' => rand(0, 1),
            ]);
        }

        // Feedback de servicios
        $this->command->info('Creando feedbacks...');
        foreach ($appointments->take(5) as $appointment) {
            ServiceFeedback::create([
                'clinic_id' => $clinicId,
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'rating' => rand(3, 5),
                'comment' => 'Muy satisfecho con el tratamiento',
                'category' => ['atencion', 'limpieza', 'procedimiento'][rand(0, 2)],
            ]);
        }

        // Notificaciones
        $this->command->info('Creando notificaciones...');
        foreach ($users->take(5) as $user) {
            Notification::factory()->create([
                'clinic_id' => $clinicId,
                'user_id' => $user->id,
                'type' => 'appointment',
                'title' => 'Recordatorio de cita',
                'message' => 'Tienes una cita programada para mañana',
            ]);
            Notification::factory()->create([
                'clinic_id' => $clinicId,
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Bienvenido',
                'message' => 'Gracias por usar DentalFlow',
            ]);
        }

        // Dashboard Banners
        $this->command->info('Creando banners...');
        DashboardBanner::factory()->create([
            'clinic_id' => $clinicId,
            'title' => '¡Nuevo! Ahora puedes subir imágenes dentales',
            'message' => 'Sube fotos de antes y después de tus tratamientos',
            'type' => 'info',
            'color' => 'blue',
            'is_active' => true,
        ]);
        DashboardBanner::factory()->create([
            'clinic_id' => $clinicId,
            'title' => 'Promoción especial',
            'message' => '20% de descuento en limpieza dental este mes',
            'type' => 'promo',
            'color' => 'green',
            'is_active' => true,
        ]);

        // Configuración de clínica (si no existe)
        $this->command->info('Creando configuración de clínica...');
        if (!ClinicSetting::where('clinic_id', $clinicId)->exists()) {
            ClinicSetting::factory()->create([
                'clinic_id' => $clinicId,
                'landing_enabled' => true,
                'landing_title' => 'Clínica Dental Sonrisa Perfecta',
                'landing_description' => 'Tu sonrisa es nuestra prioridad. Ofrecemos los mejores tratamientos dentales con tecnología de vanguardia.',
                'landing_phone' => '+56 9 1234 5678',
                'landing_email' => 'contacto@sonrisaperfecta.cl',
                'landing_address' => 'Av. Principal 1234, Santiago, Chile',
                'landing_whatsapp' => '56912345678',
                'primary_color' => '#06b6d4',
                'secondary_color' => '#0891b2',
            ]);
        }

        $this->command->info('✅ Nuevas features sembradas correctamente');
    }
}
