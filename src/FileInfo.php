<?php
namespace Tuum\Locator;

class FileInfo
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var resource
     */
    private $resource;

    /**
     * @var string
     */
    private $contents;

    /**
     * @var bool
     */
    private $found = false;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var mixed
     */
    private $misc;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * FileInfo constructor.
     *
     * @param LocatorInterface $locator
     * @param string           $path
     */
    public function __construct(LocatorInterface $locator, $path)
    {
        $this->locator   = $locator;
        $this->path      = $path;
        $this->extension = pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param string $path
     * @return FileInfo
     */
    public function withPath($path)
    {
        $self = new self($this->locator, $path);
        return $self;
    }

    /**
     * @param string $ext
     * @return FileInfo
     */
    public function withExtension($ext)
    {
        $ext = $ext ? '.'.$ext: '';
        return $this->withPath($this->path . $ext);
    }

    /**
     * @param null $ext
     * @return bool|string
     */
    public function exists($ext = null)
    {
        $ext = $ext ? '.'.$ext: '';
        return $this->locator->locate($this->path.$ext) !== false;
    }

    /**
     * 
     */
    public function setFound()
    {
        $this->found    = true;
    }

    /**
     * @param string $mime
     */
    public function setMime($mime)
    {
        $this->mimeType = $mime ?: $this->mimeType;
    }

    /**
     * @return bool
     */
    public function found()
    {
        return $this->found !== false;
    }

    /**
     * @param resource $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param string $contents
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @param string $ext
     * @return string
     */
    public function getPath($ext = '')
    {
        $ext = $ext ? '.'.$ext: '';
        return $this->path.$ext;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->locator->locate($this->path);
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        if ($this->resource) {
            return stream_get_contents($this->resource);
        }
        return $this->contents;
    }

    /**
     * @return mixed
     */
    public function getMisc()
    {
        return $this->misc;
    }

    /**
     * @param mixed $misc
     */
    public function setMisc($misc)
    {
        $this->misc = $misc;
    }
}