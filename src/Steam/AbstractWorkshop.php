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
    protected $browsePerPage = 9;

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

    /**
     * @param string $type
     *
     * @return array
     */
    public function get($type)
    {
        $items = [];

        $nbPage = $this->countBrowsePages($type);
        for ($i = 1; $i <= $nbPage; $i++) {
            $url = $this->getBrowseUrl($type);
            $items = $this->getItems($url);
        }

        return $items;
    }

    /**
     * Get browser url filtered by type
     *
     * @param string $type
     * @param int    $page
     *
     * @return string
     */
    protected function getBrowseUrl($type, $page = 1)
    {
        return sprintf('http://steamcommunity.com/workshop/browse/?appid=%d&requiredtags[]=%s&numperpage=%d&p=%d', $this->getAppId(), $type, $this->browsePerPage, $page);
    }

    /**
     * @param string $type
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function countBrowsePages($type)
    {
        $crawler = new Crawler($this->reader->get($this->getBrowseUrl($type)), $this->getBrowseUrl($type));
        $pageLinkNode = $crawler->filter('#profileBlock > div > div.workshopBrowsePaging > div.workshopBrowsePagingControls > a.pagelink');
        if (!$pageLinkNode->count()) {
            return 1;
        }

        return (int) $pageLinkNode->last()->text();
    }
}
