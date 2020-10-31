<?php

namespace App\Http\Services\TransferService;

class TransferFactory
{

    public static function build($disk, $path, $clipboard)
    {
        if ($disk !== $clipboard['disk']) {
            return new ExternalTransfer($disk, $path, $clipboard);
        }

        return new LocalTransfer($disk, $path, $clipboard);
    }
}
