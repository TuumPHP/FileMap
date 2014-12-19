<?php
namespace Tuum\Locator;

use Tuum\Web\ServiceInterface\RendererInterface;

class Renderer implements RendererInterface
{
    /**
     * @var Container
     */
    public $container;

    /**
     * @var array
     */
    public $services = [];

    /**
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
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
    public function __call($name)
    {
        return array_key_exists($name, $this->services) ? $this->services[$name] : null;
    }

    /**
     * a simple renderer for a raw PHP file.
     *
     * @param string $file
     * @param array  $data
     * @throws \Exception
     */
    public function render($file, $data = [])
    {
        try {

            ob_start();
            $this->container->evaluate($file, $data);
            return ob_get_clean();

        } catch (\Exception $e) {

            ob_end_clean();
            throw $e;
        }
    }

}