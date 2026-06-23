<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class ClinicHelper
{
    public static function getCurrency(): string
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenant = config('tenancy.tenant_model')::find($user->clinic_id);
            }
        }

        if (! $tenant) {
            return 'USD';
        }

        $data = is_array($tenant->data) ? $tenant->data : null;

        if (! $data) {
            $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['currency'] ?? 'USD';
    }

    public static function getCurrencySymbol(): string
    {
        return match (self::getCurrency()) {
            'Bs' => 'Bs ',
            'EUR' => '€',
            'USD' => '$',
            default => '$',
        };
    }

    public static function getTimezone(): string
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenant = config('tenancy.tenant_model')::find($user->clinic_id);
            }
        }

        if (! $tenant) {
            return 'UTC';
        }

        $data = is_array($tenant->data) ? $tenant->data : null;

        if (! $data) {
            $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['timezone'] ?? 'UTC';
    }

    public static function getLogo(): ?string
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenant = config('tenancy.tenant_model')::find($user->clinic_id);
            }
        }

        if (! $tenant) {
            return null;
        }

        $data = is_array($tenant->data) ? $tenant->data : null;

        if (! $data) {
            $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['logo'] ?? null;
    }

    public static function formatMoney(float $amount): string
    {
        return self::getCurrencySymbol().number_format($amount, 2, ',', '.');
    }

    public static function formatMoneyShort(float $amount): string
    {
        return self::getCurrencySymbol().number_format($amount, 0, ',', '.');
    }

    public static function getScheduleStart(): ?string
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenant = config('tenancy.tenant_model')::find($user->clinic_id);
            }
        }

        if (! $tenant) {
            return null;
        }

        $data = is_array($tenant->data) ? $tenant->data : null;

        if (! $data) {
            $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['schedule_start'] ?? null;
    }

    public static function getScheduleEnd(): ?string
    {
        $tenant = tenant();

        if (! $tenant) {
            $user = auth()->user();

            if ($user && $user->clinic_id) {
                $tenant = config('tenancy.tenant_model')::find($user->clinic_id);
            }
        }

        if (! $tenant) {
            return null;
        }

        $data = is_array($tenant->data) ? $tenant->data : null;

        if (! $data) {
            $rawData = DB::table('tenants')->where('id', $tenant->id)->value('data');
            $data = $rawData ? json_decode($rawData, true) : [];
        }

        return $data['schedule_end'] ?? null;
    }
}
