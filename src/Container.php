<?php
namespace Tuum\Locator;

class Container
{
    /**
     * @var LocatorInterface
     */
    public $union;

    /**
     * @var array
     */
    public $container = [];

    /**
     * @param LocatorInterface $union
     */
    public function __construct( $union )
    {
        $this->union = $union;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function config( $dir )
    {
        $this->union->addRoot($dir);
        return $this;
    }

    /**
     * evaluates an php file and returns the evaluated value
     * which are returned from the included file.
     *
     * @param string $file
     * @param array  $data
     * @return mixed|null
     */
    public function evaluate( $file, $data=[] )
    {
        $file .= substr($file,-4)==='.php' ? '' : '.php';
        if( $location = $this->union->locate($file) ) {
            $data['app'] = $this;
            extract($data);
            /** @noinspection PhpIncludeInspection */
            return include( $location );
        }
        return null;
    }

    /**
     * a simple container based on evaluating a file.
     * closures will be evaluated each time.
     *
     * @param string $file
     * @param array  $data
     * @return mixed
     */
    public function get( $file, $data=[] )
    {
        if( array_key_exists( $file, $this->container ) ) {
            $found = $this->container[$file];
            if( $found instanceof \Closure ) {
                return $found($this);
            }
            return $found;
        }
        $this->container[$file] = $this->evaluate( $file, $data );
        return $this->container[$file];
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function set( $name, $value )
    {
        $this->container[$name] = $value;
    }
}