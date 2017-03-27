<?php

namespace Synapse\Cmf\Bundle\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Synapse\Cmf\Bundle\Form\Type\Media\ImageChoiceType;
use Synapse\Cmf\Bundle\Form\Type\Theme\ComponentDataType;

/**
 * Gallery component form type.
 */
class GalleryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ComponentDataType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined('image_formats');
    }

    /**
     * Gallery component form prototype definition.
     *
     * @see FormInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array())
            ->add('medias', ImageChoiceType::class, array(
                'required' => false,
                'expanded' => false,
                'multiple' => true,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                // If we already selected "medias", reorder the "choices" option to keep our medias ordered
                if (!empty($data['medias'])) {
                    $form = $event->getForm();
                    $mediasConfig = $form->get('medias')->getConfig();
                    $mediasChoices = $mediasConfig->getOption('choices');
                    // Remove selected medias from medias list
                    foreach ($mediasChoices as $key => $mediaId) {
                        if (in_array($mediaId, $data['medias'])) {
                            unset($mediasChoices[$key]);
                        }
                    }
                    // Prepend selected medias to medias list
                    foreach (array_reverse($data['medias']) as $mediaId) {
                        array_unshift($mediasChoices, $mediaId);
                    }
                    // Replace existing "medias" field with the new "choices" option
                    $form->add('medias', ImageChoiceType::class, array_replace($mediasConfig->getOptions(), array(
                        'choices' => $mediasChoices,
                    )));
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                // Force keeping media ordered the way it was send
                $event->getForm()->setData($event->getData());
            })
        ;
    }
}
