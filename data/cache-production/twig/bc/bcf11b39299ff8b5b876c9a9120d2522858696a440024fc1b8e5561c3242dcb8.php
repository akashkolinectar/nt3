<?php

/* nt3-portal-base/portal/src/views/bricks/object/plugins_buttons.html.twig */
class __TwigTemplate_029426fdabf405cd2a08434d075b06f682af13f4643748d769eb498a248dc017 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["iLinkButtonsCount"] = (($this->getAttribute(($context["aButtons"] ?? null), "links", array(), "any", true, true)) ? (twig_length_filter($this->env, $this->getAttribute(($context["aButtons"] ?? null), "links", array()))) : (0));
        // line 2
        $context["iActionButtonsCount"] = (($this->getAttribute(($context["aButtons"] ?? null), "actions", array(), "any", true, true)) ? (twig_length_filter($this->env, $this->getAttribute(($context["aButtons"] ?? null), "actions", array()))) : (0));
        // line 3
        $context["iButtonsCount"] = (($context["iLinkButtonsCount"] ?? null) + ($context["iActionButtonsCount"] ?? null));
        // line 4
        $context["iButtonsGroupThreshold"] = 2;
        // line 5
        $context["bGroupButtons"] = (((($context["iButtonsCount"] ?? null) > ($context["iButtonsGroupThreshold"] ?? null))) ? (true) : (false));
        // line 6
        echo "
";
        // line 7
        $context["sButtonCssClasses"] = ((($context["bGroupButtons"] ?? null)) ? ("") : ("btn btn-default"));
        // line 8
        $context["aJSFiles"] = array();
        // line 9
        echo "
";
        // line 10
        if ((($context["bGroupButtons"] ?? null) == true)) {
            // line 11
            echo "\t<div class=\"dropup\">
\t\t<button class=\"btn btn-default dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
\t\t\t";
            // line 13
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("UI:Menu:OtherActions")), "html", null, true);
            echo "
\t\t\t<span class=\"caret\"></span>
\t\t</button>
\t\t<ul class=\"dropdown-menu\">
";
        }
        // line 18
        echo "
\t";
        // line 20
        echo "\t";
        if ($this->getAttribute(($context["aButtons"] ?? null), "actions", array(), "any", true, true)) {
            // line 21
            echo "\t\t";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["aButtons"] ?? null), "actions", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["aButton"]) {
                // line 22
                echo "\t\t\t";
                // line 23
                echo "\t\t\t";
                if ((($context["bGroupButtons"] ?? null) == true)) {
                    // line 24
                    echo "\t\t\t\t<li>
\t\t\t";
                }
                // line 26
                echo "\t\t\t<a class=\"";
                echo twig_escape_filter($this->env, ($context["sButtonCssClasses"] ?? null), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_join_filter($this->getAttribute($context["aButton"], "css_classes", array()), " "), "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aButton"], "url", array()), "html", null, true);
                echo "\" onclick=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aButton"], "onclick", array()), "html", null, true);
                echo "\">";
                echo $this->getAttribute($context["aButton"], "label", array());
                echo "</a>
\t\t\t";
                // line 27
                if ((($context["bGroupButtons"] ?? null) == true)) {
                    // line 28
                    echo "\t\t\t\t</li>
\t\t\t";
                }
                // line 30
                echo "\t\t\t";
                // line 31
                echo "\t\t\t";
                $context["aJSFiles"] = twig_array_merge(($context["aJSFiles"] ?? null), $this->getAttribute($context["aButton"], "js_files", array()));
                // line 32
                echo "\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aButton'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 33
            echo "\t";
        }
        // line 34
        echo "\t";
        // line 35
        echo "\t";
        if ($this->getAttribute(($context["aButtons"] ?? null), "links", array(), "any", true, true)) {
            // line 36
            echo "\t\t";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["aButtons"] ?? null), "links", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["aButton"]) {
                // line 37
                echo "            ";
                if ((($context["bGroupButtons"] ?? null) == true)) {
                    // line 38
                    echo "\t\t\t\t<li>
\t\t\t";
                }
                // line 40
                echo "\t\t\t<a class=\"";
                echo twig_escape_filter($this->env, ($context["sButtonCssClasses"] ?? null), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_join_filter($this->getAttribute($context["aButton"], "css_classes", array()), " "), "html", null, true);
                echo "\" href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aButton"], "url", array()), "html", null, true);
                echo "\" target=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aButton"], "target", array()), "html", null, true);
                echo "\">";
                echo $this->getAttribute($context["aButton"], "label", array());
                echo "</a>
            ";
                // line 41
                if ((($context["bGroupButtons"] ?? null) == true)) {
                    // line 42
                    echo "\t\t\t\t</li>
            ";
                }
                // line 44
                echo "\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aButton'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 45
            echo "\t";
        }
        // line 46
        echo "
";
        // line 47
        if ((($context["bGroupButtons"] ?? null) == true)) {
            // line 48
            echo "\t\t</ul>
\t</div>
";
        }
        // line 51
        echo "
";
        // line 53
        if ((twig_length_filter($this->env, ($context["aJSFiles"] ?? null)) > 0)) {
            // line 54
            echo "\t<script type=\"text/javascript\">
\t\t";
            // line 55
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["aJSFiles"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["sJSFile"]) {
                // line 56
                echo "\t\tconsole.log('loading ";
                echo twig_escape_filter($this->env, $context["sJSFile"], "html", null, true);
                echo "');
\t\t\t\$.getScript('";
                // line 57
                echo twig_escape_filter($this->env, $context["sJSFile"], "html", null, true);
                echo "');
\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['sJSFile'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 59
            echo "\t</script>
";
        }
        // line 61
        echo "
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/object/plugins_buttons.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  190 => 61,  186 => 59,  178 => 57,  173 => 56,  169 => 55,  166 => 54,  164 => 53,  161 => 51,  156 => 48,  154 => 47,  151 => 46,  148 => 45,  142 => 44,  138 => 42,  136 => 41,  123 => 40,  119 => 38,  116 => 37,  111 => 36,  108 => 35,  106 => 34,  103 => 33,  97 => 32,  94 => 31,  92 => 30,  88 => 28,  86 => 27,  73 => 26,  69 => 24,  66 => 23,  64 => 22,  59 => 21,  56 => 20,  53 => 18,  45 => 13,  41 => 11,  39 => 10,  36 => 9,  34 => 8,  32 => 7,  29 => 6,  27 => 5,  25 => 4,  23 => 3,  21 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/object/plugins_buttons.html.twig", "/var/www/html/nt3test/env-production/nt3-portal-base/portal/src/views/bricks/object/plugins_buttons.html.twig");
    }
}
