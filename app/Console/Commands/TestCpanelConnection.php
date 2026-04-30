<?php

namespace App\Console\Commands;

use App\Services\CpanelService;
use Illuminate\Console\Command;

class TestCpanelConnection extends Command
{
    protected $signature = 'cpanel:test {--subdomain=test : Subdomain to test}';
    protected $description = 'Test cPanel API connection and subdomain creation';

    public function handle(CpanelService $cpanelService): int
    {
        $this->info('Testing cPanel API Connection...');
        $this->newLine();

        $this->line('Enabled: ' . ($cpanelService->isEnabled() ? 'Yes' : 'No'));
        $this->line('Host: ' . config('services.cpanel.host'));
        $this->line('Username: ' . config('services.cpanel.username'));
        $this->line('Root Domain: ' . config('services.cpanel.root_domain'));
        $this->newLine();

        if (!$cpanelService->isEnabled()) {
            $this->error('cPanel integration is disabled. Set CPANEL_ENABLED=true in .env');
            return Command::FAILURE;
        }

        $subdomain = $this->option('subdomain');
        $rootDomain = config('services.cpanel.root_domain');

        if (!$rootDomain) {
            $this->error('CPANEL_ROOT_DOMAIN not configured in .env');
            return Command::FAILURE;
        }

        $this->info("Attempting to create subdomain: {$subdomain}.{$rootDomain}");
        $this->newLine();

        $success = $cpanelService->createSubdomain($subdomain, $rootDomain);

        if ($success) {
            $this->info("Success! Subdomain {$subdomain}.{$rootDomain} created.");
            $this->info("You can now access: https://{$subdomain}.{$rootDomain}/app/login");
            return Command::SUCCESS;
        }

        $this->error('Failed to create subdomain. Check storage/logs/laravel.log for details.');
        $this->newLine();
        $this->warn('Common issues:');
        $this->warn('1. Verify CPANEL_URL includes port 2083 (e.g., https://yourdomain.com:2083)');
        $this->warn('2. Verify CPANEL_USERNAME is your cPanel username');
        $this->warn('3. Verify CPANEL_TOKEN is a valid API token from cPanel > Security > Manage API Tokens');
        $this->warn('4. Check that your server allows outgoing connections to port 2083');

        return Command::FAILURE;
    }
}
