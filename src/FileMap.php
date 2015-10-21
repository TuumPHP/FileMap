<?php
namespace Tuum\Locator;

class FileMap
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var null|CommonMark
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
     * @param null|CommonMark  $mark
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
    public static function forge($docs_dir, $cache_dir)
    {
        return new FileMap(
            new Locator($docs_dir),
            CommonMark::forge(
                $docs_dir,
                $cache_dir)
        );
    }

    /**
     * returns the document contents as array of [contents, mime-type].
     * the contents maybe a resource or a text.
     *
     * returns empty array, [], if no file is mapped.
     *
     * @param string $path
     * @return array
     */
    public function render($path)
    {
        $ext  = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext) {
            $found = $this->handleEmit($path, $ext);
        } else {
            $found = $this->handleView($path);
        }
        return $found;
    }

    /**
     * handles a file with proper extension such as gif, js, etc.
     * returns array of [resource, mime].
     *
     * @param string $path
     * @param string $ext
     * @return array
     */
    private function handleEmit($path, $ext)
    {
        $emitExt = $this->emit_extensions;
        if ($this->enable_raw) {
            $emitExt = array_merge($emitExt, $this->raw_extensions);
        }
        if (!isset($emitExt[$ext])) {
            return [];
        }
        if (!$file_loc = $this->locator->locate($path)) {
            return [];
        }
        $mime = $emitExt[$ext];
        $fp   = fopen($file_loc, 'r');

        return [$fp, $mime];
    }

    /**
     * @param string $path
     * @return array
     */
    private function handleView($path)
    {
        foreach ($this->view_extensions as $ext => $handler) {
            if ($file_loc = $this->locator->locate($path . '.' . $ext)) {
                $info = [
                    'loc'  => $file_loc,
                    'path' => $path,
                    'ext'  => $ext,
                ];
                return $this->$handler($info);
            }
        }

        return [];
    }

    /**
     * @param array $info
     * @return array
     */
    private function evaluatePhp(array $info)
    {
        ob_start();
        /** @noinspection PhpIncludeInspection */
        include $info['loc'];
        $contents = ob_get_clean();
        return [$contents, 'text/html'];
    }

    /**
     * @param array $info
     * @return array
     */
    private function markToHtml(array $info)
    {
        $path = $info['path'];
        $ext  = $info['ext'];
        if (!$this->markUp) {
            throw new \InvalidArgumentException('no converter for CommonMark file');
        }
        $html = $this->markUp->getHtml($path . '.' . $ext);

        return [$html, 'text/html'];
    }

    /**
     * @param array $info
     * @return array
     */
    private function textToPre(array $info)
    {
        $path     = $info['path'];
        $ext      = $info['ext'];
        $file_loc = $this->locator->locate($path . '.' . $ext);
        return ['<pre>' . \file_get_contents($file_loc) . '</pre>', 'text/html'];
    }

    /**
     * dummy method to call private methods which are judged as unused methods.
     *
     * @codeCoverageIgnore
     */
    protected function dummy()
    {
        $this->evaluatePhp([]);
        $this->markToHtml([]);
        $this->textToPre([]);
    }
}