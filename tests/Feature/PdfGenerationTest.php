<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\PdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Facades\Tenancy;
use Tests\TestCase;

class PdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTenants();
    }

    public function test_budget_pdf_generation(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        $service = app(PdfService::class);
        $response = $service->generateBudgetPdf($budget);

        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('presupuesto', $response->headers->get('Content-Disposition'));
        $this->assertGreaterThan(1000, strlen($response->getContent()));
    }

    public function test_odontogram_pdf_generation(): void
    {
        $odontogram = $this->createOdontogram($this->patientA, 'completed');
        $this->createClinicalRecord($this->patientA, $odontogram, 11, 'center', 'caries');

        $service = app(PdfService::class);
        $response = $service->generateOdontogramPdf($odontogram);

        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('odontograma', $response->headers->get('Content-Disposition'));
        $this->assertGreaterThan(1000, strlen($response->getContent()));
    }

    public function test_budget_pdf_contains_patient_name(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        $service = app(PdfService::class);
        $response = $service->generateBudgetPdf($budget);

        $this->assertStringContainsString('presupuesto', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString($this->patientA->name, $response->headers->get('Content-Disposition'));
    }

    public function test_odontogram_pdf_contains_patient_name(): void
    {
        $odontogram = $this->createOdontogram($this->patientA, 'completed');

        $service = app(PdfService::class);
        $response = $service->generateOdontogramPdf($odontogram);

        $this->assertStringContainsString('odontograma', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString($this->patientA->name, $response->headers->get('Content-Disposition'));
    }

    public function test_budget_pdf_route_works(): void
    {
        $budget = $this->createBudgetWithItems($this->patientA, 'sent');

        Tenancy::initialize('clinic-a');

        $response = $this->actingAs($this->adminA)->get("/app/budgets/{$budget->id}/pdf");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_odontogram_pdf_route_works(): void
    {
        $odontogram = $this->createOdontogram($this->patientA, 'completed');

        Tenancy::initialize('clinic-a');

        $response = $this->actingAs($this->adminA)->get("/app/odontograms/{$odontogram->id}/pdf");

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }
}
