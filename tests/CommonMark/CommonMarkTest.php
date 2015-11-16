<?php
namespace tests\CommonMap;

use League\Flysystem\Adapter\Local;
use Tuum\Locator\MarkUp;

class CommonMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MarkUp
     */
    private $mark;

    /**
     * @var Local
     */
    private $cache;

    /**
     * @var string
     */
    private $cache_dir;

    /**
     * @var string
     */
    private $marked_file;

    function setup()
    {
        $this->marked_file = __DIR__.'/test.md';
        \file_put_contents($this->marked_file, '# tested');
        $this->cache_dir = __DIR__.'/cache';
        $this->cache = new Local($this->cache_dir);
        $this->mark = MarkUp::forge(__DIR__, $this->cache);
    }

    function tearDown()
    {
        $this->cache->delete('test.md');
        rmdir($this->cache_dir);
        unlink($this->marked_file);
    }

    /**
     * @test
     */
    function convert_md_file()
    {
        $html = $this->mark->getHtml('test.md');
        $this->assertEquals("<h1>tested</h1>\n", $html);
        $this->assertEquals("", $this->mark->getHtml('no-such-md'));
    }

    /**
     * @test
     */
    function uses_cached_html()
    {
        $this->assertEquals("<h1>tested</h1>\n", $this->mark->getHtml('test.md'));
        \file_put_contents($this->marked_file, '# updated');
        touch($this->marked_file, time()+10);
        touch($this->cache_dir.'/test.md', time()+15);
        $this->assertEquals("<h1>tested</h1>\n", $this->mark->getHtml('test.md'));
    }
}
