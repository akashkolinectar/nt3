<?php

/* nt3-portal-base/portal/src/views/bricks/object/mode_edit.html.twig */
class __TwigTemplate_a044c40cadba3091d0cdb3c6b48e98db6dd566f5a898536e45de7fdd9ecdd69b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 3
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig", "nt3-portal-base/portal/src/views/bricks/object/mode_edit.html.twig", 3);
        $this->blocks = array(
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/object/mode_edit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  11 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/object/mode_edit.html.twig", "/var/www/html/nt3test/env-production/nt3-portal-base/portal/src/views/bricks/object/mode_edit.html.twig");
    }
}
