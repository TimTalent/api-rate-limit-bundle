<?php

/*
 * This file is part of the ApiRateLimitBundle
 *
 * (c) Indra Gunawan <hello@indra.my.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indragunawan\ApiRateLimitBundle\Tests\Fixtures\Entity;

use Indragunawan\ApiRateLimitBundle\Annotation\ApiRateLimit;

/**
 * This is a dummy entity, rate limited only for POST requests and the "custom_operation" operation.
 */
#[ApiRateLimit(enabled: true, throttle: ['default' => ['limit' => 8, 'period' => 8]], methods: ['POST', 'custom_operation'])]
class MethodRestricted
{
}
