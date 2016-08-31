<?php
namespace Tuum\Locator\Handler;

use Tuum\Locator\FileInfo;
use Tuum\Locator\LocatorInterface;
use Tuum\Locator\MarkUp;

/**
 * Class Renderer
 *
 * for rendering a file, such as html and php.
 * text and markdown files are supported.
 *
 * the rendered text are stored as contents.
 *
 * @package Tuum\Locator\Handler
 */
class Renderer implements HandlerInterface
{
    /**
     * @var null|MarkUp
     */
    private $markUp;

    /**
     * for view/template files.
     * array( extension => [ handle, mime-type ], ... )
     *
     * @var array
     */
    public  $view_extensions = [
        'php'  => ['evaluatePhp', 'text/html'],
        'md'   => ['markToHtml', 'text/html'],
        'txt'  => ['textToPre', 'text/html'],
        'text' => ['textToPre', 'text/html'],
    ];

    /**
     * @param null|MarkUp      $mark
     */
    public function __construct($mark = null)
    {
        $this->markUp  = $mark;
    }

    /**
     * @param string          $ext
     * @param string|callable $handle
     * @param string          $mimeType
     * @return $this
     */
    public function addViewExtension($ext, $handle, $mimeType)
    {
        $this->view_extensions[$ext] = [$handle, $mimeType];
        return $this;
    }

    /**
     * handles text type file, such as html, php, text, and md.
     *
     * @param FileInfo $file
     * @return FileInfo
     */
    public function handle($file)
    {
        foreach ($this->view_extensions as $ext => $handler) {
            if ($file->exists($ext)) {
                $file = $file->withExtension($ext);
                $file->setMime($handler[1]);
                $file->setFound();
                $handle = $handler[0];
                if (!is_callable($handle)) {
                    $handle = [$this, $handle];
                }
                return call_user_func($handle, $file);
            }
        }

        return $file;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    private function evaluatePhp($file)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $file->getLocation();
        $contents = ob_get_clean();
        $file->setContents($contents);
        return $file;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    private function markToHtml($file)
    {
        if (!$this->markUp) {
            throw new \InvalidArgumentException('no converter for CommonMark file');
        }
        $html = $this->markUp->getHtml($file->getPath());

        $file->setContents($html);
        return $file;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    private function textToPre($file)
    {
        $file_loc = $file->getLocation();

        $file->setContents('<pre class="FileMap__text-to-pre">' . \file_get_contents($file_loc).'</pre>');
        return $file;
    }

    /**
     * dummy method to call private methods which are judged as unused methods.
     *
     * @codeCoverageIgnore
     */
    protected function dummy()
    {
        $this->evaluatePhp(null);
        $this->markToHtml(null);
        $this->textToPre(null);
    }
}