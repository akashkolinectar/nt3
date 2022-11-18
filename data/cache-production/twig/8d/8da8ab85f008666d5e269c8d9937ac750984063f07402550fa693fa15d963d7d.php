<?php

/* nt3-portal-base/portal/src/views/layout.html.twig */
class __TwigTemplate_0fa0f6acddb54ce085efca4a8238341ff1cfef513b0373deb890fd675b4a3a1e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'pPageExtraMetas' => array($this, 'block_pPageExtraMetas'),
            'pPageTitle' => array($this, 'block_pPageTitle'),
            'pPageStylesheets' => array($this, 'block_pPageStylesheets'),
            'pStyleinline' => array($this, 'block_pStyleinline'),
            'pPageScripts' => array($this, 'block_pPageScripts'),
            'pPageBodyClass' => array($this, 'block_pPageBodyClass'),
            'pPageBodyWrapper' => array($this, 'block_pPageBodyWrapper'),
            'pEnvBannerWrapper' => array($this, 'block_pEnvBannerWrapper'),
            'pNavigationWrapper' => array($this, 'block_pNavigationWrapper'),
            'pNavigationTopMenuWrapper' => array($this, 'block_pNavigationTopMenuWrapper'),
            'pNavigationTopMenuLogo' => array($this, 'block_pNavigationTopMenuLogo'),
            'pNavigationTopBricks' => array($this, 'block_pNavigationTopBricks'),
            'pPageUIExtensionNavigationMenuTopbar' => array($this, 'block_pPageUIExtensionNavigationMenuTopbar'),
            'pNavigationSideMenuWrapper' => array($this, 'block_pNavigationSideMenuWrapper'),
            'pNavigationSideMenu' => array($this, 'block_pNavigationSideMenu'),
            'pPageUIExtensionNavigationMenuSidebar' => array($this, 'block_pPageUIExtensionNavigationMenuSidebar'),
            'pNavigationSideMenuLogo' => array($this, 'block_pNavigationSideMenuLogo'),
            'pMainWrapper' => array($this, 'block_pMainWrapper'),
            'pMainHeader' => array($this, 'block_pMainHeader'),
            'pMainContent' => array($this, 'block_pMainContent'),
            'pPageUIExtensionMainContent' => array($this, 'block_pPageUIExtensionMainContent'),
            'pPageFooter' => array($this, 'block_pPageFooter'),
            'pModalForAllWrapper' => array($this, 'block_pModalForAllWrapper'),
            'pModalForAlert' => array($this, 'block_pModalForAlert'),
            'pPageOverlay' => array($this, 'block_pPageOverlay'),
            'pPageUIExtensionBody' => array($this, 'block_pPageUIExtensionBody'),
            'pPageLiveScripts' => array($this, 'block_pPageLiveScripts'),
            'pPageLiveScriptHelpers' => array($this, 'block_pPageLiveScriptHelpers'),
            'pPageReadyScripts' => array($this, 'block_pPageReadyScripts'),
            'pPageExtensionsScripts' => array($this, 'block_pPageExtensionsScripts'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        echo "
";
        // line 4
        if (($this->getAttribute(($context["app"] ?? null), "combodo.current_user", array(), "array", true, true) &&  !(null === $this->getAttribute(($context["app"] ?? null), "combodo.current_user", array(), "array")))) {
            // line 5
            echo "\t";
            $context["bUserConnected"] = true;
            // line 6
            echo "\t";
            $context["sUserFullname"] = (($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.current_user", array(), "array"), "Get", array(0 => "first_name"), "method") . " ") . $this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.current_user", array(), "array"), "Get", array(0 => "last_name"), "method"));
            // line 7
            echo "\t";
            $context["sUserEmail"] = $this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.current_user", array(), "array"), "Get", array(0 => "email"), "method");
            // line 8
            echo "\t";
            $context["sUserPhotoUrl"] = $this->getAttribute(($context["app"] ?? null), "combodo.current_contact.photo_url", array(), "array");
        } else {
            // line 10
            echo "\t";
            $context["bUserConnected"] = false;
            // line 11
            echo "\t";
            $context["sUserFullname"] = "";
            // line 12
            echo "\t";
            $context["sUserEmail"] = "";
            // line 13
            echo "\t";
            $context["sUserPhotoUrl"] = ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . "img/user-profile-default-256px.png");
        }
        // line 15
        echo "
<!doctype html>
<html>
<head>
\t<meta charset=\"utf-8\">
\t<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
\t";
        // line 23
        echo "\t";
        $this->displayBlock('pPageExtraMetas', $context, $blocks);
        // line 25
        echo "\t<title>";
        $this->displayBlock('pPageTitle', $context, $blocks);
        echo "</title>
\t<link rel=\"shortcut icon\" href=\"";
        // line 26
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("images/favicon.ico"))), "html", null, true);
        echo "\" />

\t";
        // line 28
        $this->displayBlock('pPageStylesheets', $context, $blocks);
        // line 71
        echo "
\t";
        // line 72
        $this->displayBlock('pStyleinline', $context, $blocks);
        // line 80
        echo "
\t";
        // line 81
        $this->displayBlock('pPageScripts', $context, $blocks);
        // line 138
        echo "</head>
<body class=\"";
        // line 139
        $this->displayBlock('pPageBodyClass', $context, $blocks);
        echo "\">
\t";
        // line 140
        $this->displayBlock('pPageBodyWrapper', $context, $blocks);
        // line 376
        echo "\t
\t";
        // line 377
        $this->displayBlock('pPageLiveScripts', $context, $blocks);
        // line 472
        echo "
\t";
        // line 473
        $this->displayBlock('pPageExtensionsScripts', $context, $blocks);
        // line 481
        echo "</body>
