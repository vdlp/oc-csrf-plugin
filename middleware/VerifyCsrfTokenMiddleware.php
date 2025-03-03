<?php

declare(strict_types=1);

namespace Vdlp\Csrf\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use October\Rain\Cookie\Middleware\EncryptCookies;
use October\Rain\Flash\FlashBag;
use RuntimeException;
use Throwable;

final class VerifyCsrfTokenMiddleware
{
    public function __construct(
        private Encrypter $encrypter,
        private Redirector $redirector,
        private ResponseFactory $responseFactory,
        private FlashBag $flashBag,
        private Translator $translator,
        private Repository $config,
        private array $excludePaths = []
    ) {
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
            if ($this->config->get('csrf.notify_user_for_expired_page')) {
                $this->flashBag->error($this->translator->get('vdlp.csrf::lang.notify_user_for_expired_page'));
            }

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
        } catch (Throwable) {
            return '';
        }
    }
}
