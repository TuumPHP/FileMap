<?php
namespace tests\Locator\UnionManager;

use Tuum\Locator\UnionManager;

require_once( __DIR__ . '/../autoloader.php' );

class UnionMgrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnionManager
     */
    protected $mgr;

    function setup()
    {
        $this->mgr = new UnionManager( __DIR__ . '/config' );
    }

    function test0()
    {
        $this->assertEquals( 'Tuum\Locator\UnionManager', get_class( $this->mgr ) );
    }

    /**
     * @test
     */
    function locator_finds_files_under_config()
    {
        $loc = $this->mgr;
        $this->assertEquals(__DIR__.'/config/test.php', $loc->locate('test.php'));
        $this->assertEquals(__DIR__.'/config/more.php', $loc->locate('more.php'));
        $this->assertEquals(false, $loc->locate('tested.php'));
    }
    
    /**
     * @test
     */
    function locator_finds_files_under_config_and_tested()
    {
        $loc = $this->mgr;
        $loc->addRoot(__DIR__.'/config/tested');
        $this->assertEquals(__DIR__.'/config/tested/test.php', $loc->locate('test.php'));
        $this->assertEquals(__DIR__.'/config/more.php', $loc->locate('more.php'));
        $this->assertEquals(__DIR__.'/config/tested/tested.php', $loc->locate('tested.php'));
    }
}
