<?php
namespace Tuum\Locator;

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
    public $view_extensions = [
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
     * @param string $docs_dir
     * @param string $cache_dir
     * @return FileMap
     */
    public static function forge($docs_dir, $cache_dir = null)
    {
        return new FileMap(
            new Locator($docs_dir),
            is_null($cache_dir) ?
                null:
                MarkUp::forge($docs_dir, $cache_dir)
        );
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
        $found = $this->handleEmit($found);
        if ($found->found()) {
            return $found;
        }
        return $this->handleView($found);
    }

    /**
     * handles a file with proper extension such as gif, js, etc.
     *
     * @param FileInfo $found
     * @return FileInfo
     */
    private function handleEmit($found)
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

    /**
     * handles text type file, such as html, php, text, and md.
     *
     * @param FileInfo $found
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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