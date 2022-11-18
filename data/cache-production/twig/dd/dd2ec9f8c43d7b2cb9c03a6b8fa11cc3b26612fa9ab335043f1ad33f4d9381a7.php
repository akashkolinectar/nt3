<?php

/* nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig */
class __TwigTemplate_5dfc0d0477003485b082021dcf73862edc7b4ce61f43ca0f0199255cedd7be05 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'pFormAlerts' => array($this, 'block_pFormAlerts'),
            'pFormFields' => array($this, 'block_pFormFields'),
            'pFormButtons' => array($this, 'block_pFormButtons'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        echo "
";
        // line 4
        $context["tIsModal"] = (((array_key_exists("tIsModal", $context) && (($context["tIsModal"] ?? null) == true))) ? (true) : (false));
        // line 5
        $context["sFormId"] = ((($this->getAttribute(($context["form"] ?? null), "id", array(), "any", true, true) &&  !(null === $this->getAttribute(($context["form"] ?? null), "id", array())))) ? ($this->getAttribute(($context["form"] ?? null), "id", array())) : ("object_form"));
        // line 6
        $context["sFormIdSanitized"] = twig_replace_filter(($context["sFormId"] ?? null), array("-" => ""));
        // line 7
        $context["sFormDisplayModeClass"] = ((($this->getAttribute(($context["form"] ?? null), "display_mode", array(), "any", true, true) &&  !(null === $this->getAttribute(($context["form"] ?? null), "display_mode", array())))) ? (("form_" . $this->getAttribute(($context["form"] ?? null), "display_mode", array()))) : (""));
        // line 8
        $context["sFormObjectStateClass"] = ((($this->getAttribute(($context["form"] ?? null), "object_state", array(), "any", true, true) &&  !(null === $this->getAttribute(($context["form"] ?? null), "object_state", array())))) ? (("form_object_state_" . $this->getAttribute(($context["form"] ?? null), "object_state", array()))) : (""));
        // line 9
        echo "
<form id=\"";
        // line 10
        echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
        echo "\" class=\"";
        echo twig_escape_filter($this->env, ($context["sFormDisplayModeClass"] ?? null), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, ($context["sFormObjectStateClass"] ?? null), "html", null, true);
        echo "\" method=\"POST\" action=\"";
        echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\"
\t";
        // line 11
        if (($this->getAttribute(($context["form"] ?? null), "object_state", array(), "any", true, true) &&  !(null === $this->getAttribute(($context["form"] ?? null), "object_state", array())))) {
            echo "data-object-state=\"";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "object_state", array()), "html", null, true);
            echo "\"";
        }
        echo ">
\t<input type=\"hidden\" name=\"transaction_id\" value=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "transaction_id", array()), "html", null, true);
        echo "\" />
\t<div class=\"form_alerts\">
\t\t";
        // line 14
        $this->displayBlock('pFormAlerts', $context, $blocks);
        // line 19
        echo "\t</div>
\t<div class=\"form_fields\">
\t\t";
        // line 21
        $this->displayBlock('pFormFields', $context, $blocks);
        // line 24
        echo "\t</div>
\t<div class=\"form_buttons\">
\t\t";
        // line 26
        $this->displayBlock('pFormButtons', $context, $blocks);
        // line 62
        echo "\t</div>
</form>

