<?php

namespace Synapse\Page\Bundle\Tests\ViewBuilder;

use Majora\Framework\Model\EntityCollection;
use Prophecy\Argument;
use Symfony\Component\Routing\RouterInterface;
use Synapse\Cmf\Framework\Theme\Component\Entity\Component;
use Synapse\Cmf\Framework\Theme\ComponentType\Entity\ComponentType;
use Synapse\Page\Bundle\Entity\Page;
use Synapse\Page\Bundle\Loader\Page\LoaderInterface;
use Synapse\Cmf\Framework\Theme\Component\Model\ComponentInterface;
use Synapse\Page\Bundle\Tests\Fixtures\PageMenuFixtures;
use Synapse\Page\Bundle\ViewBuilder\PageMenuViewBuilder;

class PageMenuViewBuilderTest extends \PHPUnit_Framework_TestCase
{
    const COMPONENT_NAME = 'page_menu';

    const DATA_MENU_TREE = 'menu_tree';

    /**
     * @return array
     */
    public function buildViewShouldYieldTheMenuTreeWithRequiredPagesProvider()
    {
        $componentTypePageMenu = (new ComponentType())->setName(self::COMPONENT_NAME);

        return [
            'empty_data' => [
                [],
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData([self::DATA_MENU_TREE => []]),
            ],
            'simple_level' => [
                PageMenuFixtures::getSimplelevelViewMenu(),
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData(PageMenuFixtures::getSimplelevelMenuTreeFixture()),
            ],
            'multi_level' => [
                PageMenuFixtures::getMultilelevelViewMenu(),
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData(PageMenuFixtures::getMultilevelMenuTreeFixture()),
            ],
        ];
    }

    /**
     * @param $expected
     * @param ComponentInterface $menuTreeComponent
     * @dataProvider buildViewShouldYieldTheMenuTreeWithRequiredPagesProvider
     */
    public function testBuildViewShouldYieldTheMenuTreeWithRequiredPages(array $expected, ComponentInterface $menuTreeComponent)
    {
        $menuPageBuilder = $this->getPageMenuViewBuilder(empty($menuTreeComponent->getData()[self::DATA_MENU_TREE]));

        $viewMenu = $menuPageBuilder->buildView($menuTreeComponent);

        $this->assertSame($expected, $viewMenu);
    }

    /**
     * @return array
     */
    public function buildViewShouldOnlyContainsOnlinePagesProvider()
    {
        $componentTypePageMenu = (new ComponentType())->setName(self::COMPONENT_NAME);

        $simpleLevelExcludedPage = 4;
        $multileLevelExcludedPage = 6;

        return [
            'simple_level_4' => [
                array_filter(PageMenuFixtures::getSimplelevelViewMenu(), function ($e) use ($simpleLevelExcludedPage) {
                    return 'page-'.$simpleLevelExcludedPage !== $e['menu']['title'];
                }),
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData(PageMenuFixtures::getSimplelevelMenuTreeFixture()),
                [$simpleLevelExcludedPage],
            ],
            'multi_level_6' => [
                PageMenuFixtures::getMultilelevelViewMenuWithOnlinePagesOnly(),
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData(PageMenuFixtures::getMultilevelMenuTreeFixture()),
                [$multileLevelExcludedPage],
            ],
            'multi_level_all' => [
                [],
                (new Component())
                    ->setComponentType($componentTypePageMenu)
                    ->setData(PageMenuFixtures::getMultilevelMenuTreeFixture()),
                range(0, 30),
            ],
        ];
    }

    /**
     * @param array|null              $expected
     * @param ComponentInterface|null $menuTreeComponent
     * @dataProvider buildViewShouldOnlyContainsOnlinePagesProvider
     */
    public function testBuildViewShouldOnlyContainsOnlinePages(array $expected, ComponentInterface $menuTreeComponent, array $excludePages)
    {
        $menuPageBuilder = $this->getPageMenuViewBuilder(empty($expected), $excludePages);

        $viewMenu = $menuPageBuilder->buildView($menuTreeComponent);

        $this->assertSame($expected, $viewMenu);
    }

    /**
     * @return PageMenuViewBuilder
     */
    protected function getPageMenuViewBuilder($emptyData, array $excludePages = [])
    {
        return new PageMenuViewBuilder(
            $this->getPageLoaderStub($emptyData, $excludePages),
            $this->getRouterStub($emptyData)
        );
    }

    /**
     * @return object
     */
    protected function getPageLoaderStub($emptyData, array $excludePages = [])
    {
        $pageLoader = $this->prophesize(LoaderInterface::class);

        if (!$emptyData || count($excludePages)) {
            $pageLoader
                ->retrieveAll(Argument::type('array'))
                ->willReturn($this->getPagesCollection($excludePages))
                ->shouldBeCalled();
        }

        return $pageLoader->reveal();
    }

    /**
     * @return object
     */
    protected function getRouterStub($emptyData)
    {
        $fakeUrl = PageMenuFixtures::FAKE_PAGE_URL;
        $router = $this->prophesize(RouterInterface::class);

        if (!$emptyData) {
            $router
                ->generate(Argument::type('string'), Argument::type('array'))
                ->willReturn($fakeUrl)
                ->shouldBeCalled();
        }

        return $router->reveal();
    }

    /**
     * @return EntityCollection
     */
    protected function getPagesCollection(array $excludePages = [])
    {
        $pages = array_map(function ($pos) {
            return (new Page())
                ->setId($pos)
                ->setTitle('page-'.$pos);
        }, range(1, 20, 1));

        return new EntityCollection(array_filter($pages, function (Page $page) use ($excludePages) {
            return !in_array($page->getId(), $excludePages);
        }));
    }
}
