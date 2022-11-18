<?php

/* nt3-portal-base/portal/src/views/bricks/manage/tile-default.html.twig */
class __TwigTemplate_b75ba970cd1a8e0453e98635230229d05d52682613b72641a4f375340b69fafc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'pTileWrapper' => array($this, 'block_pTileWrapper'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        echo "
<div class=\"col-xs-12 col-sm-";
        // line 4
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oBrick"] ?? null), "GetWidth", array()), "html", null, true);
        echo "\">
    ";
        // line 5
        $this->displayBlock('pTileWrapper', $context, $blocks);
        // line 21
        echo "</div>";
    }

    // line 5
    public function block_pTileWrapper($context, array $blocks = array())
    {
        // line 6
        echo "        <a href=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), 1 => array("sBrickId" => $this->getAttribute(($context["oBrick"] ?? null), "GetId", array()))), "method"), "html", null, true);
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array", false, true), "hash", array(), "array", true, true)) {
            echo "#";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array"), "hash", array(), "array"), "html", null, true);
        }
        echo "\"
        ";
        // line 7
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array", false, true), "navigation_menu_attr", array(), "array", true, true)) {
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute(($context["oBrick"] ?? null), "GetRouteName", array()), array(), "array"), "navigation_menu_attr", array(), "array"));
            foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                echo " ";
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "=\"";
                echo twig_escape_filter($this->env, $context["value"], "html", null, true);
                echo "\"";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
        // line 8
        echo "        ";
        if ($this->getAttribute(($context["oBrick"] ?? null), "GetModal", array())) {
            echo "data-toggle=\"modal\" data-target=\"#modal-for-all\"";
        }
        // line 9
        echo "        class=\"tile";
        echo "\" id=\"brick-";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oBrick"] ?? null), "GetId", array()), "html", null, true);
        echo "\" data-brick-id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oBrick"] ?? null), "GetId", array()), "html", null, true);
        echo "\">
        <div class=\"tile_decoration\">
            <span class=\"icon ";
        // line 11
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oBrick"] ?? null), "GetDecorationClassHome", array()), "html", null, true);
        echo "\"></span>
        </div>
        <div class=\"tile_body\">
            <div class=\"tile_title\">";
        // line 14
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute(($context["oBrick"] ?? null), "GetTitleHome", array()))), "html", null, true);
        echo "</div>
            ";
        // line 15
        if ($this->getAttribute(($context["oBrick"] ?? null), "HasDescription", array())) {
            // line 16
            echo "                <div class=\"tile_description\">";
            echo call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute(($context["oBrick"] ?? null), "GetDescription", array())));
            echo "</div>
            ";
        }
        // line 18
        echo "        </div>
        </a>
    ";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/manage/tile-default.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 18,  86 => 16,  84 => 15,  80 => 14,  74 => 11,  65 => 9,  60 => 8,  45 => 7,  36 => 6,  33 => 5,  29 => 21,  27 => 5,  23 => 4,  20 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/manage/tile-default.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/manage/tile-default.html.twig");
    }
}
