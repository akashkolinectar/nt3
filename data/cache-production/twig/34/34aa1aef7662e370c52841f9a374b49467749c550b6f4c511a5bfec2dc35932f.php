<?php

/* nt3-portal-base/portal/src/views/errors/layout.html.twig */
class __TwigTemplate_725621f6f47a57f23aa62c60a57467d8e0d95f64cec2914632a27e59c6cd03dc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 3
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/layout.html.twig", "nt3-portal-base/portal/src/views/errors/layout.html.twig", 3);
        $this->blocks = array(
            'pNavigationWrapper' => array($this, 'block_pNavigationWrapper'),
            'pMainWrapper' => array($this, 'block_pMainWrapper'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_pNavigationWrapper($context, array $blocks = array())
    {
    }

    // line 8
    public function block_pMainWrapper($context, array $blocks = array())
    {
        // line 9
        echo "\t<style>
\t\t.well {
\t\t\tmargin: 50px auto;
\t\t\ttext-align: center;
\t\t\tpadding: 25px;
\t\t\tmin-width: 600px;
\t\t\tmax-width: 1000px;
\t\t}
\t\th1, h2, h3, p {
\t\t\tmargin: 0;
\t\t}
\t\tp {
\t\t\tfont-size: 17px;
\t\t\tmargin-top: 25px;
\t\t}
\t\tp a.btn {
\t\t\tmargin: 0 5px;
\t\t}
\t\th1 .ion {
\t\t\tvertical-align: -5%;
\t\t\tmargin-right: 5px;
\t\t}
\t\tabbr[title]{
\t\t\tborder-bottom: none;
\t\t}
\t\t.traces.list_exception{
\t\t\ttext-align: left;
\t\t}
\t</style>
\t
\t<div class=\"container\">
\t\t<div class=\"well\">
\t\t\t<h1><div class=\"ion ion-alert-circled\"></div> ";
        // line 41
        echo twig_escape_filter($this->env, ($context["error_title"] ?? null), "html", null, true);
        echo "</h1>
\t\t\t<p>";
        // line 42
        echo twig_escape_filter($this->env, ($context["error_message"] ?? null), "html", null, true);
        echo "</p>
\t\t\t<p>";
        // line 43
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_format')->getCallable(), array("Error:HTTP:GetHelp", twig_constant("nt3_APPLICATION_SHORT"))), "html", null, true);
        echo "</p>
\t\t\t<p>
\t\t\t\t<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back(); return false;\"><span class=\"fa fa-arrow-left\"></span> ";
        // line 45
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:GoPreviousPage")), "html", null, true);
        echo "</a>
\t\t\t\t<a class=\"btn btn-default\" href=\"\"><span class=\"fa fa-repeat\"></span> ";
        // line 46
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:ReloadPage")), "html", null, true);
        echo "</a>
\t\t\t\t<a class=\"btn btn-default\" href=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_home"), "method"), "html", null, true);
        echo "\"><span class=\"fa fa-home\"></span> ";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:GoPortalHome")), "html", null, true);
        echo "</a>
\t\t\t</p>
\t\t</div>

\t\t";
        // line 51
        if (($this->getAttribute(($context["app"] ?? null), "debug", array(), "array") == true)) {
            // line 52
            echo "\t\t\t<div class=\"well\">
\t\t\t\t<ol class=\"traces list_exception\">
\t\t\t\t\t";
            // line 54
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["debug_trace_steps"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["aStep"]) {
                // line 55
                echo "                        <li>
\t\t\t\t\t\t\t";
                // line 56
                if ( !(null === $this->getAttribute($context["aStep"], "function_call", array()))) {
                    echo "at <abbr title=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["aStep"], "class_fq", array()), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["aStep"], "function_call", array()), "html", null, true);
                    echo "</abbr>";
                }
                // line 57
                echo "\t\t\t\t\t\t\tin <a title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aStep"], "file_fq", array()), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aStep"], "file_name", array()), "html", null, true);
                echo "</a> line ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aStep"], "line", array()), "html", null, true);
                echo "
\t\t\t\t\t\t</li>
\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aStep'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 60
            echo "\t\t\t\t</ol>
\t\t\t</div>
\t\t";
        }
        // line 63
        echo "\t</div>
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/errors/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  141 => 63,  136 => 60,  122 => 57,  114 => 56,  111 => 55,  107 => 54,  103 => 52,  101 => 51,  92 => 47,  88 => 46,  84 => 45,  79 => 43,  75 => 42,  71 => 41,  37 => 9,  34 => 8,  29 => 5,  11 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/errors/layout.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/errors/layout.html.twig");
    }
}