</html>";
    }

    // line 23
    public function block_pPageExtraMetas($context, array $blocks = array())
    {
        // line 24
        echo "\t";
    }

    // line 25
    public function block_pPageTitle($context, array $blocks = array())
    {
        if ((array_key_exists("sPageTitle", $context) &&  !(null === ($context["sPageTitle"] ?? null)))) {
            echo twig_escape_filter($this->env, ($context["sPageTitle"] ?? null), "html", null, true);
            echo " - ";
            echo twig_escape_filter($this->env, twig_constant("nt3_APPLICATION_SHORT"), "html", null, true);
        } else {
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_format')->getCallable(), array("Page:DefaultTitle", twig_constant("nt3_APPLICATION_SHORT"))), "html", null, true);
        }
    }

    // line 28
    public function block_pPageStylesheets($context, array $blocks = array())
    {
        // line 29
        echo "\t\t";
        // line 30
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/bootstrap/css/bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t";
        // line 32
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t";
        // line 34
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/dataTables.bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 35
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/fixedHeader.bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 36
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/responsive.bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 37
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/scroller.bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 38
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/select.bootstrap.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 39
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/css/select.dataTables.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
        ";
        // line 41
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("css/font-open-sans/font-open-sans.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
        ";
        // line 43
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("css/font-combodo/font-combodo.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
        ";
        // line 45
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("css/font-awesome/css/font-awesome.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t";
        // line 47
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/typeahead/css/typeaheadjs.bootstrap.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 48
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("css/magnific-popup.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t<link href=\"";
        // line 49
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("css/c3.min.css"))), "html", null, true);
        echo "\" rel=\"stylesheet\">
\t\t";
        // line 51
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "themes", array()), "bootstrap", array()))), "html", null, true);
        echo "\" rel=\"stylesheet\" id=\"css_bootstrap_theme\">
\t\t";
        // line 53
        echo "\t\t<link href=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "themes", array()), "portal", array()))), "html", null, true);
        echo "\" rel=\"stylesheet\" id=\"css_portal\">
\t\t";
        // line 55
        echo "        ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "css_files", array(), "any", true, true)) {
            // line 56
            echo "            ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "css_files", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["css_file"]) {
                // line 57
                echo "\t\t\t\t<link href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($context["css_file"])), "html", null, true);
                echo "\" rel=\"stylesheet\">
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['css_file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 59
            echo "        ";
        }
        // line 60
        echo "\t\t";
        // line 61
        echo "\t\t";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "properties", array(), "any", false, true), "themes", array(), "any", false, true), "custom", array(), "any", true, true)) {
            // line 62
            echo "\t\t\t<link href=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "themes", array()), "custom", array()))), "html", null, true);
            echo "\" rel=\"stylesheet\">
\t\t";
        }
        // line 64
        echo "\t\t";
        // line 65
        echo "\t\t";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "properties", array(), "any", false, true), "themes", array(), "any", false, true), "others", array(), "any", true, true)) {
            // line 66
            echo "\t\t\t";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "themes", array()), "others", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["theme"]) {
                // line 67
                echo "\t\t\t\t<link href=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($context["theme"])), "html", null, true);
                echo "\" rel=\"stylesheet\">
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['theme'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 69
            echo "\t\t";
        }
        // line 70
        echo "\t";
    }

    // line 72
    public function block_pStyleinline($context, array $blocks = array())
    {
        // line 73
        echo "        ";
        // line 74
        echo "        ";
        if ( !(null === $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "css_inline", array()))) {
            // line 75
            echo "\t\t\t<style>
\t\t\t\t";
            // line 76
            echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "css_inline", array());
            echo "
