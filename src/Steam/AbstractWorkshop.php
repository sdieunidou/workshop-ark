<?php

namespace App\Steam;

/**
 * Class AbstractWorkshop.
 */
abstract class AbstractWorkshop
{
    /**
     * Return the steam app identifier
     *
     * @return int
     */
    abstract public function getAppId();

    public function getAll()
    {
        echo __METHOD__;
    }
}
