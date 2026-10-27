<?php

/*
 * This file is part of the ApiRateLimitBundle
 *
 * (c) Indra Gunawan <hello@indra.my.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indragunawan\ApiRateLimitBundle\EventListener;

use Indragunawan\ApiRateLimitBundle\Exception\RateLimitExceededException;
use Indragunawan\ApiRateLimitBundle\Service\RateLimitHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author Indra Gunawan <hello@indra.my.id>
 */
class RateLimitListener
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var RateLimitHandler
     */
    private $rateLimitHandler;

    /**
     * @var array
     */
    private $exceptionConfig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(bool $enabled, RateLimitHandler $rateLimitHandler, array $exceptionConfig, TokenStorageInterface $tokenStorage)
    {
        $this->enabled = $enabled;
        $this->rateLimitHandler = $rateLimitHandler;
        $this->exceptionConfig = $exceptionConfig;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->enabled) {
            return;
        }

        // only process on main request
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->has('_api_resource_class')) {
            return;
        }

        $this->rateLimitHandler->handle($request);

        if ($this->rateLimitHandler->isEnabled()) {
            $request->attributes->set('_api_rate_limit_info', $this->rateLimitHandler->getRateLimitInfo());

            if ($this->rateLimitHandler->isRateLimitExceeded()) {
                throw $this->createRateLimitExceededException($request);
            }
        }
    }

    /**
     * Returns an RateLimitExceededException.
     *
     */
    protected function createRateLimitExceededException(Request $request): RateLimitExceededException
    {
        $config = $this->exceptionConfig;
        $class = $config['custom_exception'] ?? RateLimitExceededException::class;
        $username = null;

        if (null !== $token = $this->tokenStorage->getToken()) {
            if (\is_object($token->getUser())) {
                $username = $token->getUserIdentifier();
            }
        }

        return new $class($config['status_code'], $config['message'], $request->getClientIp(), $username);
    }
}
