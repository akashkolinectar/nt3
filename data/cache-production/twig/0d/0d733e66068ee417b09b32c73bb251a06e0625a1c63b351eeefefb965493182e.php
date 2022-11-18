<?php

/* nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig */
class __TwigTemplate_9b4409eadfaa3ff3293594d7bbc8c1e952d91a7d528fd370ee64786e48b6efb7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 2
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/layout.html.twig", "nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig", 2);
        $this->blocks = array(
            'pMainHeaderTitle' => array($this, 'block_pMainHeaderTitle'),
            'pMainHeaderActions' => array($this, 'block_pMainHeaderActions'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/bricks/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_pMainHeaderTitle($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute(($context["oBrick"] ?? null), "GetTitle", array(), "method"))), "html", null, true);
        echo " (";
        echo twig_escape_filter($this->env, ($context["iCount"] ?? null), "html", null, true);
        echo ") ";
    }

    // line 6
    public function block_pMainHeaderActions($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        if ((twig_length_filter($this->env, $this->getAttribute(($context["oBrick"] ?? null), "GetAvailablesDisplayModes", array())) > 1)) {
            // line 8
            echo "\t\t<div class=\"btn-group btn_group_explicit\">
            ";
            // line 9
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["oBrick"] ?? null), "GetAvailablesDisplayModes", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["sDisplay"]) {
                // line 10
                echo "\t\t\t\t<a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_manage_brick_display_as", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sDisplayMode" => $context["sDisplay"])), "method"), "html", null, true);
                if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array", false, true), "hash", array(), "array", true, true)) {
                    echo "#";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array"), "hash", array(), "array"), "html", null, true);
                }
                echo "\"
\t\t\t\t   id=\"btn_tab_for_";
                // line 11
                echo twig_escape_filter($this->env, $context["sDisplay"], "html", null, true);
                echo "\"
\t\t\t\t   class=\"btn btn-default ";
                // line 12
                if (($context["sDisplay"] == $this->getAttribute($this->getAttribute(($context["oBrick"] ?? null), "GetPresentationDataForTileMode", array(0 => ($context["sDisplayMode"] ?? null)), "method"), "layoutDisplayMode", array()))) {
                    echo "active";
                }
                echo "\">
                    ";
                // line 13
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array(("Brick:Portal:Manage:DisplayMode:" . $context["sDisplay"]))), "html", null, true);
                echo "
\t\t\t\t</a>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sDisplay'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 16
            echo "\t\t</div>
    ";
        }
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 16,  70 => 13,  64 => 12,  60 => 11,  51 => 10,  47 => 9,  44 => 8,  41 => 7,  38 => 6,  29 => 4,  11 => 2,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig");
    }
}
