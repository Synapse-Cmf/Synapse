<?php

namespace Synapse\Page\Bundle\Tests\Fixtures;

class PageMenuFixtures
{
    /**
     * @return array
     */
    public static function getFlatenedSimplelevelMenuTreeFixture()
    {
        return [
            [
                'label' => 'HP',
                'page' => 4,
                'parent' => '212414757418737636268',
                'id' => '212414757418737636268',
                'position' => null,
                'level' => 0,
            ],
            [
                'label' => 'LOL',
                'page' => 6,
                'parent' => '170814757418904068535',
                'id' => '170814757418904068535',
                'position' => null,
                'level' => 0,
            ],
            [
                'label' => 'menu 3',
                'page' => 5,
                'parent' => '491414757419032091078',
                'id' => '491414757419032091078',
                'position' => null,
                'level' => 0,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getSimplelevelMenuTreeFixture()
    {
        return [
            'menu_tree' => [
                [
                    'menu' => [

                        'label' => 'HP',
                        'page' => 4,
                        'parent' => '212414757418737636268',
                        'id' => '212414757418737636268',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [],
                ],
                [

                    'menu' => [
                        'label' => 'LOL',
                        'page' => 6,
                        'parent' => '170814757418904068535',
                        'id' => '170814757418904068535',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [],
                ],
                [
                    'menu' => [

                        'label' => 'menu 3',
                        'page' => 5,
                        'parent' => '491414757419032091078',
                        'id' => '491414757419032091078',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getFlatenedMultilevelMenuTreeFixture()
    {
        return [
            [
                'label' => 'HP',
                'page' => 4,
                'parent' => '212414757418737636268',
                'id' => '212414757418737636268',
                'position' => null,
                'level' => 0,
            ],
            [
                'label' => 'e-sport',
                'page' => 5,
                'parent' => '212414757418737636268',
                'id' => '809514757418790392732',
                'position' => '0',
                'level' => 1,
            ],
            [
                'label' => '1',
                'page' => 4,
                'parent' => '809514757418790392732',
                'id' => '909214757419125297148',
                'position' => '0',
                'level' => 2,
            ],
            [
                'label' => '3',
                'page' => 6,
                'parent' => '809514757418790392732',
                'id' => '2591475741913921812',
                'position' => '1',
                'level' => 2,
            ],
            [
                'label' => 'LOL',
                'page' => 6,
                'parent' => '170814757418904068535',
                'id' => '170814757418904068535',
                'position' => null,
                'level' => 0,
            ],
            [
                'label' => 'menu 4 (lol)',
                'page' => 6,
                'parent' => '491414757419032091078',
                'id' => '491414757419032091078',
                'position' => null,
                'level' => 0,
            ],
            [
                'label' => 'test',
                'page' => 4,
                'parent' => '491414757419032091078',
                'id' => '438714757482283036312',
                'position' => '0',
                'level' => 1,
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getMultilevelMenuTreeFixture()
    {
        return [
            'menu_tree' => [
                [
                    'menu' => [

                        'label' => 'HP',
                        'page' => 4,
                        'parent' => '212414757418737636268',
                        'id' => '212414757418737636268',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [
                        [

                            'menu' => [

                                'label' => 'e-sport',
                                'page' => 5,
                                'parent' => '212414757418737636268',
                                'id' => '809514757418790392732',
                                'position' => '0',
                                'level' => 1,
                            ],
                            'tree' => [
                                [
                                    'menu' => [
                                        'label' => '1',
                                        'page' => 4,
                                        'parent' => '809514757418790392732',
                                        'id' => '909214757419125297148',
                                        'position' => '0',
                                        'level' => 2,
                                    ],
                                    'tree' => [],
                                ],
                                [
                                    'menu' => [

                                        'label' => '3',
                                        'page' => 6,
                                        'parent' => '809514757418790392732',
                                        'id' => '2591475741913921812',
                                        'position' => '1',
                                        'level' => 2,
                                    ],
                                    'tree' => [],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'menu' => [
                        'label' => 'LOL',
                        'page' => 6,
                        'parent' => '170814757418904068535',
                        'id' => '170814757418904068535',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [],
                ],
                [
                    'menu' => [
                        'label' => 'menu 4 (lol)',
                        'page' => 6,
                        'parent' => '491414757419032091078',
                        'id' => '491414757419032091078',
                        'position' => null,
                        'level' => 0,
                    ],
                    'tree' => [
                        [

                            'menu' => [
                                'label' => 'test',
                                'page' => 4,
                                'parent' => '491414757419032091078',
                                'id' => '438714757482283036312',
                                'position' => '0',
                                'level' => 1,
                            ],
                            'tree' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    const FAKE_PAGE_URL = '//demo.synapse-cmf.dev/test-page-menu';

    public static function getSimplelevelViewMenu()
    {
        return [
            [
                'menu' => [
                    'label' => 'HP',
                    'title' => 'page-4',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [],
            ],
            [
                'menu' => [
                    'label' => 'LOL',
                    'title' => 'page-6',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [],
            ],
            [
                'menu' => [
                    'label' => 'menu 3',
                    'title' => 'page-5',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [],
            ],
        ];
    }

    public static function getMultilelevelViewMenu()
    {
        return [
            [
                'menu' => [
                    'label' => 'HP',
                    'title' => 'page-4',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [
                    [
                        'menu' => [
                            'label' => 'e-sport',
                            'title' => 'page-5',
                            'url' => self::FAKE_PAGE_URL,
                        ],
                        'tree' => [
                                [
                                    'menu' => [

                                        'label' => '1',
                                        'title' => 'page-4',
                                        'url' => self::FAKE_PAGE_URL,
                                    ],
                                    'tree' => [],
                                ],
                                [
                                    'menu' => [
                                        'label' => '3',
                                        'title' => 'page-6',
                                        'url' => self::FAKE_PAGE_URL,
                                    ],
                                    'tree' => [],
                                ],
                            ],
                    ],
                ],
            ],
            [
                'menu' => [
                    'label' => 'LOL',
                    'title' => 'page-6',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [],
            ],
            [
                'menu' => [
                    'label' => 'menu 4 (lol)',
                    'title' => 'page-6',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [
                    [
                        'menu' => [
                            'label' => 'test',
                            'title' => 'page-4',
                            'url' => self::FAKE_PAGE_URL,
                        ],
                        'tree' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getMultilelevelViewMenuWithOnlinePagesOnly()
    {
        return [
            [
                'menu' => [
                    'label' => 'HP',
                    'title' => 'page-4',
                    'url' => self::FAKE_PAGE_URL,
                ],
                'tree' => [
                    [
                        'menu' => [
                            'label' => 'e-sport',
                            'title' => 'page-5',
                            'url' => self::FAKE_PAGE_URL,
                        ],
                        'tree' => [
                                [
                                    'menu' => [

                                        'label' => '1',
                                        'title' => 'page-4',
                                        'url' => self::FAKE_PAGE_URL,
                                    ],
                                    'tree' => [],
                                ],
                            ],
                    ],
                ],
            ],
        ];
    }
}
