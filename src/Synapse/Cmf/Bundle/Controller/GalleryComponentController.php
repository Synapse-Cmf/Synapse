<?php

namespace Synapse\Cmf\Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Synapse\Cmf\Framework\Theme\Component\Model\ComponentInterface;
use Synapse\Cmf\Framework\Theme\Content\Model\ContentInterface;

/**
 * Gallery component type controller.
 */
class GalleryComponentController extends Controller
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
        if (!$mediaData = $component->getData('medias')) {
            // @todo log
            return new Response('');
        }

        return $this->get('synapse')
            ->createDecorator($component)
            ->decorate(array(
                'content' => $content,
                'medias' => $this->container->get('synapse.image.loader')
                    ->retrieveAll(array('id' => $mediaData))
                        ->sortIdsAtTheTop($mediaData),
            ))
        ;
    }
}
