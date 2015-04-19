<?php
namespace Tuum\Locator;

use League\CommonMark\CommonMarkConverter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Config;

class CommonMark
{
    /**
     * @var CommonMarkConverter
     */
    private $commonMark;

    /**
     * @var Local
     */
    private $docs;

    /**
     * @var Local
     */
    private $cache;

    /**
     * @param CommonMarkConverter $mark
     * @param string              $doc_dir
     * @param string              $cache_dir
     */
    public function __construct($mark, $doc_dir, $cache_dir)
    {
        $this->commonMark = $mark;
        $this->docs       = new Local($doc_dir);
        $this->cache      = new Local($cache_dir);
    }

    /**
     * @param string $doc_dir
     * @param string $cache_dir
     * @return static
     */
    public static function forge($doc_dir, $cache_dir)
    {
        return new static(new CommonMarkConverter(), $doc_dir, $cache_dir);
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
            $mark = $this->docs->read($path);
            $html = $this->commonMark->convertToHtml($mark['contents']);
            $this->cache->write($path, $html, new Config());
            return $html;
        }
        $contents = $this->cache->read($path);
        return $contents['contents'];
    }
}