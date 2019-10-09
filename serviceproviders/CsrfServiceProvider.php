<?php

declare(strict_types=1);

namespace Vdlp\Csrf\ServiceProviders;

use October\Rain\Support\ServiceProvider;

/**
 * Class CsrfServiceProvider
 *
 * @package Vdlp\Csrf\ServiceProviders
 */
final class CsrfServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('csrf.php'),
        ], 'config');
    }
}
