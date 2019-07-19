<?php

declare(strict_types=1);

namespace Vdlp\Csrf;

use Cms\Classes\CmsController;
use System\Classes\PluginBase;

/**
 * Class Plugin
 *
 * @package Vdlp\Csrf
 */
class Plugin extends PluginBase
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function boot(): void
    {
        CmsController::extend(static function (CmsController $controller) {
            $controller->middleware(VerifyCsrfTokenMiddleware::class);
        });
    }

    /**
     * {@inheritdoc}
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
