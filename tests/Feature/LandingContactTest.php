<?php

namespace Tests\Feature;

use App\Mail\AccessRequestReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LandingContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_landing_ofrece_contacto_para_enterprise(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Hablar con el equipo')
            ->assertSee('¿Clínica grande? Hablemos.');
    }

    public function test_la_solicitud_enterprise_se_envia_por_correo(): void
    {
        Mail::fake();

        $this->post('/contacto', [
            'name' => 'Dra. Ana Torres',
            'email' => 'ana@clinica.test',
            'company' => 'Clínica Torres',
            'fleet' => '3 a 5',
            'message' => 'Tenemos dos sedes.',
        ])->assertRedirect(url('/').'#contacto')
            ->assertSessionHas('access_request_sent', true);

        Mail::assertSent(AccessRequestReceived::class, fn (AccessRequestReceived $mail): bool => $mail->request['company'] === 'Clínica Torres');
    }

    public function test_la_solicitud_valida_en_espanol(): void
    {
        Mail::fake();

        $this->post('/contacto', ['name' => '', 'email' => 'no-es-correo', 'company' => '', 'fleet' => ''])
            ->assertSessionHasErrors(['name', 'email', 'company', 'fleet']);

        $this->assertEquals('Completa este campo.', session('errors')->first('name'));

        Mail::assertNothingSent();
    }
}
