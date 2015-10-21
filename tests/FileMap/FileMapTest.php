<?php
namespace tests\FileMap;

use Tuum\Locator\FileMap;
use Tuum\Locator\Locator;

class FileMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileMap
     */
    public $map;

    function setup()
    {
        $this->map = new FileMap(new Locator(__DIR__.'/map'));
    }

    /**
     * @test
     */
    function jpg_path_gets_resource()
    {
        $found = $this->map->render('test.jpg');
        $this->assertNotEmpty($found);
        $this->assertTrue(is_resource($found[0]));
        $this->assertEquals('image/jpeg', $found[1]);
        $this->assertEquals('test.jpg', basename(stream_get_meta_data($found[0])["uri"]));
    }

    /**
     * @test
     */
    function invalid_ext_path_returns_empty_array()
    {
        $found = $this->map->render('test.bad-ext');
        $this->assertEmpty($found);
    }

    /**
     * @test
     */
    function php_path_returns_contents()
    {
        $found = $this->map->render('evaluate');
        $this->assertNotEmpty($found);
        $this->assertEquals('tested evaluate', $found[0]);
        $this->assertEquals('text/html', $found[1]);
    }

    /**
     * @test
     */
    function text_path_returns_contents()
    {
        $found = $this->map->render('text');
        $this->assertNotEmpty($found);
        $this->assertEquals('<pre>tested text</pre>', $found[0]);
        $this->assertEquals('text/html', $found[1]);
    }

    /**
     * @test
     */
    function common_mark_converts_and_returns_html()
    {
        $cached_dir = __DIR__.'/cache';
        if (file_exists($cached_dir)) {
            if (file_exists($cached_dir.'/marked.md')) {
                unlink($cached_dir.'/marked.md');
            }
            rmdir($cached_dir);
        }
        $map = FileMap::forge(__DIR__.'/map', $cached_dir);

        $found = $map->render('marked');
        $this->assertEquals('<h1>tested marked</h1>', trim($found[0]));
        $this->assertEquals('text/html', $found[1]);

        $this->assertEquals([], $map->render('no-such'));
    }

    /**
     * @test
     */
    function invalid_view_path_returns_empty_array()
    {
        $found = $this->map->render('bad-path');
        $this->assertEmpty($found);
    }

    /**
     * @test
     */
    function enable_raw_outputs_raw_common_mark_text()
    {
        $this->map->enable_raw = true;
        $found = $this->map->render('marked.md');
        $this->assertEquals('# tested marked', fread($found[0], 1024));
    }
}