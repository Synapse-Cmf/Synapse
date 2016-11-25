<?php

namespace Synapse\Cmf\Framework\Engine\Resolver;

use Synapse\Cmf\Framework\Engine\Exception\InvalidTemplateException;
use Synapse\Cmf\Framework\Theme\Content\Entity\Content;
use Synapse\Cmf\Framework\Theme\TemplateType\Entity\TemplateType;
use Synapse\Cmf\Framework\Theme\Template\Loader\LoaderInterface as TemplateLoader;
use Synapse\Cmf\Framework\Theme\Theme\Model\ThemeInterface;
use Synapse\Cmf\Framework\Theme\Variation\Entity\Variation;

/**
 * Resolve and returns a Template instance from an EngineContext.
 */
class TemplateResolver
{
    /**
     * @var TemplateLoader
     */
    protected $templateLoader;

    /**
     * Construct.
     *
     * @param TemplateLoader $templateLoader
     */
    public function __construct(TemplateLoader $templateLoader)
    {
        $this->templateLoader = $templateLoader;
    }

    /**
     * Resolve current template from given Theme and template name.
     *
     * @param Variation $variation
     * @param string    $templateName
     *
     * @return TemplateInterface
     */
    public function resolve(ThemeInterface $theme, Content $content, Variation $variation, $templateName)
    {
        $templateTypes = $theme->getTemplateTypes();

        switch (true) {

            // given template name
            case $templateName :
                if (!$templateType = $templateTypes
                    ->search(array('name' => $templateName))
                    ->first()
                ) {
                    throw new InvalidTemplateException(sprintf(
                        'No template named "%s" registered under "%s" theme. Please check your configuration.',
                        $templateName,
                        $theme->getName()
                    ));
                }
            break;

            // default template type
            case $templateType = $templateTypes
                ->filter(function (TemplateType $templateType) use ($variation) {
                    return (bool) $variation->getConfiguration(
                        'templates',
                        $templateType->getName(),
                        'default'
                    );
                })
                ->first()
            :
            break;

            // first template type
            default:
                $templateType = $templateTypes->first();
        }
        if (empty($templateType)) {
            throw new InvalidTemplateException(sprintf(
                'No valid template type found under "%s" theme for "%s" content type. Please check your configuration.',
                $theme->getName(),
                $content->getType()->getName()
            ));
        }

        if (!$template = $this->templateLoader->retrieveDisplayable(
                $templateType,
                $content
            )
        ) {
            throw new InvalidTemplateException(sprintf(
                'Any "%s" template found under "%s" theme for "%s#%d" content.',
                $templateType->getName(),
                $theme->getName(),
                $content->getType()->getName(),
                $content->getContentId()
            ));
        }

        return $template->setContent($content);
    }
}
