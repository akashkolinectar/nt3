<?php

/* nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig */
class __TwigTemplate_309c84d53a2e5e13e3ada06780f05c9f8e8ca19522c6aeee683e677fd1132364 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 3
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/layout.html.twig", "nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig", 3);
        $this->blocks = array(
            'pPageBodyClass' => array($this, 'block_pPageBodyClass'),
            'pMainHeaderTitle' => array($this, 'block_pMainHeaderTitle'),
            'pMainContentHolder' => array($this, 'block_pMainContentHolder'),
            'pUserProfileWrapper' => array($this, 'block_pUserProfileWrapper'),
            'pUserProfilePictureFormContainer' => array($this, 'block_pUserProfilePictureFormContainer'),
            'pUserProfilePreferencesFormContainer' => array($this, 'block_pUserProfilePreferencesFormContainer'),
            'pUserProfilePasswordFormContainer' => array($this, 'block_pUserProfilePasswordFormContainer'),
            'pUserProfileFormButtons' => array($this, 'block_pUserProfileFormButtons'),
            'pPageReadyScripts' => array($this, 'block_pPageReadyScripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/bricks/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["oContactForm"] = $this->getAttribute(($context["forms"] ?? null), "contact", array());
        // line 6
        $context["oPreferencesForm"] = $this->getAttribute(($context["forms"] ?? null), "preferences", array());
        // line 7
        $context["oPasswordForm"] = $this->getAttribute(($context["forms"] ?? null), "password", array());
        // line 3
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 9
    public function block_pPageBodyClass($context, array $blocks = array())
    {
        $this->displayParentBlock("pPageBodyClass", $context, $blocks);
        echo " page_user-profile_brick";
    }

    // line 11
    public function block_pMainHeaderTitle($context, array $blocks = array())
    {
        // line 12
        echo "\t";
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array($this->getAttribute(($context["oBrick"] ?? null), "GetTitle", array(), "method"))), "html", null, true);
        echo "
";
    }

    // line 15
    public function block_pMainContentHolder($context, array $blocks = array())
    {
        // line 16
        echo "\t";
        if (($context["bDemoMode"] ?? null)) {
            // line 17
            echo "\t\t<div class=\"alert alert-warning\">
\t\t\t<span class=\"fa fa-info fa-2x\" style=\"margin-right: 10px; vertical-align: sub;\"></span>
\t\t\tUser profile edition is not available in demo mode.
\t\t</div>
\t";
        }
        // line 22
        echo "
\t<div id=\"user-profile-wrapper\">
\t\t";
        // line 24
        $this->displayBlock('pUserProfileWrapper', $context, $blocks);
        // line 144
        echo "\t</div>
";
    }

    // line 24
    public function block_pUserProfileWrapper($context, array $blocks = array())
    {
        // line 25
        echo "\t\t\t<div class=\"row\">
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t<div class=\"panel panel-default\">
\t\t\t\t\t\t<div class=\"panel-heading\">
\t\t\t\t\t\t\t<h3 class=\"panel-title\">";
        // line 29
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:PersonalInformations:Title")), "html", null, true);
        echo "</h3>
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"panel-body\">
\t\t\t\t\t\t\t<form id=\"";
        // line 32
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "id", array()), "html", null, true);
        echo "\" class=\"\" method=\"POST\" action=\"";
        echo $this->getAttribute($this->getAttribute(($context["oContactForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\">
\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"transaction_id\" value=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "transaction_id", array()), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t\t<div class=\"form_alerts\">
\t\t\t\t\t\t\t\t\t<div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t<div class=\"alert alert-warning\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t<div class=\"alert alert-error alert-danger\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"form_fields\">
\t\t\t\t\t\t\t\t\t";
        // line 40
        echo $this->getAttribute($this->getAttribute(($context["oContactForm"] ?? null), "renderer", array()), "GetBaseLayout", array(), "method");
        echo "
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"col-sm-6\">
\t\t\t\t\t";
        // line 47
        if ($this->getAttribute(($context["oBrick"] ?? null), "GetShowPictureForm", array(), "method")) {
            // line 48
            echo "\t\t\t\t\t\t";
            $this->displayBlock('pUserProfilePictureFormContainer', $context, $blocks);
            // line 83
            echo "\t\t\t\t\t";
        }
        // line 84
        echo "
            \t\t";
        // line 85
        if ($this->getAttribute(($context["oBrick"] ?? null), "GetShowPreferencesForm", array(), "method")) {
            // line 86
            echo "\t\t\t\t\t\t";
            $this->displayBlock('pUserProfilePreferencesFormContainer', $context, $blocks);
            // line 105
            echo "\t\t\t\t\t";
        }
        // line 106
        echo "
\t\t\t\t\t";
        // line 107
        if ($this->getAttribute(($context["oBrick"] ?? null), "GetShowPasswordForm", array(), "method")) {
            // line 108
            echo "\t\t\t\t\t\t";
            $this->displayBlock('pUserProfilePasswordFormContainer', $context, $blocks);
            // line 131
            echo "\t\t\t\t\t";
        }
        // line 132
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t<div class=\"form_buttons\">
\t\t\t\t";
        // line 135
        $this->displayBlock('pUserProfileFormButtons', $context, $blocks);
        // line 142
        echo "\t\t\t</div>
\t\t";
    }

    // line 48
    public function block_pUserProfilePictureFormContainer($context, array $blocks = array())
    {
        // line 49
        echo "\t\t\t\t\t\t\t<div class=\"panel panel-default user_profile_picture\">
\t\t\t\t\t\t\t\t<div class=\"panel-heading\">
\t\t\t\t\t\t\t\t\t<h3 class=\"panel-title\">";
        // line 51
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Photo:Title")), "html", null, true);
        echo "</h3>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"panel-body\" style=\"position: relative;\">
\t\t\t\t\t\t\t\t\t<div class=\"form_alerts\">
\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-warning\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-error alert-danger\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t<form id=\"picture-form\" method=\"POST\" action=\"";
        // line 59
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array(), "array"), "generate", array(0 => "p_user_profile_brick"), "method"), "html", null, true);
        echo "\">
\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"current_values[form_type]\" value=\"";
        // line 60
        echo twig_escape_filter($this->env, twig_constant("\\Combodo\\nt3\\Portal\\Controller\\UserProfileBrickController::ENUM_FORM_TYPE_PICTURE"), "html", null, true);
        echo "\" />
\t\t\t\t\t\t\t\t\t\t<input type=\"hidden\" name=\"operation\" value=\"submit\" />
\t\t\t\t\t\t\t\t\t\t<div class=\"text-center\">
\t\t\t\t\t\t\t\t\t\t\t<span class=\"preview\">
\t\t\t\t\t\t\t\t\t\t\t\t<img src=\"";
        // line 64
        echo twig_escape_filter($this->env, ($context["sUserPhotoUrl"] ?? null), "html", null, true);
        echo "\"/>
\t\t\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t\t\t\t<span class=\"actions\">
\t\t\t\t\t\t\t\t\t\t\t\t<span type=\"button\" class=\"btn btn-default btn_edit\">
\t\t\t\t\t\t\t\t\t\t\t\t\t<span class=\"fa fa-pencil fa-fw\"></span>
\t\t\t\t\t\t\t\t\t\t\t\t\t<input id=\"picture\" type=\"file\" name=\"picture\" />
\t\t\t\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t\t\t\t\t";
        // line 77
        echo "\t\t\t\t\t\t\t\t\t\t\t</span>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
    }

    // line 86
    public function block_pUserProfilePreferencesFormContainer($context, array $blocks = array())
    {
        // line 87
        echo "\t\t\t\t\t\t\t<div class=\"panel panel-default\">
\t\t\t\t\t\t\t\t<div class=\"panel-heading\">
\t\t\t\t\t\t\t\t\t<h3 class=\"panel-title\">";
        // line 89
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Class:appUserPreferences/Attribute:preferences")), "html", null, true);
        echo "</h3>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"panel-body\">
\t\t\t\t\t\t\t\t\t<form id=\"";
        // line 92
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "id", array()), "html", null, true);
        echo "\" class=\"\" method=\"POST\" action=\"";
        echo $this->getAttribute($this->getAttribute(($context["oPreferencesForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\">
\t\t\t\t\t\t\t\t\t\t<div class=\"form_alerts\">
\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-warning\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-error alert-danger\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t<div class=\"form_fields\">
\t\t\t\t\t\t\t\t\t\t\t";
        // line 99
        echo $this->getAttribute($this->getAttribute(($context["oPreferencesForm"] ?? null), "renderer", array()), "GetBaseLayout", array(), "method");
        echo "
\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
    }

    // line 108
    public function block_pUserProfilePasswordFormContainer($context, array $blocks = array())
    {
        // line 109
        echo "\t\t\t\t\t\t\t<div class=\"panel panel-default\">
\t\t\t\t\t\t\t\t<div class=\"panel-heading\">
\t\t\t\t\t\t\t\t\t<h3 class=\"panel-title\">";
        // line 111
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Password:Title")), "html", null, true);
        echo "</h3>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t<div class=\"panel-body\">
\t\t\t\t\t\t\t\t\t";
        // line 114
        if ( !(null === ($context["oPasswordForm"] ?? null))) {
            // line 115
            echo "\t\t\t\t\t\t\t\t\t\t<form id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "id", array()), "html", null, true);
            echo "\" class=\"\" method=\"POST\" action=\"";
            echo $this->getAttribute($this->getAttribute(($context["oPasswordForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
            echo "\" autocomplete=\"off\">
\t\t\t\t\t\t\t\t\t\t\t<div class=\"form_alerts\">
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-success\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-warning\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t\t\t<div class=\"alert alert-error alert-danger\" role=\"alert\" style=\"display: none;\"></div>
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t\t<div class=\"form_fields\">
\t\t\t\t\t\t\t\t\t\t\t\t";
            // line 122
            echo $this->getAttribute($this->getAttribute(($context["oPasswordForm"] ?? null), "renderer", array()), "GetBaseLayout", array(), "method");
            echo "
\t\t\t\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t\t\t\t";
        } else {
            // line 126
            echo "\t\t\t\t\t\t\t\t\t\t";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:UserProfile:Password:CantChangeContactAdministrator")), "html", null, true);
            echo "
\t\t\t\t\t\t\t\t\t";
        }
        // line 128
        echo "\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t";
    }

    // line 135
    public function block_pUserProfileFormButtons($context, array $blocks = array())
    {
        // line 136
        echo "\t\t\t\t\t<div class=\"form_btn_regular\">
\t\t\t\t\t\t";
        // line 137
        if ((($context["sFormMode"] ?? null) == twig_constant("\\Combodo\\nt3\\Portal\\Controller\\ObjectController::ENUM_MODE_EDIT"))) {
            // line 138
            echo "\t\t\t\t\t\t\t<input class=\"btn btn-primary form_btn_submit\" type=\"submit\" value=\"";
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Button:Submit")), "html", null, true);
            echo "\">
\t\t\t\t\t\t";
        }
        // line 140
        echo "\t\t\t\t\t</div>
\t\t\t\t";
    }

    // line 147
    public function block_pPageReadyScripts($context, array $blocks = array())
    {
        // line 148
        echo "\t";
        $this->displayParentBlock("pPageReadyScripts", $context, $blocks);
        echo "

\t// Personal informations form
\tvar oContactFormFieldSet = \$('#";
        // line 151
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "id", array()), "html", null, true);
        echo " > .form_fields').field_set(";
        echo twig_jsonencode_filter($this->getAttribute(($context["oContactForm"] ?? null), "fieldset", array()));
        echo ");
\t\$('#";
        // line 152
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "id", array()), "html", null, true);
        echo "').portal_form_handler({
\t\tformmanager_class: \"";
        // line 153
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "formmanager_class", array()), "js"), "html", null, true);
        echo "\",
\t\tformmanager_data: ";
        // line 154
        echo twig_jsonencode_filter($this->getAttribute(($context["oContactForm"] ?? null), "formmanager_data", array()));
        echo ",
\t\tfield_set: oContactFormFieldSet,
\t\tendpoint: \"";
        // line 156
        echo $this->getAttribute($this->getAttribute(($context["oContactForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\"
\t});

\t// Preferences form
\tvar oPreferencesFormFieldSet = \$('#";
        // line 160
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "id", array()), "html", null, true);
        echo " > .form_fields').field_set(";
        echo twig_jsonencode_filter($this->getAttribute(($context["oPreferencesForm"] ?? null), "fieldset", array()));
        echo ");
\t\$('#";
        // line 161
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "id", array()), "html", null, true);
        echo "').portal_form_handler({
\t\tformmanager_class: \"";
        // line 162
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "formmanager_class", array()), "js"), "html", null, true);
        echo "\",
