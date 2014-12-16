<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 14/12/15
 * Time: 17:39
 */
namespace Tuum\Locator;

interface LocatorInterface
{
    /**
     * @param string $root
     */
    public function addRoot( $root );

    /**
     * @param string $file
     * @return bool|string
     */
    public function locate( $file );
}