<?php
namespace tests\Locator;

use Tuum\Locator\Container;

require_once(__DIR__ . '/../autoloader.php');

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    var $container;
    
    function setup()
    {
        class_exists(Container::class);
        $this->container = Container::forge();
        $this->container->config(__DIR__.'/config');
    }

    /**
     * @test
     */
    function locator_finds_files_under_config()
    {
        $container = $this->container;
        $this->assertEquals(true, $container->exists('test'));
        $this->assertEquals(true, $container->exists('more'));
        $this->assertEquals(false, $container->exists('tested'));
    }

    /**
     * @test
     */
    function locator_reads_files_under_config()
    {
        $container = $this->container;
        $this->assertEquals('test', $container->evaluate('test'));
        $this->assertEquals('more', $container->evaluate('more'));
        $this->assertEquals(null, $container->evaluate('tested'));
    }

    /**
     * @test
     */
    function locator_finds_files_under_config_and_tested()
    {
        $container = $this->container;
        $container->config(__DIR__.'/config/tested');
        $this->assertEquals(true, $container->exists('test'));
        $this->assertEquals(true, $container->exists('more'));
        $this->assertEquals(true, $container->exists('tested'));
    }

    /**
     * @test
     */
    function locator_reads_files_under_config_and_tested()
    {
        $container = $this->container;
        $container->config(__DIR__.'/config/tested');
        $this->assertEquals('tested test', $container->evaluate('test'));
        $this->assertEquals('more', $container->evaluate('more'));
        $this->assertEquals('tested tested', $container->evaluate('tested'));
    }

    /**
     * @test
     */
    function shared_object_returns_identical_object()
    {
        $container = $this->container;
        $container->share('shared');
        $this->assertTrue(is_object($container->get('shared')));
        $this->assertEquals('shared', $container->get('shared')->name);
        $this->assertSame($container->get('shared'), $container->get('shared'));
    }
}