\t\tformmanager_data: ";
        // line 163
        echo twig_jsonencode_filter($this->getAttribute(($context["oPreferencesForm"] ?? null), "formmanager_data", array()));
        echo ",
\t\tfield_set: oPreferencesFormFieldSet,
\t\tendpoint: \"";
        // line 165
        echo $this->getAttribute($this->getAttribute(($context["oPreferencesForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
        echo "\"
\t});

\t";
        // line 168
        if ( !(null === ($context["oPasswordForm"] ?? null))) {
            // line 169
            echo "\t\t// Password form
\t\tvar oPasswordFormFieldSet = \$('#";
            // line 170
            echo twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "id", array()), "html", null, true);
            echo " > .form_fields').field_set(";
            echo twig_jsonencode_filter($this->getAttribute(($context["oPasswordForm"] ?? null), "fieldset", array()));
            echo ");
\t\t\$('#";
            // line 171
            echo twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "id", array()), "html", null, true);
            echo "').portal_form_handler({
\t\t\tformmanager_class: \"";
            // line 172
            echo twig_escape_filter($this->env, twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "formmanager_class", array()), "js"), "html", null, true);
            echo "\",
\t\t\tformmanager_data: ";
            // line 173
            echo twig_jsonencode_filter($this->getAttribute(($context["oPasswordForm"] ?? null), "formmanager_data", array()));
            echo ",
\t\t\tfield_set: oPasswordFormFieldSet,
\t\t\tendpoint: \"";
            // line 175
            echo $this->getAttribute($this->getAttribute(($context["oPasswordForm"] ?? null), "renderer", array()), "GetEndpoint", array(), "method");
            echo "\"
\t\t});
\t";
        }
        // line 178
        echo "
\t// Picture form
\t// - JQuery upload widget
\t\$('#picture-form #picture').fileupload({
\t\tdataType: 'json',
\t\tacceptFileTypes: /(\\.|\\/)(gif|jpe?g|png)\$/i,
\t\tdisableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator.userAgent)
\t})
\t.on('fileuploadsend', function(oEvent, oData){
\t\t\$('.user_profile_picture .form_alerts .alert-error').hide()
\t\t\$('#page_overlay .overlay_content .content_loader').clone().prependTo('.user_profile_picture .panel-body');
\t})
\t.on('fileuploadalways', function(oEvent, oData){
\t\t\$('.user_profile_picture .content_loader').remove();
\t})
\t.on('fileuploaddone', function(oEvent, oData){
\t\tif( (oData._response.result.form !== undefined) && (oData._response.result.form.validation.valid === true) )
\t\t{
\t\t\t// Retrieving picture url
\t\t\tvar sPictureUrl = oData._response.result.form.picture_url;
\t\t\t// Replacing form preview image
\t\t\t\$('#picture-form .preview img').attr('src', sPictureUrl);
\t\t\t// Replacing menu image
\t\t\t\$('#topbar .user_photo, #sidebar .user_photo').css('background-image', 'url(\"' + sPictureUrl + '\")');
\t\t}
\t})
\t.on('fileuploadfail', function(oEvent, oData){
\t\tif( (oData._response.jqXHR.responseJSON !== undefined) && (oData._response.jqXHR.responseJSON.error_message !== undefined) )
\t\t{
\t\t\t\$('.user_profile_picture .form_alerts .alert-error').show().text(oData._response.jqXHR.responseJSON.error_message);
\t\t}
\t});
\t// - Undo button
\t/*\$('#user-profile-wrapper .actions .btn_undo').on('click', function(oEvent){
\t\t//console.log('Picture undo trigger');
\t});*/
\t// - Reset button
\t\$('#user-profile-wrapper .actions .btn_reset').on('click', function(oEvent){
\t\t//console.log('Picture reset trigger');
\t});

\t// Submit button
\t\$('#user-profile-wrapper .form_buttons .form_btn_submit').off('click').on('click', function(oEvent){
\t\toEvent.preventDefault();

\t\t// Resetting feedback
\t\t\$('#user-profile-wrapper .form_alerts .alert').hide();
\t\t\$('#user-profile-wrapper .form_alerts .alert > p').remove();
\t\t\$('#user-profile-wrapper .form_field').removeClass('has-error');
\t\t\$('#user-profile-wrapper .form_field .help-block > p').remove();

\t\t// Submiting contact form through AJAX
\t\t//if(\$('#";
        // line 230
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "id", array()), "html", null, true);
        echo " .field_set').field_set('hasTouchedFields'))
\t\t//{
\t\t\t\$('#";
        // line 232
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oContactForm"] ?? null), "id", array()), "html", null, true);
        echo "').portal_form_handler('submit', oEvent);
\t\t//}

\t\t// Submiting preferences form through AJAX
\t\t//if(\$('#";
        // line 236
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "id", array()), "html", null, true);
        echo " .field_set').field_set('hasTouchedFields'))
\t\t//{
\t\t\t\$('#";
        // line 238
        echo twig_escape_filter($this->env, $this->getAttribute(($context["oPreferencesForm"] ?? null), "id", array()), "html", null, true);
        echo "').portal_form_handler('submit', oEvent);
\t\t//}

\t\t";
        // line 241
        if ( !(null === ($context["oPasswordForm"] ?? null))) {
            // line 242
            echo "\t\t\t// Submiting password form through AJAX
\t\t\t// Only if fields are filled
\t\t\t\$('#";
            // line 244
            echo twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "id", array()), "html", null, true);
            echo " :password').each(function(iIndex, oElem){
\t\t\t\tif(\$(oElem).val() !== '')
\t\t\t\t{
\t\t\t\t\t\$('#";
            // line 247
            echo twig_escape_filter($this->env, $this->getAttribute(($context["oPasswordForm"] ?? null), "id", array()), "html", null, true);
            echo "').portal_form_handler('submit', oEvent);
\t\t\t\t\treturn false;
\t\t\t\t}
\t\t\t});
\t\t";
        }
        // line 252
        echo "\t});
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  504 => 252,  496 => 247,  490 => 244,  486 => 242,  484 => 241,  478 => 238,  473 => 236,  466 => 232,  461 => 230,  407 => 178,  401 => 175,  396 => 173,  392 => 172,  388 => 171,  382 => 170,  379 => 169,  377 => 168,  371 => 165,  366 => 163,  362 => 162,  358 => 161,  352 => 160,  345 => 156,  340 => 154,  336 => 153,  332 => 152,  326 => 151,  319 => 148,  316 => 147,  311 => 140,  305 => 138,  303 => 137,  300 => 136,  297 => 135,  291 => 128,  285 => 126,  278 => 122,  265 => 115,  263 => 114,  257 => 111,  253 => 109,  250 => 108,  240 => 99,  228 => 92,  222 => 89,  218 => 87,  215 => 86,  206 => 77,  196 => 64,  189 => 60,  185 => 59,  174 => 51,  170 => 49,  167 => 48,  162 => 142,  160 => 135,  155 => 132,  152 => 131,  149 => 108,  147 => 107,  144 => 106,  141 => 105,  138 => 86,  136 => 85,  133 => 84,  130 => 83,  127 => 48,  125 => 47,  115 => 40,  105 => 33,  99 => 32,  93 => 29,  87 => 25,  84 => 24,  79 => 144,  77 => 24,  73 => 22,  66 => 17,  63 => 16,  60 => 15,  53 => 12,  50 => 11,  43 => 9,  39 => 3,  37 => 7,  35 => 6,  33 => 5,  11 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/user-profile/layout.html.twig");
    }
}
