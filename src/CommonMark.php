<?php
namespace Tuum\Locator;

use League\CommonMark\CommonMarkConverter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

class CommonMark
{
    /**
     * @var Local
     */
    private $docs;

    /**
     * @var Local
     */
    private $cache;

    /**
     * @param AdapterInterface              $doc_dir
     * @param AdapterInterface              $cache_dir
     */
    public function __construct($doc_dir, $cache_dir)
    {
        $this->docs       = $doc_dir;
        $this->cache      = $cache_dir;
    }

    /**
     * @param string $doc_dir
     * @param string $cache_dir
     * @return static
     */
    public static function forge($doc_dir, $cache_dir)
    {
        if (is_string($doc_dir)) {
            $doc_dir = new Local($doc_dir);
        }
        if (is_string($cache_dir)) {
            $cache_dir = new Local($cache_dir);
        }
        return new static($doc_dir, $cache_dir);
    }

    /**
     * @return CommonMarkConverter
     */
    private function makeMark()
    {
        return new CommonMarkConverter();
    }

    /**
     * converts a commonMark file, $mark_file, to HTML string.
     *
     * @param string $path
     * @return string
     */
    public function getHtml($path)
    {
        if (!$this->docs->has($path)) {
            return '';
        }
        if (!$this->cache->has($path) || $this->docs->getTimestamp($path) > $this->cache->getTimestamp($path)) {
            $text = $this->docs->read($path);
            $mark = $this->makeMark();
            $html = $mark->convertToHtml($text['contents']);
            $this->cache->write($path, $html, new Config());
            return $html;
        }
        $contents = $this->cache->read($path);
        return $contents['contents'];
    }
}