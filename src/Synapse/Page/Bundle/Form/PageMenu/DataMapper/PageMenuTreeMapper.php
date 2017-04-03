<?php

namespace Synapse\Page\Bundle\Form\PageMenu\DataMapper;

use Majora\Framework\Model\EntityCollection;
use Symfony\Component\Form\DataMapperInterface;

class PageMenuTreeMapper implements DataMapperInterface
{
    /**
     * @var string
     */
    protected $fieldname;

    /**
     * PageMenuTreeMapper constructor.
     *
     * @param $fieldname
     */
    public function __construct($fieldname)
    {
        $this->fieldname = $fieldname;
    }

    /**
     * @param mixed                     $data
     * @param RecursiveIteratorIterator $forms
     *
     * @return array|string
     */
    public function mapDataToForms($data, $forms)
    {
        if (!isset($data[$this->fieldname])) {
            return;
        }

        $value = $data[$this->fieldname];

        if (!$value) {
            return '';
        }
        $menus = [];
        foreach ($value as $m) {
            if (empty($m['menu'])) {
                continue;
            }
            $menus = array_merge($menus, $this->flatten($m));
        }

        foreach ($forms as $k => $f) {
            if ($this->fieldname !== $k) {
                continue;
            }
            $f->setData(array_values($menus));
        }
    }

    /**
     * @param \Symfony\Component\Form\FormInterface[] $forms
     * @param mixed                                   $data
     */
    public function mapFormsToData($forms, &$data)
    {
        $value = [];
        // $forms should be a single RecursiveIteratorIterator
        foreach ($forms as $form) {
            $value = $form->getData();
        }

        list($parents, $levels) = (new EntityCollection($value))->partition(function ($k, $menu) {
            return !$menu['parent'] || ($menu['parent'] == $menu['id']);
        });

        $tree = $parents->map(function ($p) use ($levels) {
            return [
                'menu' => $p,
                'tree' => $this->findSubMenu($p['id'], $levels),
            ];
        });

        $data[$this->fieldname] = array_values($tree->toArray());
    }

    /**
     * build submenu tree for each children of $parentId menu contained in $levels.
     *
     * @param $parentId
     * @param array $levels
     *
     * @return array
     */
    protected function findSubMenu($parentId, EntityCollection $levels)
    {
        return $levels->reduce(function ($r, $level) use ($parentId, $levels) {
            if ($parentId == $level['parent']) {
                $r[] = [
                    'menu' => $level,
                    'tree' => $this->findSubMenu($level['id'], $levels),
                ];
            }

            return $r;
        }, []);
    }

    /**
     * extract all menus inside `tree` deep array to build a single level list.
     *
     * @param array $menus
     *
     * @return array
     */
    protected function flatten(array $menus, $level = 0)
    {
        $menus['menu']['level'] = $level;

        if (empty($menus['tree'])) {
            return [$menus['menu']];
        }

        $m = [$menus['menu']];
        ++$level;
        foreach ($menus['tree'] as $menu) {
            $m = array_merge($m, $this->flatten($menu, $level));
        }

        return $m;
    }
}
