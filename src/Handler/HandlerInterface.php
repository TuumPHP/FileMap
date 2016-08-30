<?php
namespace Tuum\Locator\Handler;

use Tuum\Locator\FileInfo;

interface HandlerInterface
{

    /**
     * handles a $file.
     *
     * @param FileInfo $file
     * @return FileInfo
     */
    public function handle($file);
}