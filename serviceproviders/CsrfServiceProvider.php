<?php

declare(strict_types=1);

namespace Vdlp\Csrf\ServiceProviders;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Routing\Redirector;
use October\Rain\Flash\FlashBag;
use October\Rain\Support\ServiceProvider as ServiceProviderBase;
use Vdlp\Csrf\Middleware\VerifyCsrfTokenMiddleware;

final class CsrfServiceProvider extends ServiceProviderBase
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config.php' => config_path('csrf.php'),
        ], 'vdlp-csrf-config');

        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'csrf');
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
                    $container->make(FlashBag::class),
                    $container->make(Translator::class),
                    $container->make(Repository::class),
                    $excludePaths
                );
            }
        );
    }
}
