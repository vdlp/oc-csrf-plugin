<?php

declare(strict_types=1);

namespace Vdlp\Csrf;

use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use October\Rain\Cookie\Middleware\EncryptCookies;
use RuntimeException;

/**
 * Class VerifyCsrfTokenMiddleware
 *
 * @package Vdlp\Csrf
 */
class VerifyCsrfTokenMiddleware
{
    /**
     * @var Encrypter
     */
    private $encrypter;

    /**
     * @var Redirector
     */
    private $redirector;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * The URIs that should be excluded from CSRF verification.
     * as used by https://github.com/laravel/framework/blob/5.4/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php
     * example use $except = ["stripe/webhooks", "gocardless/webhooks"];
     * @var array
     */
    protected $except = [];

    /**
     * @param Encrypter $encrypter
     * @param Redirector $redirector
     * @param ResponseFactory $responseFactory
     */
    public function __construct(Encrypter $encrypter, Redirector $redirector, ResponseFactory $responseFactory)
    {
        $this->encrypter = $encrypter;
        $this->redirector = $redirector;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|JsonResponse
     * @throws RuntimeException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isReading($request) || $this->tokensMatch($request) || $this->inExceptArray($request)) {
            return $next($request);
        }

        if ($request->ajax()) {
            return $this->responseFactory->json([
                'X_OCTOBER_REDIRECT' => $request->getUri(),
            ]);
        }

        return $this->redirector->refresh();
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isReading($request): bool
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Check the list of exluded URLs to see if we should ignore CSRF for this request
     * as used by https://github.com/laravel/framework/blob/5.4/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php
     * @param Request $request
     * @return bool
     */
    private function inExceptArray(Request $request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->is($except)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws RuntimeException
     */
    private function tokensMatch($request): bool
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($request->session()->token())
            && is_string($token)
            && hash_equals($request->session()->token(), $token);
    }

    /**
     * @param Request $request
     * @return string
     */
    private function getTokenFromRequest($request): string
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($header, EncryptCookies::serialized('XSRF-TOKEN'));
        }

        return (string) $token;
    }
}
