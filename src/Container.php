<?php
namespace Tuum\Locator;

use Tuum\Web\ServiceInterface\ContainerInterface;

class Container implements ContainerInterface
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
    public function __construct($union)
    {
        $this->union = $union;
    }

    /**
     * @return static
     */
    public static function forge()
    {
        return new static(new Locator());
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function config($dir)
    {
        $this->union->addRoot($dir);
        return $this;
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function locate($file)
    {
        $file .= substr($file, -4) === '.php' ? '' : '.php';
        return $this->union->locate($file);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function exists($file)
    {
        return (bool) $this->locate($file);
    }

    /**
     * evaluates an php file and returns the evaluated value
     * which are returned from the included file.
     *
     * @param string $file
     * @param array  $data
     * @return mixed|null
     */
    public function evaluate($file, $data = [])
    {
        if ($location = $this->locate($file)) {
            extract($data);
            /** @noinspection PhpIncludeInspection */
            return include($location);
        }
        return null;
    }

    /**
     * a simple container based on evaluating a file.
     * closures will be evaluated each time.
     * this method will not share the found object.
     *
     * @param string $file
     * @param array  $data
     * @return mixed
     */
    public function get($file, $data = [])
    {
        $found = $this->fetchFromContainer($file);
        if( $found !== null ) {
            if ($found instanceof \Closure) {
                return $found();
            }
            return $found;
        }
        return $this->evaluate($file, $data);
    }

    /**
     * keep the found object for sharing.
     *
     * @param string $file
     * @param array  $data
     * @return $this
     */
    public function share($file, $data=[])
    {
        $this->container[$file] = $this->evaluate($file, $data);
        return $this;
    }

    /**
     * @param string $file
     * @return bool|mixed
     */    
    public function fetchFromContainer($file)
    {
        if (array_key_exists($file, $this->container)) {
            return $this->container[$file];
        }
        return null;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value)
    {
        $this->container[$name] = $value;
    }
}