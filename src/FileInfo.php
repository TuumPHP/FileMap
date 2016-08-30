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
     * @var string
     */
    private $location;

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
     * FileInfo constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path      = $path;
        $this->extension = pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * 
     */
    public function setFound()
    {
        $this->found    = true;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
        return $this->location;
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