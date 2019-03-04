<?php
declare(strict_types=1);

namespace TddWizard\ExerciseBrands\Test\Integration;

use PHPUnit\Framework\TestCase;

class XdebugTest extends TestCase
{
    public function testXdebugEnabled()
    {
        $this->assertTrue(extension_loaded('xdebug'));
    }
    public function testPhpStormEnvironment()
    {
        $this->assertEquals('serverName=tddwizard', $_ENV['PHP_IDE_CONFIG']);
    }
    public function testXdebugEnvironmentMatchesHostIp()
    {
        $this->markTestSkipped();
        $this->assertEquals('remote_host=' /*TODO check host*/, $_ENV['XDEBUG_CONFIG']);
    }
}
