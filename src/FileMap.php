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
     *
     * @var array
     */
    public $view_extensions = [
        'php'  => 'evaluatePhp',
        'md'   => 'markToHtml',
        'txt'  => 'textToPre',
        'text' => 'textToPre',
    ];

    /**
     * @param LocatorInterface $locator
     * @param null|MarkUp      $mark
     */
    private function __construct($locator, $mark = null)
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
        if ($found->getExtension()) {
            $found = $this->handleEmit($found);
        } else {
            $found = $this->handleView($found);
        }
        return $found;
    }

    /**
     * handles a file with proper extension such as gif, js, etc.
     * returns array of [resource, mime].
     *
     * @param FileInfo $found
     * @return FileInfo
     */
    private function handleEmit($found)
    {
        $emitExt = $this->emit_extensions;
        if ($this->enable_raw) {
            $emitExt = array_merge($emitExt, $this->raw_extensions);
        }
        if (!isset($emitExt[$found->getExtension()])) {
            return $found;
        }
        if (!$file_loc = $this->locator->locate($found->getPath())) {
            return $found;
        }
        $found->setFound($file_loc);
        $mime = $emitExt[$found->getExtension()];
        $fp   = fopen($file_loc, 'r');

        return $found->setResource($fp, $mime);
    }

    /**
     * @param FileInfo $found
     * @return array
     */
    private function handleView($found)
    {
        foreach ($this->view_extensions as $ext => $handler) {
            if ($file_loc = $this->locator->locate($found->getPath() . '.' . $ext)) {
                $found->setFound($file_loc);
                return $this->$handler($found, $ext);
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
        return $found->setContents($contents, 'text/html');
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
        $html = $this->markUp->getHtml($found->getPath() . '.' . $ext);

        return $found->setContents($html, 'text/html');
    }

    /**
     * @param FileInfo $found
     * @param string   $ext
     * @return array
     */
    private function textToPre($found, $ext)
    {
        $file_loc = $this->locator->locate($found->getPath() . '.' . $ext);

        return $found->setContents('<pre class="FileMap__text-to-pre">' . \file_get_contents($file_loc).'</pre>', 'text/html');
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
        $this->textToPre(null, '');
    }
}