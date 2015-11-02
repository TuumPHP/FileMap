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
     * @return bool|string
     */
    public function locate($file)
    {
        $file = substr($file,0,1) ==='/' ? substr($file,1) : $file;
        $location = $this->root . $file;
        if (file_exists($location)) {
            return $location;
        }
        return false;
    }
}