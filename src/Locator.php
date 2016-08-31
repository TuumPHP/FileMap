<?php
namespace Tuum\Locator;

class Locator implements LocatorInterface
{
    /**
     * @var string
     */
    private $root;
    
    public function __construct($root)
    {
        if (substr($root, -1) !== '/') {
            $root .= '/';
        }
        $this->root = $root;
    }

    /**
     * @param string $file
     * @return string
     */
    private function getLocation($file)
    {
        $file = substr($file,0,1) ==='/' ? substr($file,1) : $file;
        return $this->root . $file;
    }
    
    /**
     * @param string $file
     * @return bool|string
     */
    public function locate($file)
    {
        $location = $this->getLocation($file);
        if (file_exists($location)) {
            return $location;
        }
        return false;
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isDirectory($file)
    {
        if ($location = $this->locate($file)) {
            return is_dir($location);
        }
        return false;
    }
}