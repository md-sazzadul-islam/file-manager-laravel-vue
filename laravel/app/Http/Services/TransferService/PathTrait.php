<?php

namespace App\Http\Services\TransferService;

trait PathTrait
{
    
    public function newPath($path, $name)
    {
        if (!$path) {
            return $name;
        }

        return $path.'/'.$name;
    }

    public function renamePath($itemPath, $recipientPath)
    {
        if ($recipientPath) {
            return $recipientPath.'/'.basename($itemPath);
        }

        return basename($itemPath);
    }

    public function transformPath($itemPath, $recipientPath, $partsForRemove)
    {
        $elements = array_slice(explode('/', $itemPath), $partsForRemove);

        if ($recipientPath) {
            return $recipientPath.'/'.implode('/', $elements);
        }

        return implode('/', $elements);
    }
}