<script type=\"text/javascript\">
\t\$(document).ready(function(){
\t\t// Form field set declaration
\t\tvar oFieldSet_";
        // line 68
        echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
        echo " = \$('#";
        echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
        echo " > .form_fields').field_set(";
        echo twig_jsonencode_filter($this->getAttribute(($context["form"] ?? null), "fieldset", array()));
        echo ");
\t\t// Form handler declaration
\t\t\$('#";
        // line 70
        echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
        echo "').portal_form_handler({
\t\t\tformmanager_class: \"";
        // line 71
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->getAttribute(($context["form"] ?? null), "formmanager_class", array()), "js"), "html", null, true);
        echo "\",
\t\t\tformmanager_data: ";
        // line 72
        echo twig_jsonencode_filter($this->getAttribute(($context["form"] ?? null), "formmanager_data", array()));
        echo ",
\t\t\tfield_set: oFieldSet_";
        // line 73
        echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
        echo ",
\t\t\tsubmit_btn_selector: \$('#";
        // line 74
        echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
        echo "').parent().find('.form_btn_submit, .form_btn_transition'),
\t\t\tcancel_btn_selector: \$('#";
        // line 75
        echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
        echo "').parent().find('.form_btn_cancel'),
\t\t\tsubmit_url: ";
        // line 76
        if ( !(null === $this->getAttribute(($context["form"] ?? null), "submit_callback", array()))) {
            echo "\"";
            echo $this->getAttribute(($context["form"] ?? null), "submit_callback", array());
            echo "\"";
        } else {
            echo "null";
        }
        echo ",
\t\t\tcancel_url: ";
        // line 77
        if ( !(null === $this->getAttribute(($context["form"] ?? null), "cancel_callback", array()))) {
            echo "\"";
            echo $this->getAttribute(($context["form"] ?? null), "cancel_callback", array());
            echo "\"";
        } else {
            echo "null";
        }
        echo ",
\t\t\tendpoint: \"";
        // line 78
        echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\",
\t\t\tis_modal: ";
        // line 79
        if ((($context["tIsModal"] ?? null) == true)) {
            echo "true";
        } else {
            echo "false";
        }
        // line 80
        echo "\t\t});
\t\t
\t\t// Sticky buttons handler
\t\t";
        // line 83
        if ((($context["sMode"] ?? null) != "view")) {
            // line 84
            echo "\t\t\tif( \$('#";
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo " .form_btn_regular button').length > 0 )
\t\t\t{
\t\t\t\t// Note : This pattern if to prevent performance issues
\t\t\t\t// - Cloning buttons
\t\t\t\tvar oNormalRegularButtons_";
            // line 88
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo " = \$('#";
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo " .form_btn_regular');
\t\t\t\tvar oStickyRegularButtons_";
            // line 89
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo " = oNormalRegularButtons_";
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".clone(true, true);
\t\t\t\toStickyRegularButtons_";
            // line 90
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".addClass('sticky');
\t\t\t\tif(oStickyRegularButtons_";
            // line 91
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_submit span.glyphicon').length > 0)
\t\t\t\t{
\t\t\t\t\toStickyRegularButtons_";
            // line 93
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_submit').html( oStickyRegularButtons_";
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_submit span.glyphicon')[0].outerHTML );
\t\t\t\t}
\t\t\t\tif(oStickyRegularButtons_";
            // line 95
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_cancel span.glyphicon').length > 0)
\t\t\t\t{
\t\t\t\t\toStickyRegularButtons_";
            // line 97
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_cancel').html( oStickyRegularButtons_";
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".find('.form_btn_cancel span.glyphicon')[0].outerHTML );
\t\t\t\t}

\t\t\t\t\$('#";
            // line 100
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo "').closest(";
            if ((($context["tIsModal"] ?? null) == true)) {
                echo "'.modal'";
            } else {
                echo "'#main-content'";
            }
            echo ").append(oStickyRegularButtons_";
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ");

\t\t\t\t// - Global timeout for any
\t\t\t\tvar oScrollTimeout;
\t\t\t\t// - Scroll handler
\t\t\t\tscrollHandler_";
            // line 105
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo " = function () {
\t\t\t\t\tif(\$('#";
            // line 106
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo " .form_buttons').visible())
\t\t\t\t\t{
\t\t\t\t\t\toStickyRegularButtons_";
            // line 108
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".addClass('closed');
\t\t\t\t\t}
\t\t\t\t\telse
\t\t\t\t\t{
\t\t\t\t\t\toStickyRegularButtons_";
            // line 112
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".removeClass('closed');
\t\t\t\t\t}
\t\t\t\t};
\t\t\t\t// - Event binding for scroll
\t\t\t\t\$(";
            // line 116
            if ((($context["tIsModal"] ?? null) == true)) {
                echo "'.modal.in'";
            } else {
                echo "window";
            }
            echo ").off('scroll').on('scroll', function () {
\t\t\t\t\tif (oScrollTimeout) {
\t\t\t\t\t\t// Clear the timeout, if one is pending
\t\t\t\t\t\tclearTimeout(oScrollTimeout);
\t\t\t\t\t\toScrollTimeout = null;
\t\t\t\t\t}
\t\t\t\t\toScrollTimeout = setTimeout(scrollHandler_";
            // line 122
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ", 50);
\t\t\t\t});
\t\t\t\t// - Event binding for linkedset collapse
\t\t\t\t\$(";
            // line 125
            if ((($context["tIsModal"] ?? null) == true)) {
                echo "'.modal.in'";
            } else {
                echo "window";
            }
            echo ").off('shown.bs.collapse hidden.bs.collapse').on('shown.bs.collapse hidden.bs.collapse', function () {
\t\t\t\t\tscrollHandler_";
            // line 126
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo "();
\t\t\t\t});
\t\t\t\t// - Event binding for form building / updating
\t\t\t\t// Note : We do not want to 'off' the event or it will remove listeners from the widget
\t\t\t\toFieldSet_";
            // line 130
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".on('form_built', function(oEvent){
\t\t\t\t\tscrollHandler_";
            // line 131
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo "();
\t\t\t\t});
\t\t\t\t// - Initial test
\t\t\t\tsetTimeout(function(){ scrollHandler_";
            // line 134
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo "(); }, 400);

\t\t\t\t// Remove sticky button when closing modal
\t\t\t\t\$('#";
            // line 137
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo "').closest('.modal').on('hide.bs.modal', function () {
\t\t\t\t\toStickyRegularButtons_";
            // line 138
            echo twig_escape_filter($this->env, ($context["sFormIdSanitized"] ?? null), "html", null, true);
            echo ".remove();
\t\t\t\t});
\t\t\t}
\t\t";
        }
        // line 142
        echo "\t\t
\t\t";
        // line 143
        if ((($context["tIsModal"] ?? null) == true)) {
            // line 144
            echo "\t\t\t// Scroll top (because sometimes when several modals have been opened)
\t\t\t\$('#";
            // line 145
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo "').closest('.modal').scrollTop(0);
\t\t\t\$('#";
            // line 146
            echo twig_escape_filter($this->env, ($context["sFormId"] ?? null), "html", null, true);
            echo "').closest('.modal').find('.modal-footer').hide();
\t\t";
        }
        // line 148
        echo "\t});
</script>";
    }

    // line 14
    public function block_pFormAlerts($context, array $blocks = array())
    {
        // line 15
        echo "\t\t\t<div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t<div class=\"alert alert-warning\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t<div class=\"alert alert-error alert-danger\" role=\"alert\" style=\"display: none;\"></div>
\t\t";
    }

    // line 21
    public function block_pFormFields($context, array $blocks = array())
    {
        // line 22
        echo "\t\t\t";
        echo $this->getAttribute($this->getAttribute(($context["form"] ?? null), "renderer", array()), "GetBaseLayout", array(), "method");
        echo "
\t\t";
    }

    // line 26
    public function block_pFormButtons($context, array $blocks = array())
    {
        // line 27
        echo "            ";
        // line 28
        echo "            ";
        if (($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", true, true) && ($this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", false, true), "actions", array(), "any", true, true) || $this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", false, true), "links", array(), "any", true, true)))) {
            // line 29
            echo "\t\t\t\t<div class=\"form_btn_misc\">
                    ";
            // line 30
            $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/object/plugins_buttons.html.twig", "nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig", 30)->display(array_merge($context, array("aButtons" => $this->getAttribute(($context["form"] ?? null), "buttons", array()))));
            // line 31
            echo "\t\t\t\t</div>
            ";
        }
        // line 33
        echo "\t\t\t";
        // line 34
        echo "\t\t\t";
        if ((($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", true, true) && $this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", false, true), "transitions", array(), "any", true, true)) && (twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array()), "transitions", array())) > 0))) {
            // line 35
            echo "\t\t\t\t<div class=\"form_btn_transitions\">
\t\t\t\t";
            // line 36
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array()), "transitions", array()));
            foreach ($context['_seq'] as $context["sStimulusCode"] => $context["sStimulusLabel"]) {
                // line 37
                echo "\t\t\t\t\t<button class=\"btn btn-primary form_btn_transition\" type=\"submit\" name=\"stimulus_code\" value=\"";
                echo twig_escape_filter($this->env, $context["sStimulusCode"], "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $context["sStimulusLabel"], "html", null, true);
                echo "</button>
\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['sStimulusCode'], $context['sStimulusLabel'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 39
            echo "\t\t\t\t</div>
\t\t\t";
        }
        // line 41
        echo "\t\t\t<div class=\"form_btn_regular\">
\t\t\t\t";
        // line 43
        echo "\t\t\t\t";
        if (($this->getAttribute(($context["form"] ?? null), "editable_fields_count", array(), "any", true, true) && ($this->getAttribute(($context["form"] ?? null), "editable_fields_count", array()) > 0))) {
            // line 44
            echo "\t\t\t\t\t<button class=\"btn btn-default form_btn_cancel\" type=\"button\" value=\"cancel\" title=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Cancel")), "html", null, true);
            echo "\" data-dismiss=\"modal\">
\t\t\t\t\t\t<span class=\"glyphicon glyphicon-remove\"></span>
\t\t\t\t\t\t";
            // line 46
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Cancel")), "html", null, true);
            echo "
\t\t\t\t\t</button>
\t\t\t\t\t";
            // line 48
            if ($this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array(), "any", false, true), "submit", array(), "any", true, true)) {
                // line 49
                echo "\t\t\t\t\t\t<button class=\"btn btn-primary form_btn_submit\" type=\"submit\" value=\"submit\" title=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array()), "submit", array()), "label", array()), "html", null, true);
                echo "\">
\t\t\t\t\t\t\t<span class=\"glyphicon glyphicon-ok\"></span>
\t\t\t\t\t\t\t";
                // line 51
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "buttons", array()), "submit", array()), "label", array()), "html", null, true);
                echo "
\t\t\t\t\t\t</button>
\t\t\t\t\t";
            }
            // line 54
            echo "\t\t\t\t";
        } else {
            // line 55
            echo "\t\t\t\t\t";
            // line 56
            echo "\t\t\t\t\t";
            if (($context["tIsModal"] ?? null)) {
                // line 57
                echo "\t\t\t\t\t\t<input class=\"btn btn-default form_btn_cancel\" type=\"button\" value=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Close")), "html", null, true);
                echo "\" data-dismiss=\"modal\">
\t\t\t\t\t";
            }
            // line 59
            echo "\t\t\t\t";
        }
        // line 60
        echo "\t\t\t</div>
\t\t";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  436 => 60,  433 => 59,  427 => 57,  424 => 56,  422 => 55,  419 => 54,  413 => 51,  407 => 49,  405 => 48,  400 => 46,  394 => 44,  391 => 43,  388 => 41,  384 => 39,  373 => 37,  369 => 36,  366 => 35,  363 => 34,  361 => 33,  357 => 31,  355 => 30,  352 => 29,  349 => 28,  347 => 27,  344 => 26,  337 => 22,  334 => 21,  327 => 15,  324 => 14,  319 => 148,  314 => 146,  310 => 145,  307 => 144,  305 => 143,  302 => 142,  295 => 138,  291 => 137,  285 => 134,  279 => 131,  275 => 130,  268 => 126,  260 => 125,  254 => 122,  241 => 116,  234 => 112,  227 => 108,  222 => 106,  218 => 105,  202 => 100,  194 => 97,  189 => 95,  182 => 93,  177 => 91,  173 => 90,  167 => 89,  161 => 88,  153 => 84,  151 => 83,  146 => 80,  140 => 79,  136 => 78,  126 => 77,  116 => 76,  112 => 75,  108 => 74,  104 => 73,  100 => 72,  96 => 71,  92 => 70,  83 => 68,  75 => 62,  73 => 26,  69 => 24,  67 => 21,  63 => 19,  61 => 14,  56 => 12,  48 => 11,  38 => 10,  35 => 9,  33 => 8,  31 => 7,  29 => 6,  27 => 5,  25 => 4,  22 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/object/mode_create.html.twig");
    }
}
