<?php
namespace Tuum\Locator;

use Tuum\Web\ServiceInterface\RendererInterface;

class Renderer implements RendererInterface
{
    /**
     * @var LocatorInterface
     */
    public $locator;

    /**
     * @var array
     */
    public $services = [];

    /**
     * @param LocatorInterface $locator
     */
    public function __construct($locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $view
     * @return static
     */
    public static function forge($view)
    {
        $locator = new Locator($view);
        return new static($locator);
    }

    /**
     * @param string $name
     * @param mixed  $service
     */
    public function register($name, $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    public function __call($name, $args=[])
    {
        return array_key_exists($name, $this->services) ? $this->services[$name] : null;
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param string $file
     * @param array  $__data
     * @return string
     * @throws \Exception
     */
    public function render($file, $__data = [])
    {
        $__file = $this->locator->locate($file.'.php');
        if( !$__file ) return '';
        try {

            ob_start();
            extract($__data);

            /** @noinspection PhpIncludeInspection */
            include($__file);

            return ob_get_clean();

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }
    }

}