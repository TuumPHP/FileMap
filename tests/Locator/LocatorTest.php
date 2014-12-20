<?php
namespace tests\Locator;

use Tuum\Locator\Locator;

require_once(__DIR__ . '/../autoloader.php');

class LocatorTest extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        class_exists(Locator::class);
    }

    /**
     * @test
     */
    function locator_finds_files_under_config()
    {
        $loc = new Locator(__DIR__ . '/config');
        $this->assertEquals(__DIR__.'/config/test.php', $loc->locate('test.php'));
        $this->assertEquals(__DIR__.'/config/more.php', $loc->locate('more.php'));
        $this->assertEquals(false, $loc->locate('tested.txt'));
    }

    /**
     * @test
     */
    function locator_finds_files_under_config_and_tested()
    {
        $loc = new Locator(__DIR__ . '/config');
        $loc->addRoot(__DIR__.'/config/tested');
        $this->assertEquals(__DIR__.'/config/tested/test.php', $loc->locate('test.php'));
        $this->assertEquals(__DIR__.'/config/more.php', $loc->locate('more.php'));
        $this->assertEquals(__DIR__.'/config/tested/tested.php', $loc->locate('tested.php'));
    }
}
