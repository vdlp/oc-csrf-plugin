<?php

declare(strict_types=1);

namespace Vdlp\Csrf\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use October\Rain\Cookie\Middleware\EncryptCookies;
use RuntimeException;
use Throwable;

final class VerifyCsrfTokenMiddleware
{
    private Encrypter $encrypter;
    private Redirector $redirector;
    private ResponseFactory $responseFactory;
    private array $excludePaths;

    public function __construct(
        Encrypter $encrypter,
        Redirector $redirector,
        ResponseFactory $responseFactory,
        array $excludePaths = []
    ) {
        $this->encrypter = $encrypter;
        $this->redirector = $redirector;
        $this->responseFactory = $responseFactory;
        $this->excludePaths = $excludePaths;
    }

    /**
     * @return JsonResponse|RedirectResponse|mixed
     * @throws RuntimeException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->isReading($request) || $this->excludePathMatch($request) || $this->tokensMatch($request)) {
            return $next($request);
        }

        if ($request->ajax()) {
            return $this->responseFactory->json([
                'X_OCTOBER_REDIRECT' => $request->getUri(),
            ]);
        }

        return $this->redirector->refresh();
    }

    private function isReading(Request $request): bool
    {
        return in_array($request->method(), ['HEAD', 'GET', 'OPTIONS'], true);
    }

    /**
     * @throws RuntimeException
     */
    private function tokensMatch(Request $request): bool
    {
        $token = $this->getTokenFromRequest($request);

        /** @var mixed $sessionToken */
        $sessionToken = $request->session()->token();

        return is_string($sessionToken)
            && hash_equals($request->session()->token(), $token);
    }

    private function excludePathMatch(Request $request): bool
    {
        return in_array($request->path(), $this->excludePaths, true);
    }

    /**
     * @throws DecryptException
     */
    private function getTokenFromRequest(Request $request): string
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        $header = $request->header('X-XSRF-TOKEN');

        if (($token === null || $token === '') && is_string($header)) {
            $token = $this->encrypter->decrypt($header, EncryptCookies::serialized('XSRF-TOKEN'));
        }

        try {
            return (string) $token;
        } catch (Throwable $e) {
            return '';
        }
    }
}
