<?php
namespace Tuum\Locator;

use SplStack;

class Locator implements LocatorInterface
{
    /**
     * @var SplStack|string[]
     */
    protected $dirs = [];

    /**
     * @param string $root
     */
    public function __construct($root = null)
    {
        $this->dirs = [];
        $roots      = func_get_args();
        foreach ($roots as $root) {
            $this->addRoot($root);
        }
    }

    /**
     * @param string $root
     */
    public function addRoot($root)
    {
        if (substr($root, -1) !== '/') {
            $root .= '/';
        }
        $this->dirs = array_merge([$root], $this->dirs);
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function locate($file)
    {
        $file = substr($file,0,1) ==='/' ? substr($file,1) : $file;
        foreach ($this->dirs as $system) {
            $location = $system . $file;
            if (file_exists($location)) {
                return $location;
            }
        }
        return false;
    }
}