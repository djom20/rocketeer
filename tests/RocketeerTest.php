<?php
namespace Rocketeer;

use Rocketeer\TestCases\RocketeerTestCase;

class RocketeerTest extends RocketeerTestCase
{
	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanGetApplicationName()
	{
		$this->assertEquals('foobar', $this->app['rocketeer.rocketeer']->getApplicationName());
	}

	public function testCanGetHomeFolder()
	{
		$this->assertEquals($this->server.'', $this->app['rocketeer.rocketeer']->getHomeFolder());
	}

	public function testCanGetFolderWithStage()
	{
		$this->app['rocketeer.connections']->setStage('test');

		$this->assertEquals($this->server.'/test/current', $this->app['rocketeer.rocketeer']->getFolder('current'));
	}

	public function testCanGetAnyFolder()
	{
		$this->assertEquals($this->server.'/current', $this->app['rocketeer.rocketeer']->getFolder('current'));
	}

	public function testCanReplacePatternsInFolders()
	{
		$folder = $this->app['rocketeer.rocketeer']->getFolder('{path.storage}');

		$this->assertEquals($this->server.'/app/storage', $folder);
	}

	public function testCannotReplaceUnexistingPatternsInFolders()
	{
		$folder = $this->app['rocketeer.rocketeer']->getFolder('{path.foobar}');

		$this->assertEquals($this->server.'/', $folder);
	}

	public function testCanUseRecursiveStageConfiguration()
	{
		$this->swapConfig(array(
			'rocketeer::scm.branch'                   => 'master',
			'rocketeer::on.stages.staging.scm.branch' => 'staging',
		));

		$this->assertEquals('master', $this->app['rocketeer.rocketeer']->getOption('scm.branch'));
		$this->app['rocketeer.connections']->setStage('staging');
		$this->assertEquals('staging', $this->app['rocketeer.rocketeer']->getOption('scm.branch'));
	}

	public function testCanUseRecursiveConnectionConfiguration()
	{
		$this->swapConfig(array(
			'rocketeer::default'                           => 'production',
			'rocketeer::scm.branch'                        => 'master',
			'rocketeer::on.connections.staging.scm.branch' => 'staging',
		));
		$this->assertEquals('master', $this->app['rocketeer.rocketeer']->getOption('scm.branch'));

		$this->swapConfig(array(
			'rocketeer::default'                           => 'staging',
			'rocketeer::scm.branch'                        => 'master',
			'rocketeer::on.connections.staging.scm.branch' => 'staging',
		));
		$this->assertEquals('staging', $this->app['rocketeer.rocketeer']->getOption('scm.branch'));
	}

	public function testRocketeerCanGuessWhichStageHesIn()
	{
		$path = '/home/www/foobar/production/releases/12345678901234/app';
		$stage = Rocketeer::getDetectedStage('foobar', $path);
		$this->assertEquals('production', $stage);

		$path = '/home/www/foobar/staging/releases/12345678901234/app';
		$stage = Rocketeer::getDetectedStage('foobar', $path);
		$this->assertEquals('staging', $stage);

		$path = '/home/www/foobar/releases/12345678901234/app';
		$stage = Rocketeer::getDetectedStage('foobar', $path);
		$this->assertEquals(false, $stage);
	}
}
