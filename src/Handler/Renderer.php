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
     * @var LocatorInterface
     */
    private $locator;

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
     * @param LocatorInterface $locator
     * @param null|MarkUp      $mark
     */
    public function __construct($locator, $mark = null)
    {
        $this->locator = $locator;
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
     * @param FileInfo $found
     * @return FileInfo
     */
    public function handle($found)
    {
        foreach ($this->view_extensions as $ext => $handler) {
            if ($file_loc = $this->locator->locate($found->getPath($ext))) {
                $found->setFound($file_loc, $handler[1]);
                $handle = $handler[0];
                if (!is_callable($handle)) {
                    $handle = [$this, $handle];
                }
                return call_user_func($handle, $found, $ext);
            }
        }

        return $found;
    }

    /**
     * @param FileInfo $found
     * @return FileInfo
     */
    private function evaluatePhp($found)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $found->getLocation();
        $contents = ob_get_clean();
        $found->setContents($contents);
        return $found;
    }

    /**
     * @param FileInfo $found
     * @param string   $ext
     * @return FileInfo
     */
    private function markToHtml($found, $ext)
    {
        if (!$this->markUp) {
            throw new \InvalidArgumentException('no converter for CommonMark file');
        }
        $html = $this->markUp->getHtml($found->getPath($ext));

        $found->setContents($html);
        return $found;
    }

    /**
     * @param FileInfo $found
     * @return FileInfo
     */
    private function textToPre($found)
    {
        $file_loc = $found->getLocation();

        $found->setContents('<pre class="FileMap__text-to-pre">' . \file_get_contents($file_loc).'</pre>');
        return $found;
    }

    /**
     * dummy method to call private methods which are judged as unused methods.
     *
     * @codeCoverageIgnore
     */
    protected function dummy()
    {
        $this->evaluatePhp(null);
        $this->markToHtml(null, '');
        $this->textToPre(null);
    }
}