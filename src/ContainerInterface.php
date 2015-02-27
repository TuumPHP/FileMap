<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 15/01/04
 * Time: 8:05
 */
namespace Tuum\Locator;

interface ContainerInterface
{
    /**
     * a simple container based on evaluating a file.
     * closures will be evaluated each time.
     * this method will not share the found object.
     *
     * @param string $file
     * @param array  $data
     * @return mixed
     */
    public function get($file, $data = []);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function add($name, $value);


    /**
     * keep the found object for sharing.
     *
     * @param string $file
     * @param array  $data
     * @return $this
     */
    public function share($file, $data=[]);
}