<?php

/** @noinspection SpellCheckingInspection */
/** @noinspection PhpMissingParentCallCommonInspection */

declare(strict_types=1);

namespace Vdlp\Csrf;

use Cms\Classes\CmsController;
use System\Classes\PluginBase;
use Vdlp\Csrf\Middleware;
use Vdlp\Csrf\ServiceProviders;

/**
 * Class Plugin
 *
 * @package Vdlp\Csrf
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function boot(): void
    {
        CmsController::extend(static function (CmsController $controller) {
            $controller->middleware(Middleware\VerifyCsrfTokenMiddleware::class);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->app->register(ServiceProviders\CsrfServiceProvider::class);
    }

    /**
     * {@inheritDoc}
     */
    public function registerMarkupTags(): array
    {
        return [
            'functions' => [
                'csrf_token' => static function () {
                    return csrf_token();
                },
            ],
        ];
    }
}
