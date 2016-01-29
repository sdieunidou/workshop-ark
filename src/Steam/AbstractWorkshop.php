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
                        ->reduce(function(Crawler $node) {
                            $labelNode = $node->filter('label');
                            $inputNode = $node->filter('input');
                            return $labelNode->count() && $inputNode->count();
                        })
                       ->each(function(Crawler $node) {
                           $inputNode = $node->filter('input');
                           $label = mb_substr($node->text(), 0, mb_strpos($node->text(), '(') -2);

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
    public function get($type = null)
    {
        $items = [];

        $nbPage = $this->countBrowsePages($type);
        for ($page = 1; $page <= $nbPage; $page++) {
            $items = array_merge($items, $this->getBrowseItems($this->getBrowseUrl($type, $page)));
            var_dump($items);die;
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
    public function getBrowseUrl($type = null, $page = 1)
    {
        if (null !== $type) {
            return sprintf('http://steamcommunity.com/workshop/browse/?appid=%d&requiredtags[]=%s&numperpage=%d&p=%d', $this->getAppId(), $type, $this->browsePerPage, $page);
        }

        return sprintf('http://steamcommunity.com/workshop/browse/?appid=%d&numperpage=%d&p=%d', $this->getAppId(), $this->browsePerPage, $page);
    }

    /**
     * @param string $type
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function countBrowsePages($type = null)
    {
        $crawler = new Crawler($this->reader->get($this->getBrowseUrl($type)), $this->getBrowseUrl($type));
        $pageLinkNode = $crawler->filter('#profileBlock > div > div.workshopBrowsePaging > div.workshopBrowsePagingControls > a.pagelink');
        if (!$pageLinkNode->count()) {
            return 1;
        }

        return (int) $pageLinkNode->last()->text();
    }

    /**
     * @param string $url
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getBrowseItems($url)
    {
        $crawler = new Crawler($this->reader->get($url), $url);
        return $crawler->filter('#profileBlock .workshopItem')
                ->each(function(Crawler $node) {
                    if ($node->filter('.fileRating')->count()) {
                        $rating = $node->filter('.fileRating')->attr('src');
                        $rating = preg_replace('/.+\/(.+)-star.png.+/si', '$1', $rating);
                    }

                    $link = $node->filter('a')->attr('href');
                    $id = preg_replace('/.+id=([0-9]+).+/si', '$1', $link);

                    return [
                        'id' => (int) $id,
                        'link' => $link,
                        'picture' => $node->filter('img.workshopItemPreviewImage ')->attr('src'),
                        'name' => $node->filter('.workshopItemTitle')->text(),
                        'rating' => (int) $rating,
                        'authorName' => $node->filter('.workshopItemAuthorName a')->text(),
                        'authorLink' => $node->filter('.workshopItemAuthorName a')->attr('href'),
                    ];
                });
    }
}
