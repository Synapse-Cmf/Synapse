<?php

namespace Synapse\Page\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Synapse\Cmf\Framework\Theme\Component\Model\ComponentInterface;
use Synapse\Cmf\Framework\Theme\Content\Model\ContentInterface;

class PageMenuContentController extends Controller
{
    /**
     * Component rendering action.
     *
     * @param ComponentInterface $component
     * @param ContentInterface   $content
     *
     * @return Response
     */
    public function renderAction(ComponentInterface $component, ContentInterface $content)
    {
        return $this->get('synapse')
            ->createDecorator($component)
            ->decorate(array(
                'content' => $content,
                'page_menu' => $this->get('synapse.page.page_menu_view_builder')->buildView($component),
            ))
            ;
    }
}
