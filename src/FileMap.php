<?php
namespace Tuum\Locator;

use Tuum\Locator\Handler\Emitter;
use Tuum\Locator\Handler\HandlerInterface;
use Tuum\Locator\Handler\Renderer;

class FileMap
{
    /**
     * @var Emitter
     */
    public  $emitter;

    /**
     * @var Renderer
     */
    public  $renderer;

    /**
     * @var HandlerInterface[]
     */
    private $handlers;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * a handler in case $path points to a directory.
     * callable(FileInfo $file): FileInfo
     *
     * @var callable
     */
    public $dirHandler;

    /**
     * default directory index file name in case $path
     * points to a directory. i.e.
     * $path = $path . '/' . $directoryIndex.
     *
     * @var string
     */
    public $directoryIndex = 'index';

    /**
     * @param LocatorInterface $locator
     * @param Emitter          $emitter
     * @param Renderer         $renderer
     */
    public function __construct(LocatorInterface $locator, $emitter, $renderer)
    {
        $this->locator    = $locator;
        $this->emitter    = $emitter;
        $this->renderer   = $renderer;
        $this->dirHandler = [$this, 'handleDirectory'];
    }

    /**
     * @param HandlerInterface $handle
     */
    public function addHandler(HandlerInterface $handle)
    {
        $this->handlers[] = $handle;
    }

    /**
     * @param string $docs_dir
     * @param string $cache_dir
     * @return FileMap
     */
    public static function forge($docs_dir, $cache_dir = null)
    {
        $locator = new Locator($docs_dir);
        $markUp  = is_null($docs_dir) ?
            null:
            MarkUp::forge($docs_dir, $cache_dir);

        return new FileMap(
            $locator,
            new Emitter(),
            new Renderer($markUp)
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
        $path       = trim($path, "/ \t\n\r\x0B");
        $file       = new FileInfo($this->locator, $path);
        if ($file->isDir()) {
            $file = call_user_func($this->dirHandler, $file);
        }

        /** @var HandlerInterface[] $handlers */
        $handlers   = $this->handlers;
        $handlers[] = $this->emitter;
        $handlers[] = $this->renderer;

        foreach($handlers as $handle) {
            $file = $handle->handle($file);
            if ($file->found()) {
                return $file;
            }
        }
        return $file;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    public function handleDirectory($file)
    {
        return $file->withPath($file->getPath().'/'.$this->directoryIndex);
    }
}