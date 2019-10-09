<?php

declare(strict_types=1);

namespace Vdlp\Csrf\ServiceProviders;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use October\Rain\Support\ServiceProvider;
use Vdlp\Csrf\Middleware\VerifyCsrfTokenMiddleware;

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

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(VerifyCsrfTokenMiddleware::class, static function (): VerifyCsrfTokenMiddleware {
            $excludePaths = array_map(static function (string $path): string {
                return ltrim($path, '/');
            }, config('csrf.exclude_paths', []));

            return new VerifyCsrfTokenMiddleware(
                resolve(Encrypter::class),
                resolve(Redirector::class),
                resolve(ResponseFactory::class),
                $excludePaths
            );
        });
    }
}
