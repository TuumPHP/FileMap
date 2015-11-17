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
     */
    public function setFound($location)
    {
        $this->location = $location;
        $this->found    = true;
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
     * @param string   $mime
     * @return $this
     */
    public function setResource($resource, $mime)
    {
        $this->resource = $resource;
        $this->mimeType = $mime;
        return $this;
    }

    /**
     * @param string $contents
     * @param string $mime
     * @return $this
     */
    public function setContents($contents, $mime)
    {
        $this->contents = $contents;
        $this->mimeType = $mime;
        return $this;
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