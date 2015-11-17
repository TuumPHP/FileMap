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
     * @param string $location
     * @param string $mime
     */
    public function setFound($location, $mime)
    {
        $this->location = $location;
        $this->found    = true;
        $this->mimeType = $mime ?: $this->mimeType;
    }

    /**
     * @return bool
     */
    public function found()
    {
        return $this->found;
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
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
}