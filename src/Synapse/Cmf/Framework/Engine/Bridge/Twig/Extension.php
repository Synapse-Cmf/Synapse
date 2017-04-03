<?php

namespace Synapse\Cmf\Framework\Engine\Bridge\Twig;

use Synapse\Cmf\Framework\Engine\Renderer\Zone\ZoneRenderer;
use Synapse\Cmf\Framework\Engine\Engine as Synapse;

/**
 * Extension class which provides Synapse specific tags :
 *   - {{ synapse_zone('name') }}.
 *   - {{ synapse_decorate$($decorable, ['param' => 12], 'templateTypeName') }}.
 */
class Extension extends \Twig_Extension
{
    /**
     * @var ZoneRenderer
     */
    protected $zoneRenderer;

    /**
     * @var Synapse
     */
    protected $synapse;

    /**
     * Construct.
     *
     * @param ZoneRenderer $zoneRenderer
     */
    public function __construct(ZoneRenderer $zoneRenderer, Synapse $synapse)
    {
        $this->zoneRenderer = $zoneRenderer;
        $this->synapse = $synapse;
    }

    /**
     * @see \Twig_Extension::getName()
     */
    public function getName()
    {
        return 'synapse';
    }

    /**
     * @see \Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('synapse_zone', array($this, 'renderZone'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('synapse_decorate', array($this, 'decorate'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Render zone under given name.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     */
    public function renderZone($name, array $parameters = array())
    {
        return $this->zoneRenderer->render($name, $parameters);
    }

    /**
     * Decorates.
     *
     * @param ContentInterface|ComponentInterface $decorable
     * @param array                               $parameters
     * @param string                              $templateTypeName
     *
     * @return string
     */
    public function decorate($decorable, $parameters = array(), $templateTypeName = null)
    {
        return $this->synapse
            ->createDecorator($decorable, $templateTypeName)
            ->decorate($parameters);
    }
}
