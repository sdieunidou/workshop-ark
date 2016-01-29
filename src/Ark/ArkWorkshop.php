<?php

namespace App\Ark;

use App\Steam\AbstractWorkshop;

/**
 * Class ArkWorkshop.
 */
class ArkWorkshop extends AbstractWorkshop
{
    /**
     * {@inheritdoc}
     */
    public function getAppId()
    {
        return 346110;
    }
}
