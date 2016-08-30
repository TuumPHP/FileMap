<?php
namespace tests\FileMap;

use Tuum\Locator\FileInfo;
use Tuum\Locator\FileMap;

class FileMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileMap
     */
    public $map;

    function setup()
    {
        $this->map = FileMap::forge(__DIR__.'/map', null);
    }

    /**
     * @test
     */
    function jpg_path_gets_resource()
    {
        $found = $this->map->render('test.jpg');
        $this->assertTrue($found instanceof FileInfo);
        $this->assertTrue(is_resource($found->getResource()));
        $this->assertEquals('image/jpeg', $found->getMimeType());
        $this->assertEquals('test.jpg', basename($found->getLocation()));
    }

    /**
     * @test
     */
    function invalid_ext_path_returns_empty_array()
    {
        $found = $this->map->render('test.bad-ext');
        $this->assertFalse($found->found());
    }

    /**
     * @test
     */
    function php_path_returns_contents()
    {
        $found = $this->map->render('evaluate');
        $this->assertNotEmpty($found);
        $this->assertEquals('tested evaluate', $found->getContents());
        $this->assertEquals('text/html', $found->getMimeType());
    }

    /**
     * @test
     */
    function text_path_returns_contents()
    {
        $found = $this->map->render('text');
        $this->assertNotEmpty($found);
        $this->assertEquals('<pre class="FileMap__text-to-pre">tested text</pre>', $found->getContents());
        $this->assertEquals('text/html', $found->getMimeType());
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
        $this->assertEquals('<h1>tested marked</h1>', trim($found->getContents()));
        $this->assertEquals('text/html', $found->getMimeType());

        $this->assertFalse($map->render('no-such')->found());
    }

    /**
     * @test
     */
    function invalid_view_path_returns_empty_array()
    {
        $found = $this->map->render('bad-path');
        $this->assertEmpty($found->found());
    }

    /**
     * @test
     */
    function enable_raw_outputs_raw_common_mark_text()
    {
        $this->map->emitter->enable_raw = true;
        $found = $this->map->render('marked.md');
        $this->assertEquals('# tested marked', fread($found->getResource(), 1024));
    }

    /**
     * @test
     */
    function addViewExtension_handles_text_differently()
    {
        $this->map->renderer->addViewExtension('text', function(FileInfo $found) {
            $found->setContents(file_get_contents($found->getLocation()).' from closure');
            return $found;
        }, 'text/vanilla');
        $found = $this->map->render('text');
        $this->assertEquals('tested text from closure', $found->getContents());
    }

    /**
     * @test
     */
    function addEmitExtension_with_empty_mime_will_disable_for_the_extension()
    {
        $this->map->emitter->addEmitExtension('jpg', '');
        $found = $this->map->render('test.jpg');
        $this->assertFalse($found->found());
    }
}