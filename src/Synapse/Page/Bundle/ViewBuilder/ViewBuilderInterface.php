<?php

namespace Synapse\Page\Bundle\ViewBuilder;

use Synapse\Cmf\Framework\Theme\Component\Model\ComponentInterface;

interface ViewBuilderInterface
{
    /**
     * @param ComponentInterface $component
     *
     * @return mixed
     */
    public function buildView(ComponentInterface $component);
}
