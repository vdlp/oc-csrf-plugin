<?php

declare(strict_types=1);

namespace Vdlp\Csrf;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Routing\Redirector;
use October\Rain\Support\ServiceProvider as ServiceProviderBase;
use Vdlp\Csrf\Middleware\VerifyCsrfTokenMiddleware;

final class ServiceProvider extends ServiceProviderBase
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('csrf.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->app->bind(
            VerifyCsrfTokenMiddleware::class,
            static function (Container $container): VerifyCsrfTokenMiddleware {
                $excludePaths = array_map(static function (string $path): string {
                    return ltrim($path, '/');
                }, config('csrf.exclude_paths', []));

                return new VerifyCsrfTokenMiddleware(
                    $container->make(Encrypter::class),
                    $container->make(Redirector::class),
                    $container->make(ResponseFactory::class),
                    $excludePaths
                );
            }
        );
    }
}
