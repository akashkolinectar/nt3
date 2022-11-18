<?php

/* nt3-portal-base/portal/src/views/home/layout.html.twig */
class __TwigTemplate_704bb43968576f2ae0017d03ac377ce724df36fcb69d735c5e1c624246097eba extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'pPageBodyClass' => array($this, 'block_pPageBodyClass'),
            'pMainWrapper' => array($this, 'block_pMainWrapper'),
        );
    }

    protected function doGetParent(array $context)
    {
        // line 3
        return $this->loadTemplate($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "templates", array()), "layout", array()), "nt3-portal-base/portal/src/views/home/layout.html.twig", 3);
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_pPageBodyClass($context, array $blocks = array())
    {
        echo "home";
    }

    // line 7
    public function block_pMainWrapper($context, array $blocks = array())
    {
        // line 8
        $context["iCurrentTileIndex"] = 0;
        // line 9
        echo "<div class=\"container-fluid\" id=\"main-wrapper\">
\t<div class=\"row\">
\t\t<div class=\"col-xs-12 col-sm-9 col-md-10 col-sm-offset-3 col-md-offset-2\">
\t\t\t<section class=\"row tiles_wrapper\">
\t\t\t\t";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "bricks_ordering", array()), "home", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["brick"]) {
            // line 14
            echo "\t\t\t\t\t";
            if ($this->getAttribute($context["brick"], "GetVisibleHome", array())) {
                // line 15
                echo "\t\t\t\t\t\t";
                if ($this->getAttribute(($context["aTilesRendering"] ?? null), $this->getAttribute($context["brick"], "GetId", array()), array(), "array", true, true)) {
                    // line 16
                    echo "\t\t\t\t\t\t\t";
                    echo $this->getAttribute(($context["aTilesRendering"] ?? null), $this->getAttribute($context["brick"], "GetId", array()), array(), "array");
                    echo "
\t\t\t\t\t\t";
                } else {
                    // line 18
                    echo "\t\t\t\t\t\t\t";
                    $this->loadTemplate(("" . $this->getAttribute($context["brick"], "GetTileTemplatePath", array())), "nt3-portal-base/portal/src/views/home/layout.html.twig", 18)->display(array_merge($context, array("brick" => $context["brick"])));
                    // line 19
                    echo "\t\t\t\t\t\t";
                }
                // line 20
                echo "\t\t\t\t\t";
            }
            // line 21
            echo "\t\t\t\t";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['brick'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 22
        echo "\t\t\t</section>
\t\t</div>
\t</div>
</div>
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/home/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  97 => 22,  83 => 21,  80 => 20,  77 => 19,  74 => 18,  68 => 16,  65 => 15,  62 => 14,  45 => 13,  39 => 9,  37 => 8,  34 => 7,  28 => 5,  19 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/home/layout.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/home/layout.html.twig");
    }
}