\t\t\t</style>
        ";
        }
        // line 79
        echo "\t";
    }

    // line 81
    public function block_pPageScripts($context, array $blocks = array())
    {
        // line 82
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery-1.12.4.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 83
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery-migrate-1.4.1.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 84
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery-ui-1.11.4.custom.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 85
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery.magnific-popup.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 86
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery.iframe-transport.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 87
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/jquery.fileupload.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 88
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/d3.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 89
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/c3.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 90
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/bootstrap/js/bootstrap.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 91
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/latinise/latinise.min.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 93
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/jquery-visible/js/jquery.visible.min.js"))), "html", null, true);
        echo "\"></script>
        ";
        // line 95
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/jquery-base64/js/jquery.base64.min.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 97
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/moment/js/moment.min.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 99
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/jquery.dataTables.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 100
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/dataTables.bootstrap.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 101
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/dataTables.fixedHeader.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 102
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/dataTables.responsive.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 103
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/dataTables.scroller.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 104
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/dataTables.select.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 105
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/datatables/js/datetime-moment.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 106
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/dataTables.accentNeutraliseForFilter.js"))), "html", null, true);
        echo "\"></script>
        ";
        // line 108
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/export.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 110
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/ckeditor/ckeditor.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 111
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/ckeditor/adapters/jquery.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 113
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 115
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/typeahead/js/typeahead.bundle.min.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 116
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("lib/handlebars/js/handlebars.min-768ddbd.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 118
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/form_handler.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 119
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/form_field.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 120
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/subform_field.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 121
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/field_set.js"))), "html", null, true);
        echo "\"></script>
\t\t";
        // line 123
        echo "\t\t<script type=\"text/javascript\" src=\"";
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/portal_form_handler.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 124
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/portal_form_field.js"))), "html", null, true);
        echo "\"></script>
\t\t<script type=\"text/javascript\" src=\"";
        // line 125
        echo twig_escape_filter($this->env, ($this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array") . call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array("js/portal_form_field_html.js"))), "html", null, true);
        echo "\"></script>
        ";
        // line 127
        echo "        ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "js_files", array(), "any", true, true)) {
            // line 128
            echo "\t\t\t";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "js_files", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["js_file"]) {
                // line 129
                echo "\t\t\t\t<script type=\"text/javascript\" src=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('add_nt3_version')->getCallable(), array($context["js_file"])), "html", null, true);
                echo "\"></script>
\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['js_file'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 131
            echo "        ";
        }
        // line 132
        echo "\t\t<script type=\"text/javascript\">
\t\t\t\$(document).ready(function() {
\t\t\t\t\$('.tile.tile_badge[data-toggle=\"tooltip\"]').tooltip({'html': true});
\t\t\t});
\t\t</script>
\t";
    }

    // line 139
    public function block_pPageBodyClass($context, array $blocks = array())
    {
    }

    // line 140
    public function block_pPageBodyWrapper($context, array $blocks = array())
    {
        // line 141
        echo "\t\t";
        $this->displayBlock('pEnvBannerWrapper', $context, $blocks);
        // line 151
        echo "\t\t
\t\t";
        // line 152
        $this->displayBlock('pNavigationWrapper', $context, $blocks);
        // line 301
        echo "\t\t
\t\t";
        // line 302
        $this->displayBlock('pMainWrapper', $context, $blocks);
        // line 326
        echo "\t\t
\t\t<footer id=\"footer-wrapper\">
\t\t\t";
        // line 328
        $this->displayBlock('pPageFooter', $context, $blocks);
        // line 331
        echo "\t\t</footer>
\t
\t\t";
        // line 333
        $this->displayBlock('pModalForAllWrapper', $context, $blocks);
        // line 342
        echo "\t\t";
        $this->displayBlock('pModalForAlert', $context, $blocks);
        // line 361
        echo "
\t\t";
        // line 362
        $this->displayBlock('pPageOverlay', $context, $blocks);
        // line 369
        echo "
\t\t";
        // line 370
        $this->displayBlock('pPageUIExtensionBody', $context, $blocks);
        // line 375
        echo "\t";
    }

    // line 141
    public function block_pEnvBannerWrapper($context, array $blocks = array())
    {
        // line 142
        echo "\t\t\t";
        if (($this->getAttribute(($context["app"] ?? null), "combodo.current_environment", array(), "array") != "production")) {
            // line 143
            echo "\t\t\t\t<div id=\"envbanner\" class=\"alert alert-danger\" role=\"alert\">
\t\t\t\t\t";
            // line 144
            echo call_user_func_array($this->env->getFilter('dict_format')->getCallable(), array("Portal:EnvironmentBanner:Title", twig_upper_filter($this->env, $this->getAttribute(($context["app"] ?? null), "combodo.current_environment", array(), "array"))));
            echo "
\t\t\t\t\t<button type=\"button\" onclick=\"window;location.href='";
            // line 145
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array(), "array"), "generate", array(0 => "p_home", 1 => array("switch_env" => "production")), "method"), "html", null, true);
            echo "'\">
\t\t\t\t\t\t";
            // line 146
            echo call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:EnvironmentBanner:GoToProduction"));
            echo "
\t\t\t\t\t</button>
\t\t\t\t</div>
\t\t\t";
        }
        // line 150
        echo "\t\t";
    }

    // line 152
    public function block_pNavigationWrapper($context, array $blocks = array())
    {
        // line 153
        echo "\t\t\t";
        // line 154
        echo "\t\t\t";
        $this->displayBlock('pNavigationTopMenuWrapper', $context, $blocks);
        // line 225
        echo "
\t\t\t";
        // line 227
        echo "\t\t\t";
        $this->displayBlock('pNavigationSideMenuWrapper', $context, $blocks);
        // line 300
        echo "\t\t";
    }

    // line 154
    public function block_pNavigationTopMenuWrapper($context, array $blocks = array())
    {
        // line 155
        echo "\t\t\t\t<nav class=\"navbar navbar-fixed-top navbar-default visible-xs\" id=\"topbar\" role=\"navigation\">
\t\t\t\t\t<div class=\"container-fluid\">
\t\t\t\t\t\t<div class=\"navbar-header\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"navbar-toggle collapsed\" data-toggle=\"collapse\" data-target=\"#navbar\">
\t\t\t\t\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t\t\t\t\t<span class=\"icon-bar\"></span>
\t\t\t\t\t\t\t</button>
\t\t\t\t\t\t\t";
        // line 163
        $this->displayBlock('pNavigationTopMenuLogo', $context, $blocks);
        // line 172
        echo "\t\t\t\t\t\t\t<p class=\"navbar-text\">
\t\t\t\t\t\t\t\t<a class=\"navbar-link user_infos\" href=\"";
        // line 173
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_user_profile_brick"), "method"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t<span class=\"user_photo\" style=\"background-image: url('";
        // line 174
        echo twig_escape_filter($this->env, ($context["sUserPhotoUrl"] ?? null), "html", null, true);
        echo "');\"></span>
\t\t\t\t\t\t\t\t\t<span class=\"user_fullname\">";
        // line 175
        echo twig_escape_filter($this->env, ($context["sUserFullname"] ?? null), "html", null, true);
        echo "</span>
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t</p>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"collapse navbar-collapse\" id=\"navbar\">
\t\t\t\t\t\t\t<ul class=\"nav navbar-nav\">
\t\t\t\t\t\t\t\t";
        // line 181
        $this->displayBlock('pNavigationTopBricks', $context, $blocks);
        // line 199
        echo "\t\t\t\t\t\t\t\t";
        if (($context["bUserConnected"] ?? null)) {
            // line 200
            echo "\t\t\t\t\t\t\t\t\t<li role=\"separator\" class=\"divider\"></li>
\t\t\t\t\t\t\t\t\t<li><a href=\"";
            // line 201
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_user_profile_brick"), "method"), "html", null, true);
            echo "\"><span class=\"brick_icon fa fa-user fa-2x fa-fw\"></span>";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Navigation:Dropdown:MyProfil")), "html", null, true);
            echo "</a></li>
\t\t\t\t\t\t\t\t\t";
            // line 202
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "portals", array()));
            foreach ($context['_seq'] as $context["_key"] => $context["aPortal"]) {
                // line 203
                echo "\t\t\t\t\t\t\t\t\t\t";
                if (($this->getAttribute($context["aPortal"], "id", array()) != $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "id", array()))) {
                    // line 204
                    echo "\t\t\t\t\t\t\t\t\t\t\t";
                    $context["sIconClass"] = ((($this->getAttribute($context["aPortal"], "id", array()) == "backoffice")) ? ("fa-list-alt") : ("fa-external-link"));
                    // line 205
                    echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["aPortal"], "url", array()), "html", null, true);
                    echo "\" target=\"_blank\"><span class=\"brick_icon fa ";
                    echo twig_escape_filter($this->env, ($context["sIconClass"] ?? null), "html", null, true);
                    echo " fa-2x fa-fw\"></span>";
                    echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($context["aPortal"], "label", array()))), "html", null, true);
                    echo "</a></li>
