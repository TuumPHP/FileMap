<?php
namespace Tuum\Locator\Handler;

use Tuum\Locator\FileInfo;
use Tuum\Locator\LocatorInterface;

/**
 * Class Emitter
 *
 * for emitting a raw file, such as images and pdf files.
 * sets resources and mime types to FileInfo.
 *
 * @package Tuum\Locator\Handler
 */
class Emitter implements HandlerInterface
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * specify the extension => mime type.
     *
     * @var array
     */
    public $emit_extensions = [
        'pdf'  => 'application/pdf',
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'htm'  => 'text/html',
        'html' => 'text/html',
        'txt'  => 'text/plain',
        'text' => 'text/plain',
        'css'  => 'text/css',
        'js'   => 'text/javascript',
    ];

    /**
     * set to true to allow raw access for text and markdown files.
     *
     * @var bool
     */
    public $enable_raw = false;

    /**
     * raw extensions types.
     *
     * @var array
     */
    public $raw_extensions = [
        'md'       => 'text/plain',
        'markdown' => 'text/plain',
    ];

    /**
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $ext
     * @param string $mimeType
     * @return $this
     */
    public function addEmitExtension($ext, $mimeType)
    {
        $this->emit_extensions[$ext] = $mimeType;
        return $this;
    }

    /**
     * handles a file with proper extension such as gif, js, etc.
     *
     * @param FileInfo $found
     * @return FileInfo
     */
    public function handle($found)
    {
        if (!$mime = $this->getMimeForEmit($found->getExtension())) {
            return $found;
        }
        if (!$file_loc = $this->locator->locate($found->getPath())) {
            return $found;
        }
        $found->setFound($file_loc, $mime);
        $fp   = fopen($file_loc, 'r');

        $found->setResource($fp);
        return $found;
    }

    /**
     * @param string $ext
     * @return string|null
     */
    private function getMimeForEmit($ext)
    {
        $emitExt = $this->emit_extensions;
        if ($this->enable_raw) {
            $emitExt = array_merge($emitExt, $this->raw_extensions);
        }
        return isset($emitExt[$ext]) ? $emitExt[$ext]: null;
    }
}
