<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Domains
    |--------------------------------------------------------------------------
    |
    | This file contains the domain configuration for different environments
    | of the application (landing, CRM, admin panel).
    |
    */

    'landing' => env('LANDING_DOMAIN', 'localhost'),
    'crm' => env('CRM_DOMAIN', 'localhost'),
    'panel' => env('PANEL_DOMAIN', 'localhost'),
    
    /*
    |--------------------------------------------------------------------------
    | Domain URLs
    |--------------------------------------------------------------------------
    |
    | Full URLs for each domain with protocol
    |
    */
    
    'landing_url' => env('LANDING_URL', 'http://localhost'),
    'crm_url' => env('CRM_URL', 'http://localhost'),
    'panel_url' => env('PANEL_URL', 'http://localhost'),
    
    /*
    |--------------------------------------------------------------------------
    | Cross-Domain Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for cross-domain requests and redirects
    |
    */
    
    'allowed_origins' => [
        env('LANDING_URL', 'http://localhost'),
        env('CRM_URL', 'http://localhost'),
        env('PANEL_URL', 'http://localhost'),
    ],
];
