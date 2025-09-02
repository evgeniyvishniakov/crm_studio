<?php

namespace App\Helpers;

class DomainHelper
{
    /**
     * Get the landing domain URL
     */
    public static function getLandingUrl(): string
    {
        return config('domains.landing_url');
    }

    /**
     * Get the CRM domain URL
     */
    public static function getCrmUrl(): string
    {
        return config('domains.crm_url');
    }

    /**
     * Get the admin panel domain URL
     */
    public static function getPanelUrl(): string
    {
        return config('domains.panel_url');
    }

    /**
     * Get the landing domain (without protocol)
     */
    public static function getLandingDomain(): string
    {
        return config('domains.landing');
    }

    /**
     * Get the CRM domain (without protocol)
     */
    public static function getCrmDomain(): string
    {
        return config('domains.crm');
    }

    /**
     * Get the admin panel domain (without protocol)
     */
    public static function getPanelDomain(): string
    {
        return config('domains.panel');
    }

    /**
     * Check if current request is from landing domain
     */
    public static function isLandingDomain(): bool
    {
        return request()->getHost() === self::getLandingDomain();
    }

    /**
     * Check if current request is from CRM domain
     */
    public static function isCrmDomain(): bool
    {
        return request()->getHost() === self::getCrmDomain();
    }

    /**
     * Check if current request is from admin panel domain
     */
    public static function isPanelDomain(): bool
    {
        return request()->getHost() === self::getPanelDomain();
    }

    /**
     * Get all allowed origins for CORS
     */
    public static function getAllowedOrigins(): array
    {
        return config('domains.allowed_origins', []);
    }

    /**
     * Generate URL for specific domain
     */
    public static function url(string $domain, string $path = ''): string
    {
        $baseUrl = match($domain) {
            'landing' => self::getLandingUrl(),
            'crm' => self::getCrmUrl(),
            'panel' => self::getPanelUrl(),
            default => config('app.url')
        };

        return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
    }

    /**
     * Redirect to specific domain
     */
    public static function redirectTo(string $domain, string $path = ''): \Illuminate\Http\RedirectResponse
    {
        return redirect(self::url($domain, $path));
    }
}
