<?php

declare(strict_types=1);

namespace Vdlp\Csrf;

use Cms\Classes\CmsController;
use System\Classes\PluginBase;
use Vdlp\Csrf\Middleware\VerifyCsrfTokenMiddleware;
use Vdlp\Csrf\ServiceProviders\CsrfServiceProvider;

final class Plugin extends PluginBase
{
    public function pluginDetails(): array
    {
        return [
            'name' => 'vdlp.csrf::lang.plugin.name',
            'description' => 'vdlp.csrf::lang.plugin.description',
            'author' => 'Van der Let & Partners <octobercms@vdlp.nl>',
            'icon' => 'icon-link',
            'homepage' => 'https://octobercms.com/plugin/vdlp-csrf',
        ];
    }

    public function boot(): void
    {
        CmsController::extend(static function (CmsController $controller): void {
            $controller->middleware(VerifyCsrfTokenMiddleware::class);
        });
    }

    public function register(): void
    {
        $this->app->register(CsrfServiceProvider::class);
    }

    public function registerMarkupTags(): array
    {
        return [
            'functions' => [
                'csrf_token' => static function (): string {
                    return csrf_token();
                },
            ],
        ];
    }
}
