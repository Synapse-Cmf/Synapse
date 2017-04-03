<?php

namespace Synapse\Page\Bundle\ViewBuilder;

use Majora\Framework\Model\EntityCollection;
use Symfony\Component\Routing\RouterInterface;
use Synapse\Cmf\Framework\Theme\Component\Model\ComponentInterface;
use Synapse\Page\Bundle\Entity\Page;
use Synapse\Page\Bundle\Loader\Page\LoaderInterface;

class PageMenuViewBuilder implements ViewBuilderInterface
{
    /**
     * @var LoaderInterface
     */
    protected $pageLoader;

    /**
     * PageMenuViewBuilder constructor.
     *
     * @param LoaderInterface $pageLoader
     * @param RouterInterface $router
     */
    public function __construct(LoaderInterface $pageLoader, RouterInterface $router)
    {
        $this->pageLoader = $pageLoader;
        $this->router = $router;
    }

    /**
     * @param ComponentInterface $component
     *
     * @return mixed
     */
    public function buildView(ComponentInterface $component)
    {
        $tree = $component->getData('menu_tree', array());

        if (empty($tree)) {
            return [];
        }

        // extract ids
        $pageIds = [];
        foreach ($tree as $menuTree) {
            $pageIds = array_merge($pageIds, $this->extractPageIds($menuTree));
        }
        $pageIds = array_unique($pageIds);

        // load pages
        $pages = $this->loadPages($pageIds);

        if (0 == $pages->count()) {
            return [];
        }

        // build view structure
        return array_filter(array_map(function ($menuData) use ($pages) {
            return $this->buildMenu($menuData, $pages);
        }, $tree), function ($a) {
            return (bool) count($a);
        });
    }

    /**
     * @param array $tree
     *
     * @return array|mixed
     */
    protected function extractPageIds(array $tree)
    {
        if (empty($tree['tree'])) {
            return [$tree['menu']['page']];
        }

        return array_reduce($tree['tree'], function ($r, $subTree) {
            $r = array_merge($r, $this->extractPageIds($subTree));

            return $r;
        }, [$tree['menu']['page']]);
    }

    /**
     * @param array $pageIds
     *
     * @return \Majora\Framework\Model\EntityCollection
     */
    protected function loadPages(array $pageIds)
    {
        if (empty($pageIds)) {
            return new EntityCollection();
        }

        return $this
                ->pageLoader
                ->retrieveAll([
                    'id' => $pageIds,
                    'online' => true,
                ])
                ->indexBy('id');
    }

    /**
     * @param array            $tree
     * @param EntityCollection $pages
     *
     * @return array
     */
    protected function buildMenu(array $tree, EntityCollection $pages)
    {
        $menu = $tree['menu'];

        if (!$pages->containsKey($menu['page'])) {
            return [];
        }

        return [
            'menu' => $this->createMenuItem($menu, $pages->get($menu['page'])),
            'tree' => array_filter(array_map(function ($menuTree) use ($pages) {
                return $this->buildMenu($menuTree, $pages);
            }, $tree['tree']), function ($a) {
                return (bool) count($a);
            }),
        ];
    }

    /**
     * @param array $menuData
     * @param Page  $page
     *
     * @return array
     */
    protected function createMenuItem(array $menuData, Page $page)
    {
        return [
            'label' => $menuData['label'],
            'title' => $page->getTitle(),
            'url' => $this->router->generate('synapse_content_type_page', ['path' => $page->getPath()]),
        ];
    }
}
