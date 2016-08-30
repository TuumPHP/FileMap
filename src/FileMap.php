<?php
namespace Tuum\Locator;

use Tuum\Locator\Handler\Emitter;

class FileMap
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
     * @var Emitter
     */
    public $emitter;

    /**
     * @param LocatorInterface $locator
     * @param Emitter          $emitter
     * @param null|MarkUp      $mark
     */
    public function __construct($locator, $emitter, $mark = null)
    {
        $this->locator = $locator;
        $this->emitter = $emitter;
        $this->markUp  = $mark;
    }

    /**
     * @param string $docs_dir
     * @param string $cache_dir
     * @return FileMap
     */
    public static function forge($docs_dir, $cache_dir = null)
    {
        $locator = new Locator($docs_dir);
        return new FileMap(
            $locator,
            new Emitter($locator),
            is_null($docs_dir) ?
                null:
                MarkUp::forge($docs_dir, $cache_dir)
        );
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
     * returns the document contents as array of [contents, mime-type].
     * the contents maybe a resource or a text.
     *
     * returns empty array, [], if no file is mapped.
     *
     * @param string $path
     * @return FileInfo
     */
    public function render($path)
    {
        $found = new FileInfo($path);
        $found = $this->emitter->handle($found);
        if ($found->found()) {
            return $found;
        }
        return $this->handleView($found);
    }

    /**
     * handles text type file, such as html, php, text, and md.
     *
     * @param FileInfo $found
     * @return FileInfo
     */
    private function handleView($found)
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