\t\t\t\t\t\t\t\t\t\t";
                }
                // line 207
                echo "\t\t\t\t\t\t\t\t\t";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aPortal'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 208
            echo "\t\t\t\t\t\t\t\t\t";
            // line 209
            echo "\t\t\t\t\t\t\t\t\t";
            if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "portals", array())) > 1)) {
                // line 210
                echo "\t\t\t\t\t\t\t\t\t\t<li role=\"separator\" class=\"divider\"></li>
\t\t\t\t\t\t\t\t\t";
            }
            // line 212
            echo "\t\t\t\t\t\t\t\t\t<li><a href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array"), "html", null, true);
            echo "pages/logoff.php\"><span class=\"brick_icon fa fa-sign-out fa-2x fa-fw\"></span>";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Navigation:Dropdown:Logout")), "html", null, true);
            echo "</a></li>
\t\t\t\t\t\t\t\t";
        }
        // line 214
        echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t</div>

                        ";
        // line 217
        $this->displayBlock('pPageUIExtensionNavigationMenuTopbar', $context, $blocks);
        // line 222
        echo "\t\t\t\t\t</div>
\t\t\t\t</nav>
\t\t\t";
    }

    // line 163
    public function block_pNavigationTopMenuLogo($context, array $blocks = array())
    {
        // line 164
        echo "\t\t\t\t\t\t\t\t<a class=\"navbar-brand pull-right\" href=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_home"), "method"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t";
        // line 165
        if ( !(null === $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "logo", array()))) {
            // line 166
            echo "\t\t\t\t\t\t\t\t\t\t<img src=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "logo", array()), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "name", array()))), "html", null, true);
            echo "\" />
\t\t\t\t\t\t\t\t\t";
        } else {
            // line 168
            echo "\t\t\t\t\t\t\t\t\t\tnt3
\t\t\t\t\t\t\t\t\t";
        }
        // line 170
        echo "\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t";
    }

    // line 181
    public function block_pNavigationTopBricks($context, array $blocks = array())
    {
        // line 182
        echo "\t\t\t\t\t\t\t\t\t<li class=\"";
        if ( !array_key_exists("oBrick", $context)) {
            echo "active";
        }
        echo "\">
\t\t\t\t\t\t\t\t\t\t<a href=\"";
        // line 183
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_home"), "method"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t\t\t<span class=\"brick_icon fa fa-home fa-2x\"></span>
\t\t\t\t\t\t\t\t\t\t\t";
        // line 185
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:Home")), "html", null, true);
        echo "
\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
        // line 188
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "bricks_ordering", array()), "navigation_menu", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["brick"]) {
            // line 189
            echo "\t\t\t\t\t\t\t\t\t\t";
            if ((($this->getAttribute($context["brick"], "GetActive", array()) && $this->getAttribute($context["brick"], "GetVisibleNavigationMenu", array())) &&  !(null === $this->getAttribute($context["brick"], "GetRouteName", array())))) {
                // line 190
                echo "\t\t\t\t\t\t\t\t\t\t\t<li class=\"";
                if ((array_key_exists("oBrick", $context) && ($this->getAttribute($context["brick"], "id", array()) == $this->getAttribute(($context["oBrick"] ?? null), "id", array())))) {
                    echo "active";
                }
                echo "\">
\t\t\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 191
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => $this->getAttribute($context["brick"], "GetRouteName", array()), 1 => array("sBrickId" => $this->getAttribute($context["brick"], "GetId", array()))), "method"), "html", null, true);
                if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array", false, true), "hash", array(), "array", true, true)) {
                    echo "#";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array"), "hash", array(), "array"), "html", null, true);
                }
                echo "\" ";
                if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array", false, true), "navigation_menu_attr", array(), "array", true, true)) {
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array"), "navigation_menu_attr", array(), "array"));
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
                echo " ";
                if ($this->getAttribute($context["brick"], "GetModal", array())) {
                    echo "data-toggle=\"modal\" data-target=\"#modal-for-all\"";
                }
                echo ">
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"brick_icon ";
                // line 192
                echo twig_escape_filter($this->env, $this->getAttribute($context["brick"], "GetDecorationClassNavigationMenu", array()), "html", null, true);
                echo "\"></span>
\t\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 193
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($context["brick"], "GetTitleNavigationMenu", array()))), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t\t";
            }
            // line 197
            echo "\t\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['brick'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 198
        echo "\t\t\t\t\t\t\t\t";
    }

    // line 217
    public function block_pPageUIExtensionNavigationMenuTopbar($context, array $blocks = array())
    {
        // line 218
        echo "                            ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "html", array(), "any", false, true), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_NAVIGATION_MENU"), array(), "array", true, true)) {
            // line 219
            echo "                                ";
            echo $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "html", array()), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_NAVIGATION_MENU"), array(), "array");
            echo "
                            ";
        }
        // line 221
        echo "                        ";
    }

    // line 227
    public function block_pNavigationSideMenuWrapper($context, array $blocks = array())
    {
        // line 228
        echo "\t\t\t\t<nav class=\"navbar-default hidden-xs col-sm-3 col-md-2\" id=\"sidebar\" role=\"navigation\">
\t\t\t\t\t<div class=\"user_card bg-primary\">
\t\t\t\t\t\t<div class=\"user_photo\" style=\"background-image: url('";
        // line 230
        echo twig_escape_filter($this->env, ($context["sUserPhotoUrl"] ?? null), "html", null, true);
        echo "');\">
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"user_infos\">
\t\t\t\t\t\t\t<div class=\"user_fullname\">";
        // line 233
        echo twig_escape_filter($this->env, ($context["sUserFullname"] ?? null), "html", null, true);
        echo "</div>
\t\t\t\t\t\t\t<div class=\"user_email dropdown\">
\t\t\t\t\t\t\t\t<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\" id=\"user_options\">
\t\t\t\t\t\t\t\t\t";
        // line 236
        echo twig_escape_filter($this->env, ($context["sUserEmail"] ?? null), "html", null, true);
        echo "
\t\t\t\t\t\t\t\t\t<span class=\"caret\"></span>
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t<ul class=\"dropdown-menu user_options\" aria-labelledby=\"user_options\">
\t\t\t\t\t\t\t\t\t<li><a href=\"";
        // line 240
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_user_profile_brick"), "method"), "html", null, true);
        echo "\"><span class=\"brick_icon fa fa-user fa-lg fa-fw\"></span>";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Navigation:Dropdown:MyProfil")), "html", null, true);
        echo "</a></li>
\t\t\t\t\t\t\t\t\t";
        // line 241
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "portals", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["aPortal"]) {
            // line 242
            echo "\t\t\t\t\t\t\t\t\t\t";
            if (($this->getAttribute($context["aPortal"], "id", array()) != $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "id", array()))) {
                // line 243
                echo "\t\t\t\t\t\t\t\t\t\t\t";
                $context["sGlyphiconClass"] = ((($this->getAttribute($context["aPortal"], "id", array()) == "backoffice")) ? ("fa-list-alt") : ("fa-external-link"));
                // line 244
                echo "\t\t\t\t\t\t\t\t\t\t\t<li><a href=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["aPortal"], "url", array()), "html", null, true);
                echo "\" ";
                if (($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "allowed_portals", array()), "opening_mode", array()) == "tab")) {
                    echo "target=\"_blank\"";
                }
                echo " title=\"";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($context["aPortal"], "label", array()))), "html", null, true);
                echo "\"><span class=\"brick_icon fa ";
                echo twig_escape_filter($this->env, ($context["sGlyphiconClass"] ?? null), "html", null, true);
                echo " fa-lg fa-fw\"></span>";
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($context["aPortal"], "label", array()))), "html", null, true);
                echo "</a></li>
\t\t\t\t\t\t\t\t\t\t";
            }
            // line 246
            echo "\t\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aPortal'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 247
        echo "\t\t\t\t\t\t\t\t\t";
        // line 248
        echo "\t\t\t\t\t\t\t\t\t";
        if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "portals", array())) > 1)) {
            // line 249
            echo "\t\t\t\t\t\t\t\t\t\t<li role=\"separator\" class=\"divider\"></li>
\t\t\t\t\t\t\t\t\t";
        }
        // line 251
        echo "\t\t\t\t\t\t\t\t\t<li><a href=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array"), "html", null, true);
        echo "pages/logoff.php\"><span class=\"brick_icon fa fa-sign-out fa-lg fa-fw\"></span>";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Navigation:Dropdown:Logout")), "html", null, true);
        echo "</a></li>
\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"menu\">
\t\t\t\t\t\t";
        // line 257
        $this->displayBlock('pNavigationSideMenu', $context, $blocks);
        // line 277
        echo "\t\t\t\t\t</div>

                    ";
        // line 279
        $this->displayBlock('pPageUIExtensionNavigationMenuSidebar', $context, $blocks);
        // line 284
        echo "
\t\t\t\t\t";
        // line 285
        if ( !(null === $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "logo", array()))) {
            // line 286
            echo "\t\t\t\t\t\t<div class=\"logo\">
\t\t\t\t\t\t\t";
            // line 287
            $this->displayBlock('pNavigationSideMenuLogo', $context, $blocks);
            // line 296
            echo "\t\t\t\t\t\t</div>
\t\t\t\t\t";
        }
        // line 298
        echo "\t\t\t\t</nav>
\t\t\t";
    }

    // line 257
    public function block_pNavigationSideMenu($context, array $blocks = array())
    {
        // line 258
        echo "\t\t\t\t\t\t\t<ul class=\"nav navbar-nav\">
\t\t\t\t\t\t\t\t<li class=\"";
        // line 259
        if ( !array_key_exists("oBrick", $context)) {
            echo "active";
        }
        echo "\">
\t\t\t\t\t\t\t\t\t<a href=\"";
        // line 260
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_home"), "method"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t\t<span class=\"brick_icon fa fa-home fa-2x\"></span>
\t\t\t\t\t\t\t\t\t\t";
        // line 262
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:Home")), "html", null, true);
        echo "
\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t";
        // line 265
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "bricks_ordering", array()), "navigation_menu", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["brick"]) {
            // line 266
            echo "\t\t\t\t\t\t\t\t\t";
            if ((($this->getAttribute($context["brick"], "GetActive", array()) && $this->getAttribute($context["brick"], "GetVisibleNavigationMenu", array())) &&  !(null === $this->getAttribute($context["brick"], "GetRouteName", array())))) {
                // line 267
                echo "\t\t\t\t\t\t\t\t\t\t<li class=\"";
                if ((array_key_exists("oBrick", $context) && ($this->getAttribute($context["brick"], "id", array()) == $this->getAttribute(($context["oBrick"] ?? null), "id", array())))) {
                    echo "active";
                }
                echo "\">
\t\t\t\t\t\t\t\t\t\t\t<a href=\"";
                // line 268
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => $this->getAttribute($context["brick"], "GetRouteName", array()), 1 => array("sBrickId" => $this->getAttribute($context["brick"], "GetId", array()))), "method"), "html", null, true);
                if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array", false, true), "hash", array(), "array", true, true)) {
                    echo "#";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array"), "hash", array(), "array"), "html", null, true);
                }
                echo "\" ";
                if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array", false, true), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array", false, true), "navigation_menu_attr", array(), "array", true, true)) {
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.routes", array(), "array"), $this->getAttribute($context["brick"], "GetRouteName", array()), array(), "array"), "navigation_menu_attr", array(), "array"));
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
                echo " ";
                if ($this->getAttribute($context["brick"], "GetModal", array())) {
                    echo "data-toggle=\"modal\" data-target=\"#modal-for-all\"";
                }
                echo ">
\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"brick_icon ";
                // line 269
                echo twig_escape_filter($this->env, $this->getAttribute($context["brick"], "GetDecorationClassNavigationMenu", array()), "html", null, true);
                echo "\"></span>
\t\t\t\t\t\t\t\t\t\t\t\t";
                // line 270
                echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($context["brick"], "GetTitleNavigationMenu", array()))), "html", null, true);
                echo "
\t\t\t\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t\t\t\t</li>
\t\t\t\t\t\t\t\t\t";
            }
            // line 274
            echo "\t\t\t\t\t\t\t\t";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['brick'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 275
        echo "\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t";
    }

    // line 279
    public function block_pPageUIExtensionNavigationMenuSidebar($context, array $blocks = array())
    {
        // line 280
        echo "                        ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "html", array(), "any", false, true), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_NAVIGATION_MENU"), array(), "array", true, true)) {
            // line 281
            echo "                            ";
            echo $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "html", array()), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_NAVIGATION_MENU"), array(), "array");
            echo "
                        ";
        }
        // line 283
        echo "                    ";
    }

    // line 287
    public function block_pNavigationSideMenuLogo($context, array $blocks = array())
    {
        // line 288
        echo "\t\t\t\t\t\t\t\t";
        // line 289
        echo "\t\t\t\t\t\t\t\t";
        if ($this->getAttribute(($context["app"] ?? null), "debug", array(), "array")) {
            // line 290
            echo "\t\t\t\t\t\t\t\t\t<div style=\"position: fixed; bottom: 0px; left: 0px; z-index: 9999;\">Debug : Taille <span class=\"hidden-sm hidden-md hidden-lg\">XS</span><span class=\"hidden-xs hidden-md hidden-lg\">SM</span><span class=\"hidden-xs hidden-sm hidden-lg\">MD</span><span class=\"hidden-xs hidden-sm hidden-md\">LG</span></div>
\t\t\t\t\t\t\t\t";
        }
        // line 292
        echo "\t\t\t\t\t\t\t\t<a href=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_home"), "method"), "html", null, true);
        echo "\" title=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "name", array()))), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t<img src=\"";
        // line 293
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "logo", array()), "html", null, true);
        echo "\" alt=\"";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "properties", array()), "name", array()))), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t\t</a>
\t\t\t\t\t\t\t";
    }

    // line 302
    public function block_pMainWrapper($context, array $blocks = array())
    {
        // line 303
        echo "\t\t<div class=\"container-fluid\" id=\"main-wrapper\">
\t\t\t<div class=\"row\">
\t\t\t\t<div class=\"col-xs-12 col-sm-9 col-md-10 col-sm-offset-3 col-md-offset-2\">
\t\t\t\t\t
\t\t\t\t\t<section class=\"row\" id=\"main-header\">
\t\t\t\t\t\t";
        // line 308
        $this->displayBlock('pMainHeader', $context, $blocks);
        // line 310
        echo "\t\t\t\t\t</section>

\t\t\t\t\t<section class=\"row\" id=\"main-content\">
\t\t\t\t\t\t";
        // line 313
        $this->displayBlock('pMainContent', $context, $blocks);
        // line 315
        echo "\t\t\t\t\t</section>
\t\t\t\t</div>
\t\t\t</div>

\t\t\t";
        // line 319
        $this->displayBlock('pPageUIExtensionMainContent', $context, $blocks);
        // line 324
        echo "\t\t</div>
\t\t";
    }

    // line 308
    public function block_pMainHeader($context, array $blocks = array())
    {
        // line 309
        echo "\t\t\t\t\t\t";
    }

    // line 313
    public function block_pMainContent($context, array $blocks = array())
    {
        // line 314
        echo "\t\t\t\t\t\t";
    }

    // line 319
    public function block_pPageUIExtensionMainContent($context, array $blocks = array())
    {
        // line 320
        echo "                ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "html", array(), "any", false, true), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_MAIN_CONTENT"), array(), "array", true, true)) {
            // line 321
            echo "                    ";
            echo $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "html", array()), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_MAIN_CONTENT"), array(), "array");
            echo "
                ";
        }
        // line 323
        echo "\t\t\t";
    }

    // line 328
    public function block_pPageFooter($context, array $blocks = array())
    {
        // line 329
        echo "\t\t\t\t";
        // line 330
        echo "\t\t\t";
    }

    // line 333
    public function block_pModalForAllWrapper($context, array $blocks = array())
    {
        // line 334
        echo "\t\t\t<div class=\"modal fade\" id=\"modal-for-all\" role=\"dialog\">
\t\t\t\t<div class=\"modal-dialog modal-lg\" role=\"document\">
\t\t\t\t\t<div class=\"modal-content\">
\t\t\t\t\t\t";
        // line 337
        $this->loadTemplate("nt3-portal-base/portal/src/views/helpers/loader.html.twig", "nt3-portal-base/portal/src/views/layout.html.twig", 337)->display($context);
        // line 338
        echo "\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 342
    public function block_pModalForAlert($context, array $blocks = array())
    {
        // line 343
        echo "\t\t\t<div class=\"modal fade\" id=\"modal-for-alert\" role=\"dialog\">
\t\t\t\t<div class=\"modal-dialog\" role=\"document\">
\t\t\t\t\t<div class=\"modal-content\">
\t\t\t\t\t\t<div class=\"modal-header\">
\t\t\t\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
        // line 347
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Close")), "html", null, true);
        echo "\"><span aria-hidden=\"true\">&times;</span></button>
\t\t\t\t\t\t\t<h4 class=\"modal-title\"></h4>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"modal-body\">
\t\t\t\t\t\t\t<div class=\"alert\">
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"text-right\">
\t\t\t\t\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">";
        // line 354
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Close")), "html", null, true);
        echo "</button>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 362
    public function block_pPageOverlay($context, array $blocks = array())
    {
        // line 363
        echo "\t\t\t<div id=\"page_overlay\" class=\"global_overlay\">
\t\t\t\t<div class=\"overlay_content\">
\t\t\t\t\t";
        // line 365
        $this->loadTemplate("nt3-portal-base/portal/src/views/helpers/loader.html.twig", "nt3-portal-base/portal/src/views/layout.html.twig", 365)->display($context);
        // line 366
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t";
    }

    // line 370
    public function block_pPageUIExtensionBody($context, array $blocks = array())
    {
        // line 371
        echo "\t\t\t";
        if ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array", false, true), "ui_extensions", array(), "any", false, true), "html", array(), "any", false, true), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_BODY"), array(), "array", true, true)) {
            // line 372
            echo "\t\t\t\t";
            echo $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "html", array()), twig_constant("iPortalUIExtension::ENUM_PORTAL_EXT_UI_BODY"), array(), "array");
            echo "
\t\t\t";
        }
        // line 374
        echo "\t\t";
    }

    // line 377
    public function block_pPageLiveScripts($context, array $blocks = array())
    {
        // line 378
        echo "\t\t<script type=\"text/javascript\">
\t\t\t";
        // line 379
        $this->displayBlock('pPageLiveScriptHelpers', $context, $blocks);
        // line 423
        echo "\t\t\t
\t\t\t\$(document).ready(function(){
\t\t\t\t";
        // line 425
        $this->displayBlock('pPageReadyScripts', $context, $blocks);
        // line 469
        echo "\t\t\t});
\t\t</script>
\t";
    }

    // line 379
    public function block_pPageLiveScriptHelpers($context, array $blocks = array())
    {
        // line 380
        echo "\t\t\t\t// Helper to get the application root url
\t\t\t\tvar GetAbsoluteUrlAppRoot = function()
\t\t\t\t{
\t\t\t\t\treturn '";
        // line 383
        echo twig_escape_filter($this->env, $this->getAttribute(($context["app"] ?? null), "combodo.absolute_url", array(), "array"), "html", null, true);
        echo "';
\t\t\t\t};
\t\t\t\t// Helper to add a parameter to an url
\t\t\t\tvar AddParameterToUrl = function(sUrl, sParamName, sParamValue)
\t\t\t\t{
\t\t\t\t\tsUrl += (sUrl.split('?')[1] ? '&':'?') + sParamName + '=' + sParamValue;
\t\t\t\t\treturn sUrl;
\t\t\t\t};
\t\t\t\tvar GetContentLoaderTemplate = function()
\t\t\t\t{
\t\t\t\t\treturn '<div class=\"content_loader\"><div class=\"icon glyphicon glyphicon-refresh\"></div><div class=\"message\">";
        // line 393
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Page:PleaseWait")), "html", null, true);
        echo "</div></div>';
\t\t\t\t}
\t\t\t\tvar ShowLoginDialog = function()
\t\t\t\t{
                    var oModalElem = \$('#modal-for-alert').clone();
                    oModalElem.attr('id', '');
                    oModalElem.find('.modal-content .modal-header .modal-title').html('";
        // line 399
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Error:HTTP:401")), "js"), "html", null, true);
        echo "');
                    oModalElem.find('.modal-content .modal-body .alert').addClass('alert-danger').html('";
        // line 400
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:ErrorUserLoggedOut")), "js"), "html", null, true);
        echo "');

                    oModalElem.find('.modal-content .modal-body button').replaceWith( \$('<button type=\"button\" class=\"btn btn-primary\" onclick=\"javascript:window.location.reload();\">";
        // line 402
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("UI:LogOff:ClickHereToLoginAgain")), "js"), "html", null, true);
        echo "</button>') );

                    oModalElem.appendTo('body');
                    oModalElem.modal('show');
\t\t\t\t};
\t\t\t\tvar ShowErrorDialog = function(sBody, sTitle)
\t\t\t\t{
\t\t\t\t    if(sTitle === undefined)
\t\t\t\t\t{
\t\t\t\t\t    sTitle = '";
        // line 411
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Error:HTTP:500")), "js"), "html", null, true);
        echo "';
\t\t\t\t\t}
\t\t\t\t    if(sBody === undefined)
\t\t\t\t\t{
                        sBody = '";
        // line 415
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_format')->getCallable(), array("Error:XHR:Fail", twig_constant("nt3_APPLICATION_SHORT"))), "js"), "html", null, true);
        echo "';
\t\t\t\t\t}
\t\t\t\t\tvar oModalElem = \$('#modal-for-alert');
                    oModalElem.find('.modal-content .modal-header .modal-title').html(sTitle);
                    oModalElem.find('.modal-content .modal-body .alert').addClass('alert-danger').html(sBody);
                    oModalElem.modal('show');
\t\t\t\t};
\t\t\t";
    }

    // line 425
    public function block_pPageReadyScripts($context, array $blocks = array())
    {
        // line 426
        echo "\t\t\t\t\t// Hack to enable a same modal to load content from different urls
\t\t\t\t\t\$('body').on('hidden.bs.modal', '.modal#modal-for-all', function () {
\t\t\t\t\t\t\$(this).removeData('bs.modal');
\t\t\t\t\t\t\$(this).find('.modal-content').html(GetContentLoaderTemplate());
\t\t\t\t\t});
\t\t\t\t\t// Hack to enable multiple modals by making sure the .modal-open class is set to the <body> when there is at least one modal open left
\t\t\t\t\t\$('body').on('hidden.bs.modal', function () {
\t\t\t\t\t\tif(\$('.modal.in').length > 0)
\t\t\t\t\t\t{
\t\t\t\t\t\t\t\$('body').addClass('modal-open');
\t\t\t\t\t\t}
\t\t\t\t\t});
\t\t\t\t\t// Hide tooltips when a modal is opening, otherwise it might be overlapping it
\t\t\t\t\t\$('body').on('show.bs.modal', function () {
\t\t\t\t\t\t\$(this).find('.tooltip.in').tooltip('hide');
\t\t\t\t\t});
\t\t\t\t\t// Display a error message on modal if the content could not be loaded.
\t\t\t\t\t// Note : As of now, we can't display a more detailled message based on the response because Bootstrap doesn't pass response data with the loaded event.
\t\t\t\t\t\$('body').on('loaded.bs.modal', function (oEvent) {
\t\t\t\t\t\tvar sModalContent = \$(oEvent.target).find('.modal-content').html();
\t\t\t\t\t\t
\t\t\t\t\t\tif( (sModalContent === '') || (sModalContent.replace(/[\\n\\r\\t]+/g, '') === GetContentLoaderTemplate()) )
\t\t\t\t\t\t{
\t\t\t\t\t\t\t\$(oEvent.target).modal('hide');
\t\t\t\t\t\t}
\t\t\t\t\t});

\t\t\t\t\t// Handle AJAX errors (exceptions (500), logout (401), ...)
\t\t\t\t\t\$(document).ajaxError(function(oEvent, oXHR, oSettings, sError){
\t\t\t\t\t    if(oXHR.status === 401)
\t\t\t\t\t\t{
\t\t\t\t\t\t    ShowLoginDialog();
\t\t\t\t\t\t}
                        else if(oXHR.status === 404)
                        {
                            ShowErrorDialog('";
        // line 461
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("UI:ObjectDoesNotExist")), "js"), "html", null, true);
        echo "', '";
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Error:HTTP:404")), "js"), "html", null, true);
        echo "');
                        }
                        else
                        {
                            ShowErrorDialog();
                        }
\t\t\t\t\t});
\t\t\t\t";
    }

    // line 473
    public function block_pPageExtensionsScripts($context, array $blocks = array())
    {
        // line 474
        echo "        ";
        // line 475
        echo "        ";
        if ( !(null === $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "js_inline", array()))) {
            // line 476
            echo "\t\t\t<script type=\"text/javascript\">
\t\t\t\t";
            // line 477
            echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["app"] ?? null), "combodo.portal.instance.conf", array(), "array"), "ui_extensions", array()), "js_inline", array());
            echo "
\t\t\t</script>
        ";
        }
        // line 480
        echo "\t";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1371 => 480,  1365 => 477,  1362 => 476,  1359 => 475,  1357 => 474,  1354 => 473,  1340 => 461,  1303 => 426,  1300 => 425,  1288 => 415,  1281 => 411,  1269 => 402,  1264 => 400,  1260 => 399,  1251 => 393,  1238 => 383,  1233 => 380,  1230 => 379,  1224 => 469,  1222 => 425,  1218 => 423,  1216 => 379,  1213 => 378,  1210 => 377,  1206 => 374,  1200 => 372,  1197 => 371,  1194 => 370,  1188 => 366,  1186 => 365,  1182 => 363,  1179 => 362,  1168 => 354,  1158 => 347,  1152 => 343,  1149 => 342,  1142 => 338,  1140 => 337,  1135 => 334,  1132 => 333,  1128 => 330,  1126 => 329,  1123 => 328,  1119 => 323,  1113 => 321,  1110 => 320,  1107 => 319,  1103 => 314,  1100 => 313,  1096 => 309,  1093 => 308,  1088 => 324,  1086 => 319,  1080 => 315,  1078 => 313,  1073 => 310,  1071 => 308,  1064 => 303,  1061 => 302,  1052 => 293,  1045 => 292,  1041 => 290,  1038 => 289,  1036 => 288,  1033 => 287,  1029 => 283,  1023 => 281,  1020 => 280,  1017 => 279,  1012 => 275,  1006 => 274,  999 => 270,  995 => 269,  968 => 268,  961 => 267,  958 => 266,  954 => 265,  948 => 262,  943 => 260,  937 => 259,  934 => 258,  931 => 257,  926 => 298,  922 => 296,  920 => 287,  917 => 286,  915 => 285,  912 => 284,  910 => 279,  906 => 277,  904 => 257,  892 => 251,  888 => 249,  885 => 248,  883 => 247,  877 => 246,  861 => 244,  858 => 243,  855 => 242,  851 => 241,  845 => 240,  838 => 236,  832 => 233,  826 => 230,  822 => 228,  819 => 227,  815 => 221,  809 => 219,  806 => 218,  803 => 217,  799 => 198,  793 => 197,  786 => 193,  782 => 192,  755 => 191,  748 => 190,  745 => 189,  741 => 188,  735 => 185,  730 => 183,  723 => 182,  720 => 181,  715 => 170,  711 => 168,  703 => 166,  701 => 165,  696 => 164,  693 => 163,  687 => 222,  685 => 217,  680 => 214,  672 => 212,  668 => 210,  665 => 209,  663 => 208,  657 => 207,  647 => 205,  644 => 204,  641 => 203,  637 => 202,  631 => 201,  628 => 200,  625 => 199,  623 => 181,  614 => 175,  610 => 174,  606 => 173,  603 => 172,  601 => 163,  591 => 155,  588 => 154,  584 => 300,  581 => 227,  578 => 225,  575 => 154,  573 => 153,  570 => 152,  566 => 150,  559 => 146,  555 => 145,  551 => 144,  548 => 143,  545 => 142,  542 => 141,  538 => 375,  536 => 370,  533 => 369,  531 => 362,  528 => 361,  525 => 342,  523 => 333,  519 => 331,  517 => 328,  513 => 326,  511 => 302,  508 => 301,  506 => 152,  503 => 151,  500 => 141,  497 => 140,  492 => 139,  483 => 132,  480 => 131,  471 => 129,  466 => 128,  463 => 127,  459 => 125,  455 => 124,  450 => 123,  446 => 121,  442 => 120,  438 => 119,  433 => 118,  429 => 116,  424 => 115,  419 => 113,  415 => 111,  410 => 110,  405 => 108,  401 => 106,  397 => 105,  393 => 104,  389 => 103,  385 => 102,  381 => 101,  377 => 100,  372 => 99,  367 => 97,  362 => 95,  357 => 93,  353 => 91,  349 => 90,  345 => 89,  341 => 88,  337 => 87,  333 => 86,  329 => 85,  325 => 84,  321 => 83,  316 => 82,  313 => 81,  309 => 79,  303 => 76,  300 => 75,  297 => 74,  295 => 73,  292 => 72,  288 => 70,  285 => 69,  276 => 67,  271 => 66,  268 => 65,  266 => 64,  260 => 62,  257 => 61,  255 => 60,  252 => 59,  243 => 57,  238 => 56,  235 => 55,  230 => 53,  225 => 51,  221 => 49,  217 => 48,  212 => 47,  207 => 45,  202 => 43,  197 => 41,  193 => 39,  189 => 38,  185 => 37,  181 => 36,  177 => 35,  172 => 34,  167 => 32,  162 => 30,  160 => 29,  157 => 28,  145 => 25,  141 => 24,  138 => 23,  133 => 481,  131 => 473,  128 => 472,  126 => 377,  123 => 376,  121 => 140,  117 => 139,  114 => 138,  112 => 81,  109 => 80,  107 => 72,  104 => 71,  102 => 28,  97 => 26,  92 => 25,  89 => 23,  80 => 15,  76 => 13,  73 => 12,  70 => 11,  67 => 10,  63 => 8,  60 => 7,  57 => 6,  54 => 5,  52 => 4,  49 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/layout.html.twig", "F:\\xampp\\htdocs\\nt3acc\\env-production\\nt3-portal-base\\portal\\src\\views\\layout.html.twig");
    }
}
