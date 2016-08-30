<?php
namespace Tuum\Locator\Handler;

use Tuum\Locator\FileInfo;

interface HandlerInterface
{

    /**
     * handles a $file.
     *
     * @param FileInfo $found
     * @return FileInfo
     */
    public function handle($found);
}