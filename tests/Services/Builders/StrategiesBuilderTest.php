<?php

/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rocketeer\Services\Builders;

use Rocketeer\Strategies\Check\PhpStrategy;
use Rocketeer\TestCases\RocketeerTestCase;

class StrategiesBuilderTest extends RocketeerTestCase
{
    public function testReturnsNullOnUnbuildableStrategy()
    {
        $built = $this->builder->buildStrategy('Check', '');
        $this->assertInstanceOf(PhpStrategy::class, $built);

        $built = $this->builder->buildStrategy('sdffs', '');
        $this->assertNull($built);
    }
}
