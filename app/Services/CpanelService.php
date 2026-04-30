<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CpanelService
{
    private string $host;
    private string $username;
    private string $token;
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = (bool) config('services.cpanel.enabled', false);
        $this->host = rtrim(config('services.cpanel.host'), '/');
        $this->username = config('services.cpanel.username', '');
        $this->token = config('services.cpanel.token', '');
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function createSubdomain(string $subdomain, string $rootDomain): bool
    {
        if (!$this->enabled) {
            Log::info('cPanel subdomain creation skipped (disabled in config)');
            return true;
        }

        $fullDomain = "{$subdomain}.{$rootDomain}";

        try {
            $response = Http::withBasicAuth($this->username, $this->token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false,
                ])
                ->get("{$this->host}/execute/UAPI/SubDomain/add_subdomain", [
                    'domain' => $subdomain,
                    'rootdomain' => $rootDomain,
                ]);

            $statusCode = $response->status();
            $result = $response->json();

            Log::info("cPanel API response for {$fullDomain}: status={$statusCode}, body=" . json_encode($result));

            if ($result['status'] ?? false) {
                Log::info("cPanel subdomain created: {$fullDomain}");
                return true;
            }

            $error = $result['errors'][0] ?? 'Unknown error';
            Log::error("cPanel failed to create subdomain {$fullDomain}: {$error}");

            return false;
        } catch (\Exception $e) {
            Log::error("cPanel API exception: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteSubdomain(string $subdomain, string $rootDomain): bool
    {
        if (!$this->enabled) {
            return true;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->token)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->withOptions([
                    'verify' => false,
                ])
                ->get("{$this->host}/execute/UAPI/SubDomain/delete_subdomain", [
                    'domain' => "{$subdomain}.{$rootDomain}",
                ]);

            $result = $response->json();

            if ($result['status'] ?? false) {
                Log::info("cPanel subdomain deleted: {$subdomain}.{$rootDomain}");
                return true;
            }

            $error = $result['errors'][0] ?? 'Unknown error';
            Log::error("cPanel failed to delete subdomain {$subdomain}.{$rootDomain}: {$error}");

            return false;
        } catch (\Exception $e) {
            Log::error("cPanel API exception: {$e->getMessage()}");
            return false;
        }
    }
}
