<?php

/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rocketeer\Tasks;

use Mockery;
use Mockery\MockInterface;
use Rocketeer\Container;
use Rocketeer\Services\Ignition\IgnitionServiceProvider;
use Rocketeer\TestCases\RocketeerTestCase;

class IgniteTest extends RocketeerTestCase
{
    public function testCanIgniteConfigurationOnWindows()
    {
        $this->usesLaravel(false);

        $container = new Container();
        $container->add('path.base', 'E:\workspace\test');
        $container->add('home', $this->home);
        $container->addServiceProvider(new IgnitionServiceProvider());
        $this->container = $container;

        $this->mockFiles(function (MockInterface $mock) {
            return $mock
                ->shouldReceive('files')->andReturn([])
                ->shouldReceive('put')->once()->with('E:/workspace/test/.rocketeer', Mockery::any());
        });

        $this->pretendTask('Ignite')->execute();
    }

    public function testCanIgniteConfiguration()
    {
        $command = $this->getCommand(['ask' => 'foobar']);
        $this->connections->disconnect();
        $this->localStorage->destroy();

        $server = $this->server;
        $this->mock('igniter', 'Configuration', function (MockInterface $mock) use ($server) {
            return $mock
                ->shouldReceive('exportConfiguration')->once()->andReturn($this->server)
                ->shouldReceive('updateConfiguration')->once()->with($this->server, [
                    'host' => '{host}',
                    'username' => '{username}',
                    'password' => '{password}',
                    'connection' => 'production',
                    'root_directory' => dirname($this->server),
                    'scm_repository' => 'https://github.com/'.$this->repository,
                    'scm_username' => null,
                    'scm_password' => null,
                    'application_name' => 'foobar',
                ]);
        });

        $this->assertTaskOutput('Ignite', 'Rocketeer configuration was created', $command);
    }
}
