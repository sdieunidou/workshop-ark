<?php

namespace App\Steam;

use Symfony\Component\DomCrawler\Crawler;

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
     * @var int
     */
    protected $browsePerPage = 30;

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

    /**
     * Get availables types
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getTypes()
    {
        $crawler = new Crawler($this->reader->get($this->getUrl()), $this->getUrl());

        return $crawler->filter('body > div.apphub_background > div.workshop_home_content > div.right_column > div.panel .filterOption')
                       ->each(function(Crawler $node) {
                           $labelNode = $node->filter('label');
                           $inputNode = $node->filter('input');

                           $label = '';

                           if ($labelNode->count()) {
                               $label = mb_substr($node->text(), 0, mb_strpos($node->text(), '(') -2);
                           }

                           return [
                               'label' => trim($label),
                               'id'    => (int) $inputNode->attr('id'),
                               'slug'  => $inputNode->attr('value'),
                           ];
                       });
    }

    public function get($type)
    {
        $browseUrl = $this->getBrowseUrl($type);
    }

    /**
     * Get browser url filtered by type
     *
     * @param string $type
     *
     * @return string
     */
    protected function getBrowseUrl($type)
    {
        return sprintf('http://steamcommunity.com/workshop/browse/?appid=%d&requiredtags[]=%s&numperpage=%d', $this->getAppId(), $type, $this->browsePerPage);
    }
}
