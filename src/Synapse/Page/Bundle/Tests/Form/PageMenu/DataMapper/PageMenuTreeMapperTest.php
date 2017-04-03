<?php

namespace Synabpe\Page\Bundle\Tests\Form\PageMenu\DataMapper;

use RecursiveIteratorIterator;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\InheritDataAwareIterator;
use Symfony\Component\Form\Util\OrderedHashMapIterator;
use Synapse\Page\Bundle\Form\PageMenu\DataMapper\PageMenuTreeMapper;
use Prophecy\Argument;
use Synapse\Page\Bundle\Tests\Fixtures\PageMenuFixtures;

class PageMenuTreeMapperTest extends \PHPUnit_Framework_TestCase
{
    const MENU_TREE_FORM_NAME = 'menu_tree';

    /**
     * @return array
     */
    public function treeMenuShouldBeFlattenedProvider()
    {
        return [
            'empty_data' => [[], []],
            'simple_level' => [
                PageMenuFixtures::getFlatenedSimplelevelMenuTreeFixture(),
                PageMenuFixtures::getSimplelevelMenuTreeFixture(),
            ],
            'multi_level' => [
                PageMenuFixtures::getFlatenedMultilevelMenuTreeFixture(),
                PageMenuFixtures::getMultilevelMenuTreeFixture(),
            ],
        ];
    }

    /**
     * @param $expected
     * @param array $menuTree
     * @dataProvider treeMenuShouldBeFlattenedProvider
     */
    public function testTreeMenuShouldBeFlattened(array $expected, array $menuTree)
    {
        $pageMenuTreeMapper = new PageMenuTreeMapper(self::MENU_TREE_FORM_NAME);
        $recursiveFormIterator = $this->createRecursiveFormIterator(empty($menuTree));

        $pageMenuTreeMapper->mapDataToForms($menuTree, $recursiveFormIterator);

        // break test if menu tree is empty : the test is successfull
        // because $form->getData and $form->getConfig were not called
        if (empty($menuTree)) {
            return;
        }

        foreach ($recursiveFormIterator as $form) {
            $this->assertSame($expected, $form->getData());
        }
    }

    /**
     * @return array
     */
    public function flattenedMenuShouldBeConvertedToTreeProvider()
    {
        return [
            'empty_data' => [['menu_tree' => []], []],
            'simple_level' => [
                PageMenuFixtures::getSimplelevelMenuTreeFixture(),
                PageMenuFixtures::getFlatenedSimplelevelMenuTreeFixture(),
            ],
            'multi_level' => [
                PageMenuFixtures::getMultilevelMenuTreeFixture(),
                PageMenuFixtures::getFlatenedMultilevelMenuTreeFixture(),
            ],
        ];
    }

    /**
     * @param array $expected
     * @param array $flattenedMenu
     * @dataProvider flattenedMenuShouldBeConvertedToTreeProvider
     */
    public function testFlattenedMenuShouldBeConvertedToTree(array $expected, array $flattenedMenu)
    {
        $pageMenuTreeMapper = new PageMenuTreeMapper(self::MENU_TREE_FORM_NAME);
        $recursiveFormIterator = $this->createRecursiveFormIterator(false);

        foreach ($recursiveFormIterator as $k => $form) {
            if (self::MENU_TREE_FORM_NAME !== $k) {
                continue;
            }
            $form->setData($flattenedMenu);
        }

        $mappedData = [];

        $pageMenuTreeMapper->mapFormsToData($recursiveFormIterator, $mappedData);

        $this->assertSame($expected['menu_tree'], $mappedData[self::MENU_TREE_FORM_NAME]);
    }

    /**
     * @param $emptyData
     *
     * @return RecursiveIteratorIterator
     */
    protected function createRecursiveFormIterator($emptyData)
    {
        $form = $this->prophesize(FormInterface::class);

        $formConfigInterface = $this->prophesize(FormConfigInterface::class);

        // if menu tree is empty,
        if ($emptyData) {
            $form
                ->setData(Argument::any())
                ->shouldNotBeCalled();
        } else {
            $form
                ->getConfig()
                ->willReturn($formConfigInterface)
                ->shouldBeCalled();
            $form
                ->setData(Argument::type('array'))
                ->will(function ($args) use ($form) {
                    $form->getData()->willReturn($args[0]);
                })
                ->shouldBeCalled();
        }

        $forms = [self::MENU_TREE_FORM_NAME => $form->reveal()];
        $orderedBeys = array_keys($forms);
        $managedCursor = [1];

        return new RecursiveIteratorIterator(
            new InheritDataAwareIterator(
                new OrderedHashMapIterator($forms, $orderedBeys, $managedCursor)
            )
        );
    }
}
