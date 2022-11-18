<?php

/* nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig */
class __TwigTemplate_4f6cc6a920549bd64852f584509e71ec2297c3dbbddbc10cc1b4ae4ab2eec514 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 3
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/browse/layout.html.twig", "nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig", 3);
        $this->blocks = array(
            'pPageScripts' => array($this, 'block_pPageScripts'),
            'bBrowseMainContent' => array($this, 'block_bBrowseMainContent'),
            'pPageLiveScripts' => array($this, 'block_pPageLiveScripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/bricks/browse/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 14
    public function block_pPageScripts($context, array $blocks = array())
    {
        // line 15
        echo "\t";
        $this->displayParentBlock("pPageScripts", $context, $blocks);
        echo "
\t<script type=\"text/javascript\" src=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute(($context["app"] ?? null), "combodo.portal.base.absolute_url", array(), "array"), "html", null, true);
        echo "lib/jquery-treelistfilter/js/TreeListFilter.js\"></script>
";
    }

    // line 19
    public function block_bBrowseMainContent($context, array $blocks = array())
    {
        // line 20
        echo "\t<div class=\"row\" id=\"brick_content_toolbar\">
\t\t<div class=\"col-xs-4 col-sm-2 col-lg-1\">
\t\t\t<div class=\"btn-group btn-group-justified btn-group-sm\">
\t\t\t\t<a href=\"#\" class=\"btn btn-default\" id=\"btn-collapse-all\" title=\"";
        // line 23
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:Browse:Tree:CollapseAll")), "html", null, true);
        echo "\">-</a>
\t\t\t\t<a href=\"#\" class=\"btn btn-default\" id=\"btn-expand-all\" title=\"";
        // line 24
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:Browse:Tree:ExpandAll")), "html", null, true);
        echo "\">+</a>
\t\t\t</div>
\t\t</div>
\t\t<div class=\"col-xs-8 col-sm-10 col-lg-11 text-right\">
\t\t\t<label>";
        // line 28
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Search")), "html", null, true);
        echo "<input type=\"search\" class=\"form-control input-sm\" id=\"brick_search_field\" placeholder=\"\" aria-controls=\"brick_main_table\" value=\"";
        echo twig_escape_filter($this->env, ($context["sSearchValue"] ?? null), "html", null, true);
        echo "\"></label>
\t\t</div>
\t</div>
\t<ul class=\"list-group tree\" id=\"brick_content_tree\" data-level-id=\"L\">
\t</ul>

\t<div id=\"brick_content_empty\" class=\"text-center\">
\t\t";
        // line 35
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:Browse:Filter:NoData")), "html", null, true);
        echo "
\t</div>
\t<div id=\"brick_tree_overlay\">
\t\t<div class=\"overlay_content\">
            ";
        // line 39
        $this->loadTemplate("nt3-portal-base/portal/src/views/helpers/loader.html.twig", "nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig", 39)->display($context);
        // line 40
        echo "\t\t</div>
\t</div>
";
    }

    // line 44
    public function block_pPageLiveScripts($context, array $blocks = array())
    {
        // line 45
        echo "\t";
        $this->displayParentBlock("pPageLiveScripts", $context, $blocks);
        echo "
\t
\t<script type=\"text/javascript\">
\t\tvar sNodeCollapsedClass = 'glyphicon-menu-right';
\t\tvar sNodeExpandedClass = 'glyphicon-menu-down';
\t\tvar sNodeLoadingClass = 'glyphicon-refresh keep-spinning';
\t\tvar iSearchDelay = 500
\t\tvar sBrowseMode = '";
        // line 52
        echo twig_escape_filter($this->env, ($context["sBrowseMode"] ?? null), "html", null, true);
        echo "';
\t\tvar oLevelsProperties = ";
        // line 53
        echo ($context["aLevelsProperties"] ?? null);
        echo ";
\t\tvar oRawDatas = ";
        // line 54
        echo ($context["aItems"] ?? null);
        echo ";
\t\tvar bIsFullyLoaded = ('";
        // line 55
        echo twig_escape_filter($this->env, ($context["sDataLoading"] ?? null), "html", null, true);
        echo "' === '";
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"), "html", null, true);
        echo "') ? true : false;
\t\t\t
\t\t// Collapses / Expands all the tree nodes
\t\tvar collapseAll = function()
\t\t{
\t\t\t\$('#brick_content_tree .tree').toggle(false);
\t\t\t\$('#brick_content_tree .tree-toggle .glyphicon').removeClass(sNodeExpandedClass+' '+sNodeLoadingClass).addClass(sNodeCollapsedClass);
\t\t};
\t\tvar expandAll = function()
\t\t{
\t\t\t\$('#brick_content_tree .tree').toggle(true);
\t\t\t\$('#brick_content_tree .tree-toggle .glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeLoadingClass).addClass(sNodeExpandedClass);
\t\t};
\t\t// Show a loader over the tree
\t\tvar showTreeLoader = function()
\t\t{
\t\t\t\$(\"#brick_content_tree\").hide();
\t\t\t\$('#brick_tree_overlay').show();
\t\t};
\t\t// Hide the loader over the tree
\t\tvar hideTreeLoader = function()
\t\t{
\t\t\t\$('#brick_tree_overlay').hide();
\t\t\t\$(\"#brick_content_tree\").show();
\t\t}
\t\t// Registers the toggle listeners on the tree nodes. Used after every AJAX calls.
\t\tvar registerToggleListeners = function()
\t\t{
\t\t\t\$('#brick_content_tree .tree-toggle').off('click').on('click', function (oEvent) {
\t\t\t\toEvent.preventDefault();
\t\t\t\t
\t\t\t\t// Forcing subitems to expand after a filter, so we can browse subitems of a filtered item. Else is the regular toggle
\t\t\t\tif(\$(this).parent().children('ul.tree:visible').length > 0 && \$(this).parent().children('ul.tree:visible').children('li:visible').length === 0)
\t\t\t\t{
\t\t\t\t\t\$(this).parent().children('ul.tree').children('li').toggle(true);
\t\t\t\t}else{
\t\t\t\t\t\$(this).parent().children('ul.tree').toggle(200);
\t\t\t\t}
\t\t\t\t
\t\t\t\t// Toggling glyphicon class
\t\t\t\tif(\$(this).find('.glyphicon').hasClass(sNodeCollapsedClass))
\t\t\t\t{
\t\t\t\t\t\$(this).find('.glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeLoadingClass).addClass(sNodeExpandedClass);
\t\t\t\t}
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\t\$(this).find('.glyphicon').removeClass(sNodeExpandedClass+' '+sNodeLoadingClass).addClass(sNodeCollapsedClass);
\t\t\t\t}
\t\t\t\t
\t\t\t\t// Check if the node has no children, if so we try to load them through AJAX (Only for the current item)
\t\t\t\tif(\$(this).parent().children('ul.tree').children('li').length === 0)
\t\t\t\t{
\t\t\t\t\t\$(this).find('.glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeExpandedClass).addClass(sNodeLoadingClass);
\t\t\t\t\tloadChildNodes(\$(this).attr('data-level-alias'), \$(this).attr('data-item-id'));
\t\t\t\t}
\t\t\t});

            // Allows link in item's description to work. Otherwise, the predentDefault of the item takes over.
            \$('#brick_content_tree .list-group-item-description a').off('click').on('click', function(oEvent){
                oEvent.stopPropagation();
            });
\t\t};
\t\t// Registers the filter listeners on the tree.
\t\tvar registerFilterListeners = function()
\t\t{
\t\t\t\$('#brick_search_field').treeListFilter('#brick_content_tree', iSearchDelay, filterResultsHandler);
\t\t};
\t\tvar filterResultsHandler = function()
\t\t{
\t\t\t// If results shows intermediate levels without any leaves under, we show all its children.
\t\t\t\$('#brick_content_tree .list-group-item:visible').each(function(iIndex, oElement){
\t\t\t\tif(\$(oElement).find('.list-group.tree:visible').length === 0)
\t\t\t\t{
\t\t\t\t\t\$(oElement).find('.list-group.tree:not(:visible)').show();
\t\t\t\t\t\$(oElement).find('.list-group.tree:not(:visible) .list-group-item').show();
\t\t\t\t}
\t\t\t\telse if(\$(oElement).find('.list-group.tree:visible .list-group-item:visible').length === 0)
\t\t\t\t{
\t\t\t\t\t\$(oElement).find('.list-group.tree:visible .list-group-item:not(:visible)').show();
\t\t\t\t}
\t\t\t});
\t\t\t
\t\t\t// Show / hide empty data message
\t\t\tif(bIsFullyLoaded)
\t\t\t{
\t\t\t\thideTreeLoader();
\t\t\t\t
\t\t\t\tif(\$('#brick_content_tree > .list-group-item:visible').length > 0)
\t\t\t\t{
\t\t\t\t\t\$('#brick_content_empty').hide();
\t\t\t\t}
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\t\$('#brick_content_empty').show();
\t\t\t\t}
\t\t\t}
\t\t\t
\t\t\texpandAll();
\t\t};
\t\t// Load current node childnodes throught AJAX
\t\tvar loadChildNodes = function(sLevelAlias, sNodeId)
\t\t{
\t\t\tvar sUrl = '";
        // line 157
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_browse_brick_mode_tree", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sBrowseMode" => ($context["sBrowseMode"] ?? null), "sLevelAlias" => "-sLevelAlias-", "sNodeId" => "-sNodeId-")), "method");
        echo "';
\t\t\tsUrl = sUrl.replace(/-sLevelAlias-/, sLevelAlias).replace(/-sNodeId-/, sNodeId);
\t\t\t
\t\t\t\$.ajax(sUrl)
\t\t\t.done(function(data) {
\t\t\t\t\$('#brick_content_tree .tree-toggle[data-level-alias=\"'+sLevelAlias+'\"][data-item-id=\"'+sNodeId+'\"] .glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeLoadingClass).addClass(sNodeExpandedClass);
\t\t\t\tfor(index in data.data)
\t\t\t\t{
\t\t\t\t\tvar sublevel = data.data[index];
\t\t\t\t\tvar sublevelData = {};
\t\t\t\t\tsublevelData[sublevel.level_alias+\"::\"+sublevel.id] = sublevel;
\t\t\t\t\tbuildTree(sublevelData, sLevelAlias+\"::\"+sNodeId, false);
\t\t\t\t}
\t\t\t\tregisterToggleListeners();
\t\t\t})
\t\t\t.fail(function() {
\t\t\t\talert('";
        // line 173
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_format')->getCallable(), array("Error:XHR:Fail", twig_constant("nt3_APPLICATION_SHORT"))), "html", null, true);
        echo "');
\t\t\t});
\t\t};
\t\t// Build tree nodes from data under the nodeId
\t\tvar buildTree = function(data, nodeId, isRootLevel)
\t\t{
\t\t\tif(nodeId === undefined)
\t\t\t{
\t\t\t\t// We are on the root node
\t\t\t\tnodeId = 'L';
\t\t\t\t\$('ul[data-level-id=\"'+nodeId+'\"]').html('');
\t\t\t}
\t\t\tif(isRootLevel === undefined)
\t\t\t{
\t\t\t\tisRootLevel = true;
\t\t\t}
\t\t\t
\t\t\t\$.each(data, function(i, item){
\t\t\t\tvar levelId = item.level_alias+'::'+item.id;
\t\t\t\tvar levelAltId = item.level_alias+'_'+item.id;
\t\t\t\tvar levelActions = oLevelsProperties[item.level_alias].actions;
\t\t\t\tvar levelActionsKeys = Object.keys(levelActions);
\t\t\t\tvar levelPrimaryAction = levelActions[levelActionsKeys[0]];
\t\t\t\tvar url = '';

\t\t\t\tvar liElem  = \$('<li></li>').addClass('list-group-item');
\t\t\t\tvar aElem = \$('<span></span>').addClass('tree-item-wrapper').attr('data-item-id', item.id).attr('data-level-alias', item.level_alias);
\t\t\t\tvar nameElem   = \$('<a></a>').addClass('tree-item').text(item.name);
\t\t\t\t// Building node
\t\t\t\t\$('ul[data-level-id=\"'+nodeId+'\"]').append(liElem);
\t\t\t\taElem.append(nameElem);
\t\t\t\tliElem.append(aElem);
\t\t\t\t
\t\t\t\t// Building tooltip for the node
\t\t\t\tif( (item.tooltip !== undefined) && (item.tooltip !== '') )
\t\t\t\t{
\t\t\t\t\tnameElem.attr('title', item.tooltip).attr('data-toggle', 'tooltip').tooltip({html: true, trigger: 'hover', placement: 'right'});
\t\t\t\t}
\t\t\t\t// Building description for the node
\t\t\t\tif( (item.description !== undefined) && (item.description !== '') )
\t\t\t\t{
\t\t\t\t\taElem.append( \$('<span class=\"list-group-item-description\">'+item.description+'</span>') );
\t\t\t\t}
\t\t\t\t
\t\t\t\t// Building actions for that node
\t\t\t\tswitch(levelPrimaryAction.type)
\t\t\t\t{
\t\t\t\t\tcase '";
        // line 220
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_DRILLDOWN"), "html", null, true);
        echo "':
                        aElem.addClass('tree-toggle');
                        nameElem.html('<span class=\"glyphicon '+sNodeCollapsedClass+'\" aria-hidden=\"true\"></span><span class=\"list-group-item-text\">'+nameElem.text()+'</span>');
                        break;
\t\t\t\t\tcase '";
        // line 224
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_VIEW"), "html", null, true);
        echo "':
\t\t\t\t\t\turl = '";
        // line 225
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_view", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\tSetActionUrl(aElem, url);
                        SetActionOpeningTarget(aElem, levelPrimaryAction.opening_target);
                        break;
\t\t\t\t\tcase '";
        // line 229
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_EDIT"), "html", null, true);
        echo "':
\t\t\t\t\t\turl = '";
        // line 230
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_edit", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\tSetActionUrl(aElem, url);
                        SetActionOpeningTarget(aElem, levelPrimaryAction.opening_target);
                        break;
\t\t\t\t\tcase '";
        // line 234
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_CREATE_FROM_THIS"), "html", null, true);
        echo "':
\t\t\t\t\t\turl = levelPrimaryAction.url.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\turl = AddParameterToUrl(url, 'ar_token', item.action_rules_token[levelPrimaryAction.type]);
\t\t\t\t\t\tSetActionUrl(aElem, url);
                        SetActionOpeningTarget(aElem, levelPrimaryAction.opening_target);
\t\t\t\t\t\tbreak;
\t\t\t\t\tdefault:
\t\t\t\t\t\t//console.log('Action \"'+levelPrimaryAction.type+'\" not implemented for primary action');
\t\t\t\t\t\tbreak;
\t\t\t\t}
\t\t\t\t
\t\t\t\tif(levelActionsKeys.length > 1)
\t\t\t\t{
\t\t\t\t\t// Retrieving secondary action (Now we also display primary action)
\t\t\t\t\tvar actionsButtons = {};
\t\t\t\t\tfor(j = 0; j < levelActionsKeys.length; j++)
\t\t\t\t\t{
\t\t\t\t\t\tactionsButtons[levelActionsKeys[j]] = levelActions[levelActionsKeys[j]];
\t\t\t\t\t}
\t\t\t\t\t
\t\t\t\t\t// Preparing secondary actions container
\t\t\t\t\tvar actionsElem = \$('<div></div>').addClass('list-group-item-actions');
\t\t\t\t\tliElem.append(actionsElem);
\t\t\t\t\t// Checking if a menu is necessary
\t\t\t\t\tvar actionsSSTogglerElem = \$('<a class=\"glyphicon glyphicon-menu-hamburger\" data-toggle=\"collapse\" data-target=\"#item-actions-menu-'+levelAltId+'\"></a>');
\t\t\t\t\tvar actionsSSMenuElem = \$('<div id=\"item-actions-menu-'+levelAltId+'\" class=\"item-action-wrapper panel panel-default\"></div>');
\t\t\t\t\tvar actionsSSMenuContainerElem = \$('<div class=\"panel-body\"></div>');
\t\t\t\t\tactionsSSMenuElem.append(actionsSSMenuContainerElem);
\t\t\t\t\tactionsElem.append(actionsSSTogglerElem);
\t\t\t\t\tactionsElem.append(actionsSSMenuElem);

\t\t\t\t\t// Adding secondary actions
\t\t\t\t\tfor(j in actionsButtons)
\t\t\t\t\t{
\t\t\t\t\t\tvar action = actionsButtons[j];
\t\t\t\t\t\tvar actionElem = \$('<a></a>');
\t\t\t\t\t\tvar actionIconElem = \$('<span></span>').appendTo(actionElem);
\t\t\t\t\t\t
\t\t\t\t\t\tswitch(action.type)
\t\t\t\t\t\t{
\t\t\t\t\t\t\tcase '";
        // line 274
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_VIEW"), "html", null, true);
        echo "':
\t\t\t\t\t\t\t\turl = '";
        // line 275
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_view", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\t\t\tbreak;
\t\t\t\t\t\t\tcase '";
        // line 277
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_EDIT"), "html", null, true);
        echo "':
\t\t\t\t\t\t\t\turl = '";
        // line 278
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_edit", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\t\t\tbreak;
\t\t\t\t\t\t\tcase '";
        // line 280
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\BrowseBrick::ENUM_ACTION_CREATE_FROM_THIS"), "html", null, true);
        echo "':
\t\t\t\t\t\t\t\turl = action.url.replace(/-objectClass-/, item.class).replace(/-objectId-/, item.id);
\t\t\t\t\t\t\t\turl = AddParameterToUrl(url, 'ar_token', item.action_rules_token[action.type]);
\t\t\t\t\t\t\t\tbreak;
\t\t\t\t\t\t\tdefault:
\t\t\t\t\t\t\t    url = '#';
\t\t\t\t\t\t\t\t//console.log('Action \"'+action.type+'\" not implemented for secondary action');
\t\t\t\t\t\t\t\tbreak;
\t\t\t\t\t\t}
                        SetActionUrl(actionElem, url);
                        SetActionOpeningTarget(actionElem, action.opening_target);

                        // Adding title if present
\t\t\t\t\t\tif(action.title !== undefined)
\t\t\t\t\t\t{
\t\t\t\t\t\t\tactionElem.attr('title', action.title);
\t\t\t\t\t\t}
\t\t\t\t\t\t// Adding icon class if present
\t\t\t\t\t\tif(action.icon_class !== undefined)
\t\t\t\t\t\t{
\t\t\t\t\t\t\tactionIconElem.addClass(action.icon_class);
\t\t\t\t\t\t}
\t\t\t\t\t\t
\t\t\t\t\t\tactionElem.append(action.title);
\t\t\t\t\t\tactionsSSMenuContainerElem.append( \$('<p></p>').append(actionElem) );
\t\t\t\t\t}
\t\t\t\t}
\t\t\t\t
\t\t\t\t// Building subnodes if necessary
\t\t\t\tvar ulElem  = \$('<ul></ul>').addClass('list-group').addClass('tree').attr('data-level-id', levelId);
\t\t\t\tliElem.append(ulElem);
\t\t\t\tif(item.subitems.length !== 0)
\t\t\t\t{
\t\t\t\t\tbuildTree(item.subitems, levelId, false);
\t\t\t\t}\t\t\t\t
\t\t\t});
\t\t\t
\t\t\t// Update listeners
\t\t\tif(isRootLevel)
\t\t\t{
\t\t\t\tregisterToggleListeners();
\t\t\t}
\t\t};

\t\t\$(document).ready(function(){
\t\t\t// Init expand/collapse all buttons
\t\t\t\$('#btn-collapse-all').on('click', function (oEvent) {
\t\t\t    oEvent.preventDefault();
\t\t\t\tcollapseAll();
\t\t\t});
\t\t\t\$('#btn-expand-all').on('click', function (oEvent) {
                oEvent.preventDefault();
                if(!bIsFullyLoaded)
\t\t\t\t{
\t\t\t\t\t// Show a loader while fetching results
\t\t\t\t\tshowTreeLoader();
\t\t\t\t\t
\t\t\t\t\t// If we don't do that now, we have have several calls
\t\t\t\t\tbIsFullyLoaded = true;
\t\t\t\t\t
\t\t\t\t\t// Display loader by toggling glyphicon class
\t\t\t\t\t\$('#brick_content_tree .tree-toggle .glyphicon.'+sNodeCollapsedClass).removeClass(sNodeCollapsedClass).addClass(sNodeLoadingClass);
\t\t\t\t\t
\t\t\t\t\t// Load the whole tree
\t\t\t\t\t\$.ajax('";
        // line 344
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_browse_brick_mode", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sBrowseMode" => ($context["sBrowseMode"] ?? null), "sDataLoading" => twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"))), "method");
        echo "')
\t\t\t\t\t.done(function(data)
\t\t\t\t\t{
\t\t\t\t\t\tbuildTree(data.data);
\t\t\t\t\t})
\t\t\t\t\t.fail(function(){
\t\t\t\t\t\tbIsFullyLoaded = false;
\t\t\t\t\t})
\t\t\t\t\t.always(function(){
\t\t\t\t\t\t\$('#brick_content_tree .tree-toggle .glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeLoadingClass).addClass(sNodeExpandedClass);
\t\t\t\t\t\t// Hide loader no matter what
\t\t\t\t\t\thideTreeLoader();
\t\t\t\t\t});
\t\t\t\t}
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\t\$('#brick_content_tree .tree-toggle .glyphicon').removeClass(sNodeCollapsedClass+' '+sNodeLoadingClass).addClass(sNodeExpandedClass);
\t\t\t\t}
\t\t\t\texpandAll();
\t\t\t});
\t\t\t
\t\t\t// Init filter field
\t\t\t// Note : If placed in the registerFilterListeners function, must be before the .treeListFilter as the off('change') will remove .treeListFilter
\t\t\t\$('#brick_search_field').on('change', function(oEvent){
\t\t\t\t// Show a loader while fetching/filtering results
\t\t\t\tshowTreeLoader();
\t\t\t\t
\t\t\t\tif(!bIsFullyLoaded)
\t\t\t\t{
\t\t\t\t\t
\t\t\t\t\t// We don't want to trigger TreeListFilter yet
\t\t\t\t\toEvent.stopPropagation();
\t\t\t\t\t
\t\t\t\t\t// Load the whole tree
\t\t\t\t\t\$.ajax('";
        // line 378
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_browse_brick_mode", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sBrowseMode" => ($context["sBrowseMode"] ?? null), "sDataLoading" => twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"))), "method");
        echo "')
\t\t\t\t\t.done(function(data)
\t\t\t\t\t{
\t\t\t\t\t\tbIsFullyLoaded = true;
\t\t\t\t\t\t// Updating tree
\t\t\t\t\t\tbuildTree(data.data);
\t\t\t\t\t\t// Trigerring filter
\t\t\t\t\t\t\$('#brick_search_field').trigger('change');
\t\t\t\t\t})
\t\t\t\t\t.fail(function(){
\t\t\t\t\t\tbIsFullyLoaded = false;
\t\t\t\t\t})
\t\t\t\t\t.always(function(){
\t\t\t\t\t\t// We don't need to call this because it will be called as a callback when \"change\" event is triggered on treeListFilter
\t\t\t\t\t\t//filterResultsHandler();
\t\t\t\t\t});
\t\t\t\t}\t\t\t\t
\t\t\t\telse
\t\t\t\t{
\t\t\t\t\t// // We don't need to call this because it will be called as a callback when \"change\" event is triggered on treeListFilter
\t\t\t\t\tfilterResultsHandler();
\t\t\t\t}\t\t\t\t
\t\t\t});
\t\t\t
\t\t\t// Auto collapse item actions popup
\t\t\t\$('body').click(function(){
\t\t\t\t\$('#brick_content_tree .item-action-wrapper.collapse.in').collapse('hide');
\t\t\t});
\t\t\t
\t\t\t// Build the tree (collapsed)
\t\t\tshowTreeLoader();
\t\t\tbuildTree(oRawDatas);
\t\t\thideTreeLoader();
\t\t\tregisterFilterListeners();
\t\t\tcollapseAll();

            // Open first level if only one item and not pre-filtering
            if( (\$('#brick_content_tree > .list-group-item').length == 1) && (\$('#brick_search_field').val() === '') )
            {
                setTimeout(function(){
                    \$('#brick_content_tree > .list-group-item > .tree-toggle').trigger('click');
                }, 300);
            }
\t\t\t
\t\t\t";
        // line 422
        if ( !(null === ($context["sSearchValue"] ?? null))) {
            // line 423
            echo "\t\t\t\t// Filters from default value
\t\t\t\t\$('#brick_search_field').trigger('change');
\t\t\t";
        }
        // line 426
        echo "\t\t});
\t</script>
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  540 => 426,  535 => 423,  533 => 422,  486 => 378,  449 => 344,  382 => 280,  377 => 278,  373 => 277,  368 => 275,  364 => 274,  321 => 234,  314 => 230,  310 => 229,  303 => 225,  299 => 224,  292 => 220,  242 => 173,  223 => 157,  116 => 55,  112 => 54,  108 => 53,  104 => 52,  93 => 45,  90 => 44,  84 => 40,  82 => 39,  75 => 35,  63 => 28,  56 => 24,  52 => 23,  47 => 20,  44 => 19,  38 => 16,  33 => 15,  30 => 14,  11 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig", "/var/www/html/nt3test/env-production/nt3-portal-base/portal/src/views/bricks/browse/mode_tree.html.twig");
    }
}
