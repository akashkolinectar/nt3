<?php

/* nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig */
class __TwigTemplate_a1977d190c63422550c60dd98b8abf10fb63d5c1115bd3a56dbc274dcc956376 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 3
        $this->parent = $this->loadTemplate("nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig", "nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig", 3);
        $this->blocks = array(
            'pPageBodyClass' => array($this, 'block_pPageBodyClass'),
            'pMainContentHolder' => array($this, 'block_pMainContentHolder'),
            'pPageLiveScripts' => array($this, 'block_pPageLiveScripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "nt3-portal-base/portal/src/views/bricks/manage/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_pPageBodyClass($context, array $blocks = array())
    {
        $this->displayParentBlock("pPageBodyClass", $context, $blocks);
        echo " page_manage_brick";
    }

    // line 7
    public function block_pMainContentHolder($context, array $blocks = array())
    {
        // line 8
        echo "    ";
        if ((twig_length_filter($this->env, ($context["aGroupingTabsValues"] ?? null)) > 1)) {
            // line 9
            echo "        <ul class=\"nav nav-pills grouping_tabs\">
            ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["aGroupingTabsValues"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["aGroupingTab"]) {
                // line 11
                echo "                <li";
                if ((array_key_exists("sGroupingTab", $context) && (($context["sGroupingTab"] ?? null) == $this->getAttribute($context["aGroupingTab"], "value", array())))) {
                    echo " class=\"active\"";
                }
                echo ">
                    <a href=\"";
                // line 12
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_manage_brick", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sDisplayMode" => "default", "sGroupingTab" => $this->getAttribute($context["aGroupingTab"], "value", array()))), "method"), "html", null, true);
                echo "\"
                       id=\"btn_tab_for_";
                // line 13
                echo twig_escape_filter($this->env, $this->getAttribute($context["aGroupingTab"], "value", array()), "html", null, true);
                echo "\">
                        ";
                // line 14
                echo $this->getAttribute($context["aGroupingTab"], "label", array());
                echo "
                        ";
                // line 15
                if ($this->getAttribute(($context["oBrick"] ?? null), "GetShowTabCounts", array(), "method")) {
                    // line 16
                    echo "                            <span class=\"badge\">";
                    echo $this->getAttribute($context["aGroupingTab"], "count", array());
                    echo "</span>
                        ";
                }
                // line 18
                echo "                    </a>
                </li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aGroupingTab'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 21
            echo "        </ul>
    ";
        }
        // line 23
        echo "    ";
        $context["iTableCount"] = 0;
        // line 24
        echo "    ";
        if ((twig_length_filter($this->env, ($context["aGroupingAreasData"] ?? null)) > 0)) {
            // line 25
            echo "        ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["aGroupingAreasData"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["aAreaData"]) {
                // line 26
                echo "            ";
                if (($this->getAttribute($context["aAreaData"], "iItemsCount", array()) > 0)) {
                    // line 27
                    echo "                ";
                    $context["iTableCount"] = (($context["iTableCount"] ?? null) + 1);
                    // line 28
                    echo "                <div class=\"panel panel-default\">
                    <div class=\"panel-heading clearfix\">
                        <h3 class=\"panel-title\" style=\"float: left;\">";
                    // line 30
                    echo twig_escape_filter($this->env, $this->getAttribute($context["aAreaData"], "sTitle", array()), "html", null, true);
                    echo "</h3>
                        ";
                    // line 31
                    if (($context["bCanExport"] ?? null)) {
                        // line 32
                        echo "                            <a href=\"";
                        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_manage_brick_excel_export_start", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sGroupingTab" => ($context["sGroupingTab"] ?? null), "sGroupingArea" => $this->getAttribute($context["aAreaData"], "sId", array()))), "method");
                        echo "\"
                               id=\"btn_export_excel_for_";
                        // line 33
                        echo twig_escape_filter($this->env, $this->getAttribute($context["aAreaData"], "sId", array()), "html", null, true);
                        echo "\"
                               data-toggle=\"modal\" data-target=\"#modal-for-all\">
                                <span class=\"fa fa-download fa-lg\" style=\"float: right;\"
                                   data-toggle=\"tooltip\" data-delay=\"300\" data-placement=\"left\"
                                   title=\"";
                        // line 37
                        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("ExcelExporter:ExportMenu")), "html", null, true);
                        echo "\"></span>
                            </a>
                        ";
                    }
                    // line 40
                    echo "                    </div>
                    <div class=\"panel-body\">
                        <table id=\"table-";
                    // line 42
                    echo twig_escape_filter($this->env, $this->getAttribute($context["aAreaData"], "sId", array()), "html", null, true);
                    echo "\" class=\"table table-striped table-bordered responsive\"
                               width=\"100%\"></table>
                    </div>
                </div>
            ";
                }
                // line 47
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aAreaData'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 48
            echo "    ";
        }
        // line 49
        echo "
    ";
        // line 50
        if ((($context["iTableCount"] ?? null) == 0)) {
            // line 51
            echo "        <div class=\"panel panel-default\">
            <div class=\"panel-body\">
                <h3 class=\"text-center\">";
            // line 53
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Brick:Portal:Manage:Table:NoData")), "html", null, true);
            echo "</h3>
            </div>
        </div>
    ";
        }
    }

    // line 59
    public function block_pPageLiveScripts($context, array $blocks = array())
    {
        // line 60
        echo "    ";
        $this->displayParentBlock("pPageLiveScripts", $context, $blocks);
        echo "

    <script type=\"text/javascript\">
        var sDataLoading = '";
        // line 63
        echo twig_escape_filter($this->env, ($context["sDataLoading"] ?? null), "html", null, true);
        echo "';
        // Used for ajax throttling
        var iSearchThrottle = 300;
        var oKeyTimeout;
        var aKeyTimeoutFilteredKeys = [16, 17, 18, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40]; // Shift, Ctrl, Alt, Pause, Esc, Page Up/Down, Home, End, Left/Up/Right/Down arrows

        var columnsProperties = {
            ";
        // line 70
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["aGroupingAreasData"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["aAreaData"]) {
            // line 71
            echo "            '";
            echo twig_escape_filter($this->env, $this->getAttribute($context["aAreaData"], "sId", array()), "html", null, true);
            echo "': ";
            echo twig_jsonencode_filter($this->getAttribute($context["aAreaData"], "aColumnsDefinition", array()));
            echo ",
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aAreaData'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        echo "        };
        var rawData = {
            ";
        // line 75
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["aGroupingAreasData"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["aAreaData"]) {
            // line 76
            echo "            '";
            echo twig_escape_filter($this->env, $this->getAttribute($context["aAreaData"], "sId", array()), "html", null, true);
            echo "': ";
            echo twig_jsonencode_filter($this->getAttribute($context["aAreaData"], "aItems", array()));
            echo ",
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aAreaData'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 78
        echo "        };

        // Show a loader inside the table
        var showTableLoader = function (oElem) {
            oElem.children('tbody').html('<tr><td class=\"datatables_overlay\" colspan=\"100\">' + \$('#page_overlay').html() + '</td></tr>');
        };
        // Columns definition for the table from the columnsProperties
        var getColumnsDefinition = function (tableName) {
            var tableProperties = columnsProperties[tableName];

            if (tableProperties === undefined && window.console) {
                console.log('Could not retrieve columns properties for table \"' + tableName + '\"');
                return false;
            }
            if (rawData[tableName] === undefined && window.console) {
                console.log('Could not retrieve data for table \"' + tableName + '\"');
                return false;
            }

            var columnsDefinition = [];

            for (key in tableProperties) {
                // Regular attribute columns
                if (key !== '_ui_extensions') {
                    columnsDefinition.push({
                        \"width\": \"auto\",
                        \"searchable\": true,
                        \"sortable\": (sDataLoading === '";
        // line 105
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"), "html", null, true);
        echo "'),
                        \"title\": tableProperties[key].title,
                        \"defaultContent\": \"\",
                        \"type\": \"html\",
                        \"data\": \"attributes.\" + key + \".att_code\",
                        \"render\": function (att_code, type, row) {
                            var cellElem;
                            var itemActions;
                            var itemPrimarayAction;

                            // Preparing action on the cell
                            // Note : For now we will use only one action, the secondary actions are therefore not implemented. Only the data structure is done.
                            itemActions = row.attributes[att_code].actions;

                            // Preparing the cell data
                            cellElem = (itemActions.length > 0) ? \$('<a></a>') : \$('<span></span>');
                            cellElem.html(row.attributes[att_code].value);
                            // Building actions
                            if (itemActions.length > 0) {
                                // - Primary action
                                itemPrimaryAction = itemActions[0];
                                switch (itemPrimaryAction.type) {
                                    case '";
        // line 127
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\ManageBrick::ENUM_ACTION_VIEW"), "html", null, true);
        echo "':
                                        url = '";
        // line 128
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_view", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, itemPrimaryAction.class).replace(/-objectId-/, itemPrimaryAction.id);
                                        break;
                                    case '";
        // line 130
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\ManageBrick::ENUM_ACTION_EDIT"), "html", null, true);
        echo "':
                                        url = '";
        // line 131
        echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_object_edit", 1 => array("sObjectClass" => "-objectClass-", "sObjectId" => "-objectId-")), "method");
        echo "'.replace(/-objectClass-/, itemPrimaryAction.class).replace(/-objectId-/, itemPrimaryAction.id);
                                        break;
                                    default:
                                        url = '#';
                                        //console.log('Action \"'+itemPrimaryAction+'\" not implemented');
                                        break;
                                }
                                SetActionUrl(cellElem, url);
                                SetActionOpeningTarget(cellElem, itemPrimaryAction.opening_target);

                                // - Secondary actions
                                // Not done for now, only the data structure is ready in case we need it later
                            }

                            return cellElem.prop('outerHTML');
                        },
                    });
                }
                // UI extensions buttons
                else {
                    columnsDefinition.push({
                        \"width\": \"auto\",
                        \"searchable\": false,
                        \"sortable\": (sDataLoading === '";
        // line 154
        echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"), "html", null, true);
        echo "'),
                        \"title\": tableProperties[key].title,
                        \"defaultContent\": \"\",
                        \"type\": \"html\",
                        \"data\": \"attributes.\" + key + \".att_code\",
                        \"render\": function (att_code, type, row) {
                            var cellElem = \$('<div class=\"group-actions-wrapper\"></div>');
                            var actionsCount = row.actions.length;

                            // Adding menu wrapper in case there are several actions
                            var actionsElem = \$('<div></div>');
                            actionsElem.appendTo(cellElem);
                            if (actionsCount > 1) {
                                actionsElem.addClass('group-actions pull-right');

                                // Adding hamburger icon toggler
                                actionsElem.append(
                                    \$('<a class=\"glyphicon glyphicon-menu-hamburger\" data-toggle=\"collapse\" data-target=\"#item-actions-menu-' + row.id + '\"></a>')
                                );

                                // Adding sub menu
                                var actionsSSMenuElem = \$('<div id=\"item-actions-menu-' + row.id + '\" class=\"item-action-wrapper panel panel-default\"></div>')
                                    .appendTo(actionsElem);
                                var actionsSSMenuContainerElem = \$('<div class=\"panel-body\"></div>')
                                    .appendTo(actionsSSMenuElem);
                            }

                            // Adding actions
                            for (var i in row.actions) {
                                var actionDef = row.actions[i];
                                var actionElem = \$('<a></a>')
                                    .attr('href', actionDef.url)
                                    .append(\$('<span></span>').html(actionDef.label));

                                // Adding css classes to action
                                for (var j in actionDef.css_classes) {
                                    actionElem.addClass(actionDef.css_classes[j]);
                                }

                                // Performing specific treatment regarding the action type
                                if (actionDef.type === 'button') {
                                    // External files
                                    // Note: Not supported yet

                                    // On click callback
                                    actionElem.attr('onclick', actionDef.onclick);
                                }
                                else if (actionDef.type === 'link') {
                                    actionElem.attr('target', actionDef.target);
                                }

                                if (actionsCount > 1) {
                                    actionsSSMenuContainerElem.append(\$('<p></p>').append(actionElem));
                                }
                                else {
                                    actionsElem.append(actionElem);
                                }
                            }

                            return cellElem.prop('outerHTML');
                        }
                    });
                }
            }

            return columnsDefinition;
        };

        \$(document).ready(function () {
            ";
        // line 223
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["aGroupingAreasData"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["aAreaData"]) {
            // line 224
            echo "            ";
            $context["sAreaId"] = $this->getAttribute($context["aAreaData"], "sId", array());
            // line 225
            echo "
            \$(function () {
                \$('[data-toggle=\"tooltip\"]').tooltip()
            })

            showTableLoader(\$('#table-";
            // line 230
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo "'));
\t\t\tvar oTable";
            // line 231
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo " = \$('#table-";
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo "').DataTable({
                \"language\": {
                    \"processing\": \"";
            // line 233
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Processing")), "html", null, true);
            echo "\",
                    \"search\": \"";
            // line 234
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Search")), "html", null, true);
            echo "\",
                    \"lengthMenu\": \"";
            // line 235
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:LengthMenu")), "html", null, true);
            echo "\",
                    \"zeroRecords\": \"";
            // line 236
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:ZeroRecords")), "html", null, true);
            echo "\",
                    \"info\": \"";
            // line 237
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Info")), "html", null, true);
            echo "\",
                    \"infoEmpty\": \"";
            // line 238
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:InfoEmpty")), "html", null, true);
            echo "\",
                    \"infoFiltered\": \"(";
            // line 239
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:InfoFiltered")), "html", null, true);
            echo ")\",
                    \"emptyTable\": \"";
            // line 240
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:EmptyTable")), "html", null, true);
            echo "\",
                    \"paginate\": {
                        \"first\": \"";
            // line 242
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Paginate:First")), "html", null, true);
            echo "\",
                        \"previous\": \"";
            // line 243
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Paginate:Previous")), "html", null, true);
            echo "\",
                        \"next\": \"";
            // line 244
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Paginate:Next")), "html", null, true);
            echo "\",
                        \"last\": \"";
            // line 245
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Paginate:Last")), "html", null, true);
            echo "\"
                    },
                    \"aria\": {
                        \"sortAscending\": \": ";
            // line 248
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Sort:Ascending")), "html", null, true);
            echo "\",
                        \"sortDescending\": \": ";
            // line 249
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:Sort:Descending")), "html", null, true);
            echo "\"
                    }
                },
                \"lengthMenu\": [[10, 20, 50, -1], [10, 20, 50, \"";
            // line 252
            echo twig_escape_filter($this->env, call_user_func_array($this->env->getFilter('dict_s')->getCallable(), array("Portal:Datatables:Language:DisplayLength:All")), "html", null, true);
            echo "\"]],
                \"displayLength\": ";
            // line 253
            echo twig_escape_filter($this->env, twig_constant("Combodo\\nt3\\Portal\\Brick\\ManageBrick::DEFAULT_LIST_LENGTH"), "html", null, true);
            echo ",
                \"dom\": '<\"row\"<\"col-sm-6\"l><\"col-sm-6\"<f><\"visible-xs\"p>>>t<\"row\"<\"col-sm-6\"i><\"col-sm-6\"p>>',
                \"columns\": getColumnsDefinition('";
            // line 255
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo "'),
                \"order\": [],
                \"rowCallback\": function (oRow, oData) {
                    if (oData.highlight_class !== undefined) {
                        var sHighlightClass = oData.highlight_class;
                        var sBSHiglightClass = '';

                        // Adding classic nt3 class
                        \$(oRow).addClass(sHighlightClass);
                        // Adding mapped BS class
                        if (sHighlightClass === '";
            // line 265
            echo twig_escape_filter($this->env, twig_constant("HILIGHT_CLASS_CRITICAL"), "html", null, true);
            echo "') {
                            sBSHiglightClass = 'danger';
                        }
                        else if (sHighlightClass === '";
            // line 268
            echo twig_escape_filter($this->env, twig_constant("HILIGHT_CLASS_WARNING"), "html", null, true);
            echo "') {
                            sBSHiglightClass = 'warning';
                        }
                        else if (sHighlightClass === '";
            // line 271
            echo twig_escape_filter($this->env, twig_constant("HILIGHT_CLASS_OK"), "html", null, true);
            echo "') {
                            sBSHiglightClass = 'success';
                        }
                        \$(oRow).addClass(sBSHiglightClass);
                    }
                },
                \"drawCallback\": function (settings) {
                    // Hiding pagination if only one page
                    if (\$(this).closest('.dataTables_wrapper').find('.dataTables_paginate:last .paginate_button:not(.previous):not(.next)').length < 2) {
                        \$(this).closest('.dataTables_wrapper').find('.dataTables_paginate, .dataTables_info').hide();
                    }
                    else {
                        \$(this).closest('.dataTables_wrapper').find('.dataTables_paginate, .dataTables_info').show();
                    }
                },
                ";
            // line 286
            if ((($context["sDataLoading"] ?? null) == twig_constant("Combodo\\nt3\\Portal\\Brick\\AbstractBrick::ENUM_DATA_LOADING_FULL"))) {
                // line 287
                echo "                \"data\": rawData['";
                echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
                echo "'],
                ";
            } else {
                // line 289
                echo "                \"processing\": true,
                \"serverSide\": true,
                ";
                // line 292
                echo "                \"ajax\": {
\t\t\t\t\t\"url\": \"";
                // line 293
                echo $this->getAttribute($this->getAttribute(($context["app"] ?? null), "url_generator", array()), "generate", array(0 => "p_manage_brick_lazy", 1 => array("sBrickId" => ($context["sBrickId"] ?? null), "sGroupingTab" => ($context["sGroupingTab"] ?? null), "sGroupingArea" => ($context["sAreaId"] ?? null))), "method");
                echo "\",
                    \"data\": function (d) {
                        d.iPageNumber = Math.floor(d.start / d.length) + 1;
                        d.iListLength = d.length;
                        d.columns = null;
                        d.orders = null;

                        ";
                // line 300
                if ( !(null === ($context["sSearchValue"] ?? null))) {
                    // line 301
                    echo "                        // Sets default filter value
                        if (d.draw === 1) {
                            \$('#table-";
                    // line 303
                    echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
                    echo "_filter input').val('";
                    echo twig_escape_filter($this->env, ($context["sSearchValue"] ?? null), "html", null, true);
                    echo "');
                            d.search.value = \$('#table-";
                    // line 304
                    echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
                    echo "_filter input').val();
                        }
                        ";
                }
                // line 307
                echo "                        if (d.search.value) {
                            d.sSearchValue = d.search.value;
                        }
                    }
                }
                ";
            }
            // line 313
            echo "            });

            // Overrides filter input to apply throttle. Otherwise, an ajax request is send each time a key is pressed
            // Also removes accents from search string
            // Note : The '.off()' call is to unbind event from DataTables that where triggered before we could intercept anything
            \$('#table-";
            // line 318
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo "_filter input').off().on('keyup', function () {
                var me = this;

                clearTimeout(oKeyTimeout);
                oKeyTimeout = setTimeout(function () {
                    oTable";
            // line 323
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo ".search(me.value.latinise()).draw();
                }, iSearchThrottle);
            });// Shows a loader in the table when processing
            \$('#table-";
            // line 326
            echo twig_escape_filter($this->env, ($context["sAreaId"] ?? null), "html", null, true);
            echo "').on('processing.dt', function (event, settings, processing) {
                if (processing === true) {
                    showTableLoader(\$(this));
                }
            });
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['aAreaData'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 332
        echo "
            // Auto collapse item actions popup
            \$('body').click(function () {
                \$('table .item-action-wrapper.collapse.in').collapse('hide');
            });
        });
    </script>
