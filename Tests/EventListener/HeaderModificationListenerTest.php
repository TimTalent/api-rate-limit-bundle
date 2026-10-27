<?php

/*
 * This file is part of the ApiRateLimitBundle
 *
 * (c) Indra Gunawan <hello@indra.my.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indragunawan\ApiRateLimitBundle\Tests\EventListener;

use Indragunawan\ApiRateLimitBundle\EventListener\HeaderModificationListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HeaderModificationListenerTest extends TestCase
{
    private $kernel;

    protected function setUp(): void
    {
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    protected function tearDown(): void
    {
        $this->kernel = null;
    }

    public function testNotDisplayHeader()
    {
        $request = new Request();
        $request->attributes->set('_api_rate_limit_info', [
            'limit' => '60',
            'remaining' => '59',
            'reset' => gmdate('U'),
        ]);
        $response = new Response();
        $headersBefore = $response->headers->all();

        $event = new ResponseEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        $headerConfig = [
            'display' => false,
        ];

        $listener = new HeaderModificationListener($headerConfig);
        $listener->onKernelResponse($event);

        $this->assertSame($headersBefore, $response->headers->all());
    }

    public function testNoRateLimitInfoAttributes()
    {
        $response = new Response();
        $headersBefore = $response->headers->all();

        $event = new ResponseEvent($this->kernel, new Request(), HttpKernelInterface::MAIN_REQUEST, $response);

        $headerConfig = [
            'display' => true,
            'names' => [
                'limit' => 'X-RateLimit-Limit',
                'remaining' => 'X-RateLimit-Remaining',
                'reset' => 'X-RateLimit-Reset',
            ],
        ];

        $listener = new HeaderModificationListener($headerConfig);
        $listener->onKernelResponse($event);

        $this->assertSame($headersBefore, $response->headers->all());
    }

    public function testSetResponseHeaders()
    {
        $resetTime = gmdate('U');

        $request = new Request();
        $request->attributes->set('_api_rate_limit_info', [
            'limit' => '60',
            'remaining' => '59',
            'reset' => $resetTime,
        ]);

        $response = new Response();

        $event = new ResponseEvent($this->kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        $headerConfig = [
            'display' => true,
            'names' => [
                'limit' => 'X-RateLimit-Limit',
                'remaining' => 'X-RateLimit-Remaining',
                'reset' => 'X-RateLimit-Reset',
            ],
        ];

        $listener = new HeaderModificationListener($headerConfig);
        $listener->onKernelResponse($event);

        $this->assertSame('60', $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame('59', $response->headers->get('X-RateLimit-Remaining'));
        $this->assertSame($resetTime, $response->headers->get('X-RateLimit-Reset'));
    }
}
