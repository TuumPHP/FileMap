<?php
namespace Tuum\Locator;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class FileFinder implements LocatorInterface
{
    /**
     * @var \SplStack|string[]
     */
    protected $dirs = [ ];

    /**
     * @param string $root
     */
    public function __construct( $root = null )
    {
        $this->dirs = new \SplStack();
        $roots             = func_get_args();
        foreach ( $roots as $root ) {
            $this->addRoot( $root );
        }
    }

    /**
     * @param string $root
     */
    public function addRoot( $root )
    {
        if ( substr( $root, -1 ) !== '/' ) $root .= '/';
        $this->dirs->push( $root );
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function locate( $file )
    {
        foreach ( $this->dirs as $system ) {
            if ( file_exists( $system . $file ) ) {
                return $system . $file;
            }
        }
        return false;
    }
}