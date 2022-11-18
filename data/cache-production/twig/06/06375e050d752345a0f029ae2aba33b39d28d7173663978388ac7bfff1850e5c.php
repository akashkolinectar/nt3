<?php

/* nt3-portal-base/portal/src/views/bricks/manage/popup-export-excel.html.twig */
class __TwigTemplate_7a660cad1832b40b15acbfe82e0adc194e96404491c837fad8f50aa71bdeb978 extends Twig_Template
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
        // line 3
        echo "
<div class=\"modal-header clearfix\">
    <h4 class=\"modal-title\" style=\"float: left;\">";
        // line 5
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("ExcelExporter:ExportDialogTitle")), "html", null, true);
        echo "</h4>
</div>
<div class=\"modal-body\">
    <div id=\"export-text-result\" style=\"display:none;\">
        <p>";
        // line 9
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Core:BulkExport:ExportResult")), "html", null, true);
        echo "</p>
        <p id=\"export-error\" class=\"alert alert-danger\" role=\"alert\"></p>
    </div>

    <div id=\"export-feedback\">
        <p class=\"export-message\" style=\"text-align:center;\">";
        // line 14
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("ExcelExport:PreparingExport")), "html", null, true);
        echo "</p>
        <div class=\"progress\">
            <div class=\"progress-bar\" role=\"progressbar\" style=\"width: 0%\"
                 aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\">
                <span class=\"progress-message\">0%</span>
            </div>
        </div>
    </div>
</div>
<div class=\"modal-footer\">
    <button id=\"export-close\" type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" style=\"display:none;\">";
        // line 24
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Close")), "html", null, true);
        echo "</button>
    <button id=\"export-cancel\" type=\"button\" class=\"btn btn-secondary export-cancel\">";
        // line 25
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Cancel")), "html", null, true);
        echo "</button>
</div>

<script type=\"text/javascript\">
    var sDataState = 'not-yet-started';
    var sOQL = \"";
        // line 30
        echo ($context["sOQL"] ?? null);
        echo "\";
    var sFormat = 'xlsx';
    var sFields = \"";
        // line 32
        echo twig_escape_filter($this->env, ($context["sFields"] ?? null), "html", null, true);
        echo "\";

    \$(document).ready(function () {
        window.setTimeout(function () {
            \$('.progress').progressbar({
                value: 0,
                change: function () {
                    \$('.progress-message').text(\$(this).progressbar(\"value\") + \"%\");
                    \$('.progress-bar').attr('aria-valuenow', \$(this).progressbar(\"value\"));
                    \$('.progress-bar').width(\$(this).progressbar(\"value\") + \"%\");
                },
                complete: function () {
                    \$('.progress-message').text('100 %');
                    \$('.progress-bar').attr('aria-valuenow', '100');
                    \$('.progress-bar').width('100%');
                }
            });

            \$('.export-cancel').on('click', function () {
                sDataState = 'cancelled';
            });

            ExportStartExport();
        }, 100);
    });
</script>
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/manage/popup-export-excel.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  68 => 32,  63 => 30,  55 => 25,  51 => 24,  38 => 14,  30 => 9,  23 => 5,  19 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/manage/popup-export-excel.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/manage/popup-export-excel.html.twig");
    }
}