";
    }

    public function getTemplateName()
    {
        return "nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  614 => 332,  602 => 326,  596 => 323,  588 => 318,  581 => 313,  573 => 307,  567 => 304,  561 => 303,  557 => 301,  555 => 300,  545 => 293,  542 => 292,  538 => 289,  532 => 287,  530 => 286,  512 => 271,  506 => 268,  500 => 265,  487 => 255,  482 => 253,  478 => 252,  472 => 249,  468 => 248,  462 => 245,  458 => 244,  454 => 243,  450 => 242,  445 => 240,  441 => 239,  437 => 238,  433 => 237,  429 => 236,  425 => 235,  421 => 234,  417 => 233,  410 => 231,  406 => 230,  399 => 225,  396 => 224,  392 => 223,  320 => 154,  294 => 131,  290 => 130,  285 => 128,  281 => 127,  256 => 105,  227 => 78,  216 => 76,  212 => 75,  208 => 73,  197 => 71,  193 => 70,  183 => 63,  176 => 60,  173 => 59,  164 => 53,  160 => 51,  158 => 50,  155 => 49,  152 => 48,  146 => 47,  138 => 42,  134 => 40,  128 => 37,  121 => 33,  116 => 32,  114 => 31,  110 => 30,  106 => 28,  103 => 27,  100 => 26,  95 => 25,  92 => 24,  89 => 23,  85 => 21,  77 => 18,  71 => 16,  69 => 15,  65 => 14,  61 => 13,  57 => 12,  50 => 11,  46 => 10,  43 => 9,  40 => 8,  37 => 7,  30 => 5,  11 => 3,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig", "/home/nt3/env-production/nt3-portal-base/portal/src/views/bricks/manage/layout-table.html.twig");
    }
}
