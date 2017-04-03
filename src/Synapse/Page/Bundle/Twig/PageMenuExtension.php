<?php

namespace Synapse\Page\Bundle\Twig;

class PageMenuExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_page_menu', function (\Twig_Environment $env, $menu) {
                return $env->render('SynapsePageBundle:PageMenu:page_menu.html.twig', ['menu' => $menu]);
            }, ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'synapse_page_menu';
    }
}
