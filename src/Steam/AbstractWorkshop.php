<?php

namespace App\Steam;

/**
 * Class AbstractWorkshop.
 */
abstract class AbstractWorkshop
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reader = new Reader();
    }

    /**
     * Return the steam app identifier
     *
     * @return int
     */
    abstract public function getAppId();

    /**
     * Return the steam workshop url
     *
     * @return string
     */
    public function getUrl()
    {
        return sprintf('http://steamcommunity.com/app/%d/workshop/', $this->getAppId());
    }

    public function getTypes()
    {

    }
}
