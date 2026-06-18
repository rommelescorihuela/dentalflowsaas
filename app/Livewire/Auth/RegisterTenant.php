<?php

namespace App\Livewire\Auth;

use App\Services\TenantService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class RegisterTenant extends Component
{
    public $company_name = '';

    public $subdomain = '';

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'subdomain' => 'required|alpha_dash|min:3|max:50|unique:tenants,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:8|confirmed',
    ];

    public function updatedSubdomain()
    {
        $this->validateOnly('subdomain');
    }

    public function register(TenantService $tenantService)
    {
        $this->validate();

        try {
            $clinic = $tenantService->createTenant([
                'company_name' => $this->company_name,
                'subdomain' => $this->subdomain,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            // Send Welcome Email
            try {
                \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\WelcomeClinic($clinic));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send welcome email: '.$e->getMessage());
            }

            $host = request()->getHost();
            $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1']);

            if ($isLocal) {
                Auth::loginUsingId(
                    \App\Models\User::where('email', $this->email)->firstOrFail()->id
                );

                return redirect('/app');
            }

            return redirect()->route('register.success', ['tenant_id' => $clinic->id]);

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('base', 'Ocurrio un error durante el registro: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.auth.register-tenant')->layout('layouts.guest'); // Assuming a guest layout exists or we will use a simple one
    }
}
