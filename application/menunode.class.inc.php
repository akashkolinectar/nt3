<?php

require_once(APPROOT . '/application/utils.inc.php');
require_once(APPROOT . '/application/template.class.inc.php');
require_once(APPROOT . "/application/user.dashboard.class.inc.php");

$postData = file_get_contents("php://input");
$jsonData = json_decode($postData,TRUE);


/**
 * This class manipulates, stores and displays the navigation menu used in the application
 * In order to improve the modularity of the data model and to ease the update/migration
 * between evolving data models, the menus are no longer stored in the database, but are instead
 * built on the fly each time a page is loaded.
 * The application's menu is organized into top-level groups with, inside each group, a tree of menu items.
 * Top level groups do not display any content, they just expand/collapse.
 * Sub-items drive the actual content of the page, they are based either on templates, OQL queries or full (external?) web pages.
 *
 * Example:
 * Here is how to insert the following items in the application's menu:
 *   +----------------------------------------+
 *   | Configuration Management Group         | >> Top level group
 *   +----------------------------------------+
 * 		+ Configuration Management Overview     >> Template based menu item
 * 		+ Contacts								>> Template based menu item
 * 			+ Persons							>> Plain list (OQL based)
 * 			+ Teams								>> Plain list (OQL based)
 *
 * // Create the top-level group. fRank = 1, means it will be inserted after the group '0', which is usually 'Welcome'
 * $oConfigMgmtMenu = new MenuGroup('ConfigurationManagementMenu', 1);
 * // Create an entry, based on a custom template, for the Configuration management overview, under the top-level group
 * new TemplateMenuNode('ConfigurationManagementMenu', '../somedirectory/configuration_management_menu.html', $oConfigMgmtMenu->GetIndex(), 0);
 * // Create an entry (template based) for the overview of contacts
 * $oContactsMenu = new TemplateMenuNode('ContactsMenu', '../somedirectory/configuration_management_menu.html',$oConfigMgmtMenu->GetIndex(), 1);
 * // Plain list of persons
 * new OQLMenuNode('PersonsMenu', 'SELECT bizPerson', $oContactsMenu->GetIndex(), 0);
 *
 */
class ApplicationMenu {

    /**
     * @var bool
     */
    static $bAdditionalMenusLoaded = false;

    /**
     * @var array
     */
    static $aRootMenus = array();

    /**
     * @var array
     */
    static $aMenusIndex = array();

    /**
     * @var string
     */
    static $sFavoriteSiloQuery = 'SELECT Organization';

    static public function LoadAdditionalMenus() {
        if (!self::$bAdditionalMenusLoaded) {
            // Build menus from module handlers
            //
			foreach (get_declared_classes() as $sPHPClass) {
                if (is_subclass_of($sPHPClass, 'ModuleHandlerAPI')) {
                    $aCallSpec = array($sPHPClass, 'OnMenuCreation');
                    call_user_func($aCallSpec);
                }
            }

            // Build menus from the menus themselves (e.g. the ShortcutContainerMenuNode will do that)
            //
			foreach (self::$aRootMenus as $aMenu) {
                $oMenuNode = self::GetMenuNode($aMenu['index']);
                $oMenuNode->PopulateChildMenus();
            }

            self::$bAdditionalMenusLoaded = true;
        }
    }

    /**
     * Set the query used to limit the list of displayed organizations in the drop-down menu
     * @param $sOQL string The OQL query returning a list of Organization objects
     * @return void
     */
    static public function SetFavoriteSiloQuery($sOQL) {
        self::$sFavoriteSiloQuery = $sOQL;
    }

    /**
     * Get the query used to limit the list of displayed organizations in the drop-down menu
     * @return string The OQL query returning a list of Organization objects
     */
    static public function GetFavoriteSiloQuery() {
        return self::$sFavoriteSiloQuery;
    }

    /**
     * Check wether a menu Id is enabled or not
     * @param $sMenuId
     * @throws DictExceptionMissingString
     */
    static public function CheckMenuIdEnabled($sMenuId) {
        self::LoadAdditionalMenus();
        $oMenuNode = self::GetMenuNode(self::GetMenuIndexById($sMenuId));
        if (is_null($oMenuNode) || !$oMenuNode->IsEnabled()) {
            require_once(APPROOT . '/setup/setuppage.class.inc.php');
            $oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
            $oP->add("<h1>" . Dict::S('UI:Login:Error:AccessRestricted') . "</h1>\n");
            $oP->p("<a href=\"" . utils::GetAbsoluteUrlAppRoot() . "pages/logoff.php\">" . Dict::S('UI:LogOffMenu') . "</a>");
            $oP->output();
            exit;
        }
    }

    /**
     * Main function to add a menu entry into the application, can be called during the definition
     * of the data model objects
     * @param MenuNode $oMenuNode
     * @param $iParentIndex
     * @param $fRank
     * @return int
     */
    static public function InsertMenu(MenuNode $oMenuNode, $iParentIndex, $fRank) {
        $index = self::GetMenuIndexById($oMenuNode->GetMenuId());
        if ($index == -1) {
            // The menu does not already exist, insert it
            $index = count(self::$aMenusIndex);

            if ($iParentIndex == -1) {
                $sParentId = '';
                self::$aRootMenus[] = array('rank' => $fRank, 'index' => $index);
            } else {
                $sParentId = self::$aMenusIndex[$iParentIndex]['node']->GetMenuId();
                self::$aMenusIndex[$iParentIndex]['children'][] = array('rank' => $fRank, 'index' => $index);
            }

            // Note: At the time when 'parent', 'rank' and 'source_file' have been added for the reflection API,
            //       they were not used to display the menus (redundant or unused)
            //
			$aBacktrace = debug_backtrace();
            $sFile = isset($aBacktrace[2]["file"]) ? $aBacktrace[2]["file"] : $aBacktrace[1]["file"];
            self::$aMenusIndex[$index] = array('node' => $oMenuNode, 'children' => array(), 'parent' => $sParentId, 'rank' => $fRank, 'source_file' => $sFile);
        } else {
            // the menu already exists, let's combine the conditions that make it visible
            self::$aMenusIndex[$index]['node']->AddCondition($oMenuNode);
        }

        return $index;
    }

    /**
     * Reflection API - Get menu entries
     */
    static public function ReflectionMenuNodes() {
        self::LoadAdditionalMenus();
        return self::$aMenusIndex;
    }

    /**
     * Entry point to display the whole menu into the web page, used by NT3WebPage
     * @param $oPage
     * @param $aExtraParams
     * @throws DictExceptionMissingString
     */
    static public function DisplayMenu($oPage, $aExtraParams) {
        self::LoadAdditionalMenus();
        // Sort the root menu based on the rank
        usort(self::$aRootMenus, array('ApplicationMenu', 'CompareOnRank'));
        $iAccordion = 0;
        $iActiveAccordion = $iAccordion;
        $iActiveMenu = self::GetMenuIndexById(self::GetActiveNodeId());

            // echo "<pre>";
            // print_r(self::$aRootMenus);
            // exit; admin  || $aMenu['rank'] == 80 Config  || $aMenu['rank'] == 20
        $aRootMenusBulk = self::$aRootMenus;
        $NDRMenu = array(array('rank' => 78,'index' => 51));
        array_splice($aRootMenusBulk,3,0,$NDRMenu); 

        foreach ($aRootMenusBulk as $aMenu) {

            // || $aMenu['rank'] == 70 (Data Administrator)

            $permFlag = FALSE; $permRank = array();
            $lanSet = CMDBSource::QueryToArray("SELECT id FROM ntpriv_user WHERE login = '".$_SESSION['auth_user']."'");
            if(!empty($lanSet)){
                $userID = $lanSet[0]['id'];
                $profiles = CMDBSource::QueryToArray("SELECT perm.permission_name FROM ntpermission perm LEFT JOIN ntpriv_urp_userprofile prof ON prof.profileid = perm.profile_id  WHERE prof.userid = '".$userID."'");
                if(!empty($profiles)){
                    foreach($profiles as $rows){
                        $permFlag = TRUE;
                        array_push($permRank, $rows['permission_name']);
                    }
                }
            }

            if ($aMenu['rank'] == 10 || $aMenu['rank'] == 35 || $aMenu['rank'] == 42 || $aMenu['rank'] == 50 || $aMenu['rank'] == 60 || $aMenu['rank'] == 20 || $aMenu['rank'] == 80 || $aMenu['rank'] == 78) {
                
            if (($permRank && in_array($aMenu['rank'], $permRank))|| !$permRank) {

                if (!self::CanDisplayMenu($aMenu)) {
                    continue;
                }
                $oMenuNode = self::GetMenuNode($aMenu['index']);

                if($aMenu['rank']==78){
                    $oPage->AddToMenu('<h3 id="AccordionMenu_NDRConfig"> NDR (Número De Registo) </h3>');
                }else{
                    $oPage->AddToMenu('<h3 id="' . utils::GetSafeId('AccordionMenu_' . $oMenuNode->GetMenuID()) . '">' . ucwords($oMenuNode->GetTitle()) . '</h3>');
                }
                $oPage->AddToMenu('<div>');
                $oPage->AddToMenu('<ul id="list1">');

                $aChildren = self::GetChildren($aMenu['index']);


                if ($aMenu['rank'] == 35) {
                    /********** Modified by Nilesh For Open Incident Technology change position (After new incident position) **********************/
                    $openAll = $aChildren;
                    $incChild = array_chunk($aChildren,2);
                    $aChildren = $incChild[0]; // By Breaking menu active-menu for specific page will disappear
                    $bActive = self::DisplaySubMenu($oPage, $aChildren, $aExtraParams, $iActiveMenu);
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=Incident%3AOpenIncidents&c[feature]=incidentTech'>".$labelArr['incident'][0]."</a></li>");
                    $aChildren = $incChild[1];
                    /********** EOF Modified by Nilesh For Open Incident Technology change position (After new incident position) **********************/
                }
                
                /* Remove some submenus of admin tools Start - Vidya's Code*/
                if ($aMenu['rank'] == 80) {
                    unset($aChildren['1'],$aChildren['6'],$aChildren['7'],$aChildren['9'],$aChildren['11'],$aChildren['0']);
                }

               /* Commented by Mahesh 04-02-2020 if($aMenu['rank'] == 70){ // This is for data administrator
                    unset($aChildren['0'],$aChildren['1']);
                }*/
                /* Remove some submenus of admin tools End - Vidya's Code*/
                /* Added by Mahesh*/
                if($aMenu['rank'] == 70){ // This is for data administrator
                    unset($aChildren['0'],$aChildren['1']);
                }
                
                if($aMenu['rank'] == 80){
                    unset($aChildren['0'],$aChildren['1']);
                }
                /* End Added by Mahesh*/
                
                if ($aMenu['rank'] == 10) {
                    array_push($aChildren, array("rank"=>50,"index"=>5));
                }

                $bActive = self::DisplaySubMenu($oPage, $aChildren, $aExtraParams, $iActiveMenu);
                
                $labelArr = array();
                switch ($_SESSION['language']) {
                    case 'PT BR': 
                    $labelArr = array(
                            'ndr'=>array('NDR','Nova notificação de falha na entrega'),
                            'site'=>array('Site','Novo Site'),
                            'welcome'=>array('Função Tipologia','Configuração de relatório'),
                            'incident'=>array('Aberto Incidente Tecnologias')
                        );
                    break;
                    default: 
                    $labelArr = array(
                            'ndr'=>array('NDR','New NDR'),
                            'site'=>array('Site','New Site'),
                            'welcome'=>array('Role Typology','Report Configuration'),
                            'incident'=>array('Open Incident Technologies')
                        );
                    break;
                }
				
				switch ($_SESSION['language']) {
					case 'PT BR': $ListNDR = 'Lista NDR'; break;
					default: $ListNDR = 'List NDR'; break;
				}
				switch ($_SESSION['language']) {
					case 'PT BR': $newndr = 'Nova notificação de falha na entrega'; break;
					default: $newndr = 'New NDR'; break;
				}

                if ($aMenu['rank'] == 20) {
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=listsite'>".$labelArr['site'][0]."</a></li>");

                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=WelcomeMenuPage&c[feature]=addSite'>".$labelArr['site'][1]."</a></li>");
                }

                /*if($aMenu['rank']==78){
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity'>".$labelArr['ndr'][0]."</a></li>");
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addactivity'>".$labelArr['ndr'][1]."</a></li>");
                }*/
                switch ($_SESSION['language']) {
                    case 'PT BR': $CreateNewProfile = 'Criar Novo Perfil'; break;
                    default: $CreateNewProfile = 'Create New Profile'; break;
                }
                if($aMenu['rank'] == 80){ // This is for data administrator
                    unset($aChildren['0'],$aChildren['1']);$oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=createnewprofile'>".$CreateNewProfile."</a></li>");
                }

				 if($aMenu['rank']==78){
                    $oPage->AddToMenu('<li><a href="http://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=activity">'.$ListNDR.'</a></li>');
                    $oPage->AddToMenu('<li><a href="http://nt3.nectarinfotel.com/pages/UI.php?c%5Bmenu%5D=addactivity">'.$newndr.'</a></li>');//New NDR
				}

               /* if ($aMenu['rank'] == 35) {
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?operation=cancel&c[menu]=Incident%3AOpenIncidents&c[feature]=incidentTech'>".$labelArr['incident'][0]."</a></li>");
                }*/

                if ($aMenu['rank'] == 10) {
                    
                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?operation=search_form&do_search=0&class=ContactType&c[menu]=Typology'>".$labelArr['welcome'][0]."</a></li>");

                    $oPage->AddToMenu("<li><a href='http://nt3.nectarinfotel.com/pages/UI.php?c[menu]=reportConfiguration'>".$labelArr['welcome'][1]."</a></li>");
                }

                $oPage->AddToMenu('</ul>');
                if ($bActive) {
                    $iActiveAccordion = $iAccordion;
                }
                $oPage->AddToMenu('</div>');
                $iAccordion++;
            }
        }
        }

         if(isset($_GET['c'])){
            if(isset($_GET['c']['menu'])){
                if($_GET['c']['menu']=='activity' || $_GET['c']['menu']=='addactivity'
                   || $_GET['c']['menu']=='viewactivity' || $_GET['c']['menu']=='editbyactivity'){
                    $iActiveAccordion = 2;
                }
            }
        }

        $oPage->add_ready_script(
                <<<EOF
	// Accordion Menu
	$("#accordion").css({display:'block'}).accordion({ header: "h3", navigation: true, heightStyle: "content", collapsible: true,  active: $iActiveAccordion, icons: false, animate:true }); // collapsible will be enabled once the item will be selected
EOF
        );
    }

    /**
     * Recursively check if the menu and at least one of his sub-menu is enabled
     * @param array $aMenu menu entry
     * @return bool true if at least one menu is enabled
     */
    static private function CanDisplayMenu($aMenu) {
        $oMenuNode = self::GetMenuNode($aMenu['index']);
        if ($oMenuNode->IsEnabled()) {
            $aChildren = self::GetChildren($aMenu['index']);
            if (count($aChildren) > 0) {
                foreach ($aChildren as $aSubMenu) {
                    if (self::CanDisplayMenu($aSubMenu)) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Handles the display of the sub-menus (called recursively if necessary)
     * @param WebPage $oPage
     * @param array $aMenus
     * @param array $aExtraParams
     * @param int $iActiveMenu
     * @return true if the currently selected menu is one of the submenus
     * @throws DictExceptionMissingString
     */
    static protected function DisplaySubMenu($oPage, $aMenus, $aExtraParams, $iActiveMenu = -1) {
        // Sort the menu based on the rank
        $bActive = false;
        usort($aMenus, array('ApplicationMenu', 'CompareOnRank'));
        foreach ($aMenus as $aMenu) {
            $index = $aMenu['index'];
            $oMenu = self::GetMenuNode($index);
            if ($oMenu->IsEnabled()) {
                $aChildren = self::GetChildren($index);
                $sCSSClass = (count($aChildren) > 0) ? ' class="submenu"' : '';
                $sHyperlink = $oMenu->GetHyperlink($aExtraParams);                

                switch ($oMenu->GetMenuID()) {
                    case 'Typology': case 'FAQCategory': case 'FAQ': case 'Contact': case 'NewContact': case 'SearchContacts': case 'Location':
                    case 'NotificationsMenu': case 'AuditCategories': case 'ExportMenu': case 'UniversalSearchMenu': break;
                    default:
                        if($oMenu->GetMenuID()=='UserAccountsMenu'){
                            $menuTitle = 'User Management';
                        }
						
					/****Edited by Priya*****/
					
					switch ($_SESSION['language']) {
						case 'PT BR': $NewElement = "Novo elemento"; break;
						default: $NewElement = "New Element"; break;
					}
					switch ($_SESSION['language']) {
						case 'PT BR': $SearchforElements = "Procurar elementos"; break;
						default: $SearchforElements = "Search for Elements"; break;
					}
					switch ($_SESSION['language']) {
						case 'PT BR': $GroupsofElements = "Grupos de Elementos"; break;
						default: $GroupsofElements = "Groups of Elements"; break;
					}
					switch ($_SESSION['language']) {
						case 'PT BR': $Area = "Área"; break;
						default: $Area = "Area"; break;
					}
                    if($oMenu->GetMenuID()=='NewCI'){
                        $menuTitle = '<label>'.$NewElement.'</label>';
                    }else if($oMenu->GetMenuID()=='SearchCIs'){
                        $menuTitle = '<label>'.$SearchforElements.'</label>';
                    }else if($oMenu->GetMenuID()=='Group'){
                        $menuTitle = '<label>'.$GroupsofElements.'</label>';
                    }else if($oMenu->GetMenuID()=='Organization'){
                        $menuTitle = '<label>'.$Area.'</label>';
                    }
					else {
                        $menuTitle = $oMenu->GetTitle();
                        }
                        //if($oMenu->GetMenuID()!='FAQCategory' && $oMenu->GetMenuID()!='FAQ'){
                        if ($sHyperlink != '') {
                            $oPage->AddToMenu('<li id="' . utils::GetSafeId('AccordionMenu_' . $oMenu->GetMenuID()) . '" ' . $sCSSClass . '><a href="' . $oMenu->GetHyperlink($aExtraParams) . '">' . $menuTitle . '</a></li>');
                        } else {
                            $oPage->AddToMenu('<li id="' . utils::GetSafeId('AccordionMenu_' . $oMenu->GetMenuID()) . '" ' . $sCSSClass . '>' . $menuTitle . '</li>');
                        }
                        if ($iActiveMenu == $index) {
                            $bActive = true;
                        }
                        if (count($aChildren) > 0) {
                            $oPage->AddToMenu('<ul>');
                            $bActive |= self::DisplaySubMenu($oPage, $aChildren, $aExtraParams, $iActiveMenu);
                            $oPage->AddToMenu('</ul>');
                        }
                   // }
                    break;
                }

            }
        }
        return $bActive;
    }

    /**
     * Helper function to sort the menus based on their rank
     * @param $a
     * @param $b
     * @return int
     */
    static public function CompareOnRank($a, $b) {
        $result = 1;
        if ($a['rank'] == $b['rank']) {
            $result = 0;
        }
        if ($a['rank'] < $b['rank']) {
            $result = -1;
        }
        return $result;
    }

    /**
     * Helper function to retrieve the MenuNode Object based on its ID
     * @param int $index
     * @return MenuNode|null
     */
    static public function GetMenuNode($index) {
        return isset(self::$aMenusIndex[$index]) ? self::$aMenusIndex[$index]['node'] : null;
    }

    /**
     * Helper function to get the list of child(ren) of a menu
     * @param int $index
     * @return array
     */
    static public function GetChildren($index) {
        return self::$aMenusIndex[$index]['children'];
    }

    /**
     * Helper function to get the ID of a menu based on its name
     * @param string $sTitle Title of the menu (as passed when creating the menu)
     * @return integer ID of the menu, or -1 if not found
     */
    static public function GetMenuIndexById($sTitle) {
        $index = -1;
        foreach (self::$aMenusIndex as $aMenu) {
            if ($aMenu['node']->GetMenuId() == $sTitle) {
                $index = $aMenu['node']->GetIndex();
                break;
            }
        }
        return $index;
    }

    /**
     * Retrieves the currently active menu (if any, otherwise the first menu is the default)
     * @return string The Id of the currently active menu
     */
    static public function GetActiveNodeId() {
        $oAppContext = new ApplicationContext();
        $sMenuId = $oAppContext->GetCurrentValue('menu', null);
        if ($sMenuId === null) {
            $sMenuId = self::GetDefaultMenuId();
        }
        return $sMenuId;
    }

    /**
     * @return null|string
     */
    static public function GetDefaultMenuId() {
        static $sDefaultMenuId = null;
        if (is_null($sDefaultMenuId)) {
            // Make sure the root menu is sorted on 'rank'
            usort(self::$aRootMenus, array('ApplicationMenu', 'CompareOnRank'));
            $oFirstGroup = self::GetMenuNode(self::$aRootMenus[0]['index']);
            $aChildren = self::$aMenusIndex[$oFirstGroup->GetIndex()]['children'];
            usort($aChildren, array('ApplicationMenu', 'CompareOnRank'));
            $oMenuNode = self::GetMenuNode($aChildren[0]['index']);
            $sDefaultMenuId = $oMenuNode->GetMenuId();
        }
        return $sDefaultMenuId;
    }

    /**
     * @param $sMenuId
     * @return string
     */
    static public function GetRootMenuId($sMenuId) {
        $iMenuIndex = self::GetMenuIndexById($sMenuId);
        if ($iMenuIndex == -1) {
            return '';
        }
        $oMenu = ApplicationMenu::GetMenuNode($iMenuIndex);
        while ($oMenu->GetParentIndex() != -1) {
            $oMenu = ApplicationMenu::GetMenuNode($oMenu->GetParentIndex());
        }
        return $oMenu->GetMenuId();
    }

}

/**
 * Root class for all the kind of node in the menu tree, data model providers are responsible for instantiating
 * MenuNodes (i.e instances from derived classes) in order to populate the application's menu. Creating an objet
 * derived from MenuNode is enough to have it inserted in the application's main menu.
 * The class NT3WebPage, takes care of 3 items:
 * +--------------------+
 * | Welcome            |
 * +--------------------+
 * 		Welcome To NT3
 * +--------------------+
 * | Tools              |
 * +--------------------+
 * 		CSV Import
 * +--------------------+
 * | Admin Tools        |
 * +--------------------+
 * 		User Accounts
 * 		Profiles
 * 		Notifications
 * 		Run Queries
 * 		Export
 * 		Data Model
 * 		Universal Search
 *
 * All the other menu items must constructed along with the various data model modules
 */
abstract class MenuNode {

    /**
     * @var string
     */
    protected $sMenuId;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var int
     */
    protected $iParentIndex;

    /**
     * Properties reflecting how the node has been declared
     */
    protected $aReflectionProperties;

    /**
     * Class of objects to check if the menu is enabled, null if none
     */
    protected $m_aEnableClasses;

    /**
     * User Rights Action code to check if the menu is enabled, null if none
     */
    protected $m_aEnableActions;

    /**
     * User Rights allowed results (actually a bitmask) to check if the menu is enabled, null if none
     */
    protected $m_aEnableActionResults;

    /**
     * Stimulus to check: if the user can 'apply' this stimulus, then she/he can see this menu
     */
    protected $m_aEnableStimuli;

    /**
     * Create a menu item, sets the condition to have it displayed and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param integer $iParentIndex ID of the parent menu, pass -1 for top level (group) items
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param mixed $iActionCode UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus The user can see this menu if she/he has enough rights to apply this stimulus
     */
    public function __construct($sMenuId, $iParentIndex = -1, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        $this->sMenuId = $sMenuId;
        $this->iParentIndex = $iParentIndex;
        $this->aReflectionProperties = array();
        if (strlen($sEnableClass) > 0) {
            $this->aReflectionProperties['enable_class'] = $sEnableClass;
            $this->aReflectionProperties['enable_action'] = $iActionCode;
            $this->aReflectionProperties['enable_permission'] = $iAllowedResults;
            $this->aReflectionProperties['enable_stimulus'] = $sEnableStimulus;
        }
        $this->m_aEnableClasses = array($sEnableClass);
        $this->m_aEnableActions = array($iActionCode);
        $this->m_aEnableActionResults = array($iAllowedResults);
        $this->m_aEnableStimuli = array($sEnableStimulus);
        $this->index = ApplicationMenu::InsertMenu($this, $iParentIndex, $fRank);
    }

    /**
     * @return array
     */
    public function ReflectionProperties() {
        return $this->aReflectionProperties;
    }

    /**
     * @return string
     */
    public function GetMenuId() {
        return $this->sMenuId;
    }

    /**
     * @return int
     */
    public function GetParentIndex() {
        return $this->iParentIndex;
    }

    /**
     * @return string
     * @throws DictExceptionMissingString
     */
    public function GetTitle() {
        return Dict::S("Menu:$this->sMenuId", str_replace('_', ' ', $this->sMenuId));
    }

    /**
     * @return string
     * @throws DictExceptionMissingString
     */
    public function GetLabel() {
        $sRet = Dict::S("Menu:$this->sMenuId+", "");
        if ($sRet === '') {
            if ($this->iParentIndex != -1) {
                $oParentMenu = ApplicationMenu::GetMenuNode($this->iParentIndex);
                $sRet = $oParentMenu->GetTitle() . ' / ' . $this->GetTitle();
            } else {
                $sRet = $this->GetTitle();
            }
            //$sRet = $this->GetTitle();
        }
        return $sRet;
    }

    /**
     * @return int
     */
    public function GetIndex() {
        return $this->index;
    }

    public function PopulateChildMenus() {
        foreach (ApplicationMenu::GetChildren($this->GetIndex()) as $aMenu) {
            $index = $aMenu['index'];
            $oMenu = ApplicationMenu::GetMenuNode($index);
            $oMenu->PopulateChildMenus();
        }
    }

    /**
     * @param $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        $aExtraParams['c[menu]'] = $this->GetMenuId();
        return $this->AddParams(utils::GetAbsoluteUrlAppRoot() . 'pages/UI.php', $aExtraParams);
    }

    /**
     * Add a limiting display condition for the same menu node. The conditions will be combined with a AND
     * @param $oMenuNode MenuNode Another definition of the same menu node, with potentially different access restriction
     * @return void
     */
    public function AddCondition(MenuNode $oMenuNode) {
        foreach ($oMenuNode->m_aEnableClasses as $index => $sClass) {
            $this->m_aEnableClasses[] = $sClass;
            $this->m_aEnableActions[] = $oMenuNode->m_aEnableActions[$index];
            $this->m_aEnableActionResults[] = $oMenuNode->m_aEnableActionResults[$index];
            $this->m_aEnableStimuli[] = $oMenuNode->m_aEnableStimuli[$index];
        }
    }

    /**
     * Tells whether the menu is enabled (i.e. displayed) for the current user
     * @return bool True if enabled, false otherwise
     */
    public function IsEnabled() {
        foreach ($this->m_aEnableClasses as $index => $sClass) {
            if ($sClass != null) {
                if (MetaModel::IsValidClass($sClass)) {
                    if ($this->m_aEnableStimuli[$index] != null) {
                        if (!UserRights::IsStimulusAllowed($sClass, $this->m_aEnableStimuli[$index])) {
                            return false;
                        }
                    }
                    if ($this->m_aEnableActions[$index] != null) {
                        // Menus access rights ignore the archive mode
                        utils::PushArchiveMode(false);
                        $iResult = UserRights::IsActionAllowed($sClass, $this->m_aEnableActions[$index]);
                        utils::PopArchiveMode();
                        if (!($iResult & $this->m_aEnableActionResults[$index])) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     * @return mixed
     */
    public abstract function RenderContent(WebPage $oPage, $aExtraParams = array());

    /**
     * @param $sHyperlink
     * @param $aExtraParams
     * @return string
     */
    protected function AddParams($sHyperlink, $aExtraParams) {
        if (count($aExtraParams) > 0) {
            $aQuery = array();
            $sSeparator = '?';
            if (strpos($sHyperlink, '?') !== false) {
                $sSeparator = '&';
            }
            foreach ($aExtraParams as $sName => $sValue) {
                $aQuery[] = urlencode($sName) . '=' . urlencode($sValue);
            }
            $sHyperlink .= $sSeparator . implode('&', $aQuery);
        }
        return $sHyperlink;
    }

}

/**
 * This class implements a top-level menu group. A group is just a container for sub-items
 * it does not display a page by itself
 */
class MenuGroup extends MenuNode {

    /**
     * Create a top-level menu group and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param float $fRank Number used to order the list, the groups are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $fRank, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, -1 /* no parent, groups are at root level */, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        assert(false); // Shall never be called, groups do not display any content
    }

}

/**
 * This class defines a menu item which content is based on a custom template.
 * Note the template can be either a local file or an URL !
 */
class TemplateMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sTemplateFile;

    /**
     * Create a menu item based on a custom template and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sTemplateFile Path (or URL) to the file that will be used as a template for displaying the page's content
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $sTemplateFile, $iParentIndex, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sTemplateFile = $sTemplateFile;
        $this->aReflectionProperties['template_file'] = $sTemplateFile;
    }

    /**
     * @param $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        if ($this->sTemplateFile == '')
            return '';
        return parent::GetHyperlink($aExtraParams);
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     * @return mixed|void
     * @throws DictExceptionMissingString
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        ApplicationMenu::CheckMenuIdEnabled($this->GetMenuId());
        $sTemplate = @file_get_contents($this->sTemplateFile);
        if ($sTemplate !== false) {
            $aExtraParams['table_id'] = 'Menu_' . $this->GetMenuId();
            $oTemplate = new DisplayTemplate($sTemplate);
            $oTemplate->Render($oPage, $aExtraParams);
        } else {
            $oPage->p("Error: failed to load template file: '{$this->sTemplateFile}'"); // No need to translate ?
        }
    }

}

/**
 * This class defines a menu item that uses a standard template to display a list of items therefore it allows
 * only two parameters: the page's title and the OQL expression defining the list of items to be displayed
 */
class OQLMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sPageTitle;

    /**
     * @var string
     */
    protected $sOQL;

    /**
     * @var bool
     */
    protected $bSearch;

    /**
     * @var bool|null
     */
    protected $bSearchFormOpen;

    /**
     * Extra parameters to be passed to the display block to fine tune its appearence
     */
    protected $m_aParams;

    /**
     * Create a menu item based on an OQL query and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sOQL OQL query defining the set of objects to be displayed
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param bool $bSearch Whether or not to display a (collapsed) search frame at the top of the page
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     * @param bool $bSearchFormOpen
     */
    public function __construct($sMenuId, $sOQL, $iParentIndex, $fRank = 0.0, $bSearch = false, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null, $bSearchFormOpen = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sPageTitle = "Menu:$sMenuId+";
        $this->sOQL = $sOQL;
        $this->bSearch = $bSearch;
        $this->bSearchFormOpen = $bSearchFormOpen;
        $this->m_aParams = array();
        $this->aReflectionProperties['oql'] = $sOQL;
        $this->aReflectionProperties['do_search'] = $bSearch;
        // Enhancement: we could set as the "enable" condition that the user has enough rights to "read" the objects
        // of the class specified by the OQL...
    }

    /**
     * Set some extra parameters to be passed to the display block to fine tune its appearence
     * @param Hash $aParams paramCode => value. See DisplayBlock::GetDisplay for the meaning of the parameters
     */
    public function SetParameters($aParams) {
        $this->m_aParams = $aParams;
        foreach ($aParams as $sKey => $value) {
            $this->aReflectionProperties[$sKey] = $value;
        }
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     * @return mixed|void
     * @throws CoreException
     * @throws DictExceptionMissingString
     * @throws OQLException
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        ApplicationMenu::CheckMenuIdEnabled($this->GetMenuId());
        OQLMenuNode::RenderOQLSearch
                (
                $this->sOQL, Dict::S($this->sPageTitle), 'Menu_' . $this->GetMenuId(), $this->bSearch, // Search pane
                $this->bSearchFormOpen, // Search open
                $oPage, array_merge($this->m_aParams, $aExtraParams), true
        );
    }

    /**
     * @param $sOql
     * @param $sTitle
     * @param $sUsageId
     * @param $bSearchPane
     * @param $bSearchOpen
     * @param WebPage $oPage
     * @param array $aExtraParams
     * @param bool $bEnableBreadcrumb
     * @throws CoreException
     * @throws DictExceptionMissingString
     * @throws OQLException
     */
    public static function RenderOQLSearch($sOql, $sTitle, $sUsageId, $bSearchPane, $bSearchOpen, WebPage $oPage, $aExtraParams = array(), $bEnableBreadcrumb = false) {
        $sUsageId = utils::GetSafeId($sUsageId);
        $oSearch = DBObjectSearch::FromOQL($sOql);
        $sIcon = MetaModel::GetClassIcon($oSearch->GetClass());

        if ($bSearchPane) {
            $aParams = array_merge(array('open' => $bSearchOpen, 'table_id' => $sUsageId), $aExtraParams);
            $oBlock = new DisplayBlock($oSearch, 'search', false /* Asynchronous */, $aParams);
            $oBlock->Display($oPage, 0);
        }
		/******* Edited By Priya ********/
		$lTitle = Dict::S($sTitle);
        if(isset($_GET['c']['menu']) && $_GET['c']['menu']=='Group')
        {
            $lTitle = "Group of Elements";
        }
        $oPage->add("<p class=\"page-header\">$sIcon " .  $lTitle . "</p>");
		
		/************* End **************/
         if($sUsageId == "Menu_Organization"){
            $sTitle = 'All Departments';
        }

        //$oPage->add("<p class=\"page-header\">$sIcon " . Dict::S($sTitle) . "</p>");

        $aParams = array_merge(array('table_id' => $sUsageId), $aExtraParams);
        $oBlock = new DisplayBlock($oSearch, 'list', false /* Asynchronous */, $aParams);
        $oBlock->Display($oPage, $sUsageId);

        if ($bEnableBreadcrumb && ($oPage instanceof nt3WebPage)) {
            // Breadcrumb
            //$iCount = $oBlock->GetDisplayedCount();
            $sPageId = "ui-search-" . $oSearch->GetClass();
            $sLabel = MetaModel::GetName($oSearch->GetClass());
            $oPage->SetBreadCrumbEntry($sPageId, $sLabel, $sTitle, '', '../images/breadcrumb-search.png');
        }
    }

}

/**
 * This class defines a menu item that displays a search form for the given class of objects
 */
class SearchMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sPageTitle;

    /**
     * @var string
     */
    protected $sClass;

    /**
     * Create a menu item based on an OQL query and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sClass The class of objects to search for
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param bool $bSearch (not used)
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $sClass, $iParentIndex, $fRank = 0.0, $bSearch = false, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sPageTitle = "Menu:$sMenuId+";
        $this->sClass = $sClass;
        $this->aReflectionProperties['class'] = $sClass;
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     * @return mixed|void
     * @throws DictExceptionMissingString
     * @throws Exception
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        ApplicationMenu::CheckMenuIdEnabled($this->GetMenuId());
        $oPage->SetBreadCrumbEntry("menu-" . $this->sMenuId, $this->GetTitle(), '', '', utils::GetAbsoluteUrlAppRoot() . 'images/search.png');

        $oSearch = new DBObjectSearch($this->sClass);
        $aParams = array_merge(array('table_id' => 'Menu_' . utils::GetSafeId($this->GetMenuId())), $aExtraParams);
        $oBlock = new DisplayBlock($oSearch, 'search', false /* Asynchronous */, $aParams);
        $oBlock->Display($oPage, 0);
    }

}

/**
 * This class defines a menu that points to any web page. It takes only two parameters:
 * - The hyperlink to point to
 * - The name of the menu
 * Note: the parameter menu=xxx (where xxx is the id of the menu itself) will be added to the hyperlink
 * in order to make it the active one, if the target page is based on NT3WebPage and therefore displays the menu
 */
class WebPageMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sHyperlink;

    /**
     * Create a menu item that points to any web page (not only UI.php)
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sHyperlink URL to the page to load. Use relative URL if you want to keep the application portable !
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $sHyperlink, $iParentIndex, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sHyperlink = $sHyperlink;
        $this->aReflectionProperties['url'] = $sHyperlink;
    }

    /**
     * @param array $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        $aExtraParams['c[menu]'] = $this->GetMenuId();
        return $this->AddParams($this->sHyperlink, $aExtraParams);
    }

    /**
     * @param WebPage $oPage
     * @param array $aExtraParams
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        assert(false); // Shall never be called, the external web page will handle the display by itself
    }

}

/**
 * This class defines a menu that points to the page for creating a new object of the specified class.
 * It take only one parameter: the name of the class
 * Note: the parameter menu=xxx (where xxx is the id of the menu itself) will be added to the hyperlink
 * in order to make it the active one
 */
class NewObjectMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sClass;

    /**
     * Create a menu item that points to the URL for creating a new object, the menu will be added only if the current user has enough
     * rights to create such an object (or an object of a child class)
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sClass URL to the page to load. Use relative URL if you want to keep the application portable !
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass
     * @param int|null $iActionCode
     * @param int $iAllowedResults
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $sClass, $iParentIndex, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sClass = $sClass;
        $this->aReflectionProperties['class'] = $sClass;
    }

    /**
     * @param string[] $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        $sHyperlink = utils::GetAbsoluteUrlAppRoot() . 'pages/UI.php?operation=new&class=' . $this->sClass;
        $aExtraParams['c[menu]'] = $this->GetMenuId();
        return $this->AddParams($sHyperlink, $aExtraParams);
    }

    /**
     * Overload the check of the "enable" state of this menu to take into account
     * derived classes of objects
     * @throws CoreException
     */
    public function IsEnabled() {
        // Enable this menu, only if the current user has enough rights to create such an object, or an object of
        // any child class

        $aSubClasses = MetaModel::EnumChildClasses($this->sClass, ENUM_CHILD_CLASSES_ALL); // Including the specified class itself
        $bActionIsAllowed = false;

        foreach ($aSubClasses as $sCandidateClass) {
            if (!MetaModel::IsAbstract($sCandidateClass) && (UserRights::IsActionAllowed($sCandidateClass, UR_ACTION_MODIFY) == UR_ALLOWED_YES)) {
                $bActionIsAllowed = true;
                break; // Enough for now
            }
        }
        return $bActionIsAllowed;
    }

    /**
     * @param WebPage $oPage
     * @param string[] $aExtraParams
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        assert(false); // Shall never be called, the external web page will handle the display by itself
    }

}

require_once(APPROOT . 'application/dashboard.class.inc.php');

/**
 * This class defines a menu item which content is based on XML dashboard.
 */
class DashboardMenuNode extends MenuNode {

    /**
     * @var string
     */
    protected $sDashboardFile;

    /**
     * Create a menu item based on a custom template and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param string $sDashboardFile
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $sDashboardFile, $iParentIndex, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->sDashboardFile = $sDashboardFile;
        $this->aReflectionProperties['definition_file'] = $sDashboardFile;
    }

    /**
     * @param string[] $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        if ($this->sDashboardFile == '')
            return '';
        return parent::GetHyperlink($aExtraParams);
    }

    /**
     * @return null|RuntimeDashboard
     * @throws CoreException
     * @throws Exception
     */
    public function GetDashboard() {
        $sDashboardDefinition = @file_get_contents($this->sDashboardFile);
        if ($sDashboardDefinition !== false) {
            $bCustomized = false;

            // Search for an eventual user defined dashboard, overloading the existing one
            $oUDSearch = new DBObjectSearch('UserDashboard');
            $oUDSearch->AddCondition('user_id', UserRights::GetUserId(), '=');
            $oUDSearch->AddCondition('menu_code', $this->sMenuId, '=');
            $oUDSet = new DBObjectSet($oUDSearch);
            if ($oUDSet->Count() > 0) {
                // Assuming there is at most one couple {user, menu}!
                $oUserDashboard = $oUDSet->Fetch();
                $sDashboardDefinition = $oUserDashboard->Get('contents');
                $bCustomized = true;
            }
            $oDashboard = new RuntimeDashboard($this->sMenuId);
            $oDashboard->FromXml($sDashboardDefinition);
            $oDashboard->SetCustomFlag($bCustomized);
        } else {
            $oDashboard = null;
        }
        return $oDashboard;
    }

    /**
     * @param WebPage $oPage
     * @param string[] $aExtraParams
     * @throws CoreException
     * @throws Exception
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        ApplicationMenu::CheckMenuIdEnabled($this->GetMenuId());
        $oDashboard = $this->GetDashboard();
        if ($oDashboard != null) {
            $sDivId = preg_replace('/[^a-zA-Z0-9_]/', '', $this->sMenuId);
            $oPage->add('<div class="dashboard_contents" id="' . $sDivId . '">');

       /*********** Edited By Nilesh for Overview dropdown Add on screen ******************/

           if($this->GetMenuId()=="Incident:Overview" || $this->GetMenuId()=="Problem:Overview" || $this->GetMenuId()=="Change:Overview"){

            
            $dayVal = isset($_COOKIE['new_days'])? $_COOKIE['new_days']:"";

            switch ($_SESSION['language']) {
                case 'PT BR': $placeh = 'Digite o número de dias'; break;
                default: $placeh = 'Enter number of days'; break;
            }
			switch ($_SESSION['language']) {
                case 'PT BR': $DownloadPDF = 'baixar PDF'; break;
                default: $DownloadPDF = 'Download PDF'; break;
            }
			switch ($_SESSION['language']) {
                case 'PT BR': $Go = 'Vai'; break;
                default: $Go = 'Go'; break;
            }
            $oPage->add('<div style="float:right">
                <button class=\'action generate\' style=\'margin: 6px;cursor:pointer;\' onclick="generate()">'.$DownloadPDF.'</button>
                <div class=\'dialog previewMain\' style=\'display:none\'><div class=\'preview\' id=\'previewMain\'></div></div>
                <input type="number" name="days" id="days" min="1" placeholder="'.$placeh.'" value="'.$dayVal.'">
                <button class="btn btn-default" id="gotoDaySerach" style="padding: 5px 20px 4px 20px;border-radius: 2px;color: #ffffff;background-color: #F17422;border: 1px solid #F17422;cursor: pointer;">'.$Go.'</button>
                </div>');
            /*if(!isset($_COOKIE['pre_days'])){
               setcookie('pre_days', 20, time() + (60 * 1000), "/");
            }*/

            //$preDay = $_COOKIE['pre_days'];
            $preDay = 20;
           
		   $oPage->add_ready_script('$("ul li#AccordionMenu_Organization a").html("Area")');
            $oPage->add_ready_script(
<<<EOF

        var days = document.getElementById('days');

        days.onkeydown = function(e) {
            if(!((e.keyCode > 95 && e.keyCode < 106)
              || (e.keyCode > 47 && e.keyCode < 58) 
              || e.keyCode == 8)) {
                return false;
            }
        }
        
        $("#gotoDaySerach").on('click',function(){            
            var newDay = $("#days").val();
            console.log(newDay);
            $.ajax({
                url: "addSiteAttr.php",
                data: {"attr":"overview","preDay":$preDay,"newDay":newDay,"sessAction":"set"},
                type: "POST",
                dataType: "json",
                success: function(res){
                    console.log(res);
                    if(res.flag){
                        location.reload();
                    }
                }
            });
        });

        $("#gotoDayClear").on('click',function(){           
            $.ajax({
                url: "addSiteAttr.php",
                data: {"attr":"overview","preSess":"20","sessAction":"clear"},
                type: "POST",
                dataType: "json",
                success: function(res){
                    console.log(res);
                    //document.location = url;
                }
            });
        });
EOF
);
        }
            

        /*********** EOF Edited By Nilesh for Overview dropdown Add on screen **************/
            $oDashboard->Render($oPage, false, $aExtraParams);
            $oPage->add('</div>');
            $oDashboard->RenderEditionTools($oPage);

            if ($oDashboard->GetAutoReload()) {
                $sId = $this->sMenuId;
                $sExtraParams = json_encode($aExtraParams);
                $iReloadInterval = 1000 * $oDashboard->GetAutoReloadInterval();
                $oPage->add_script(
                        <<<EOF
					setInterval("ReloadDashboard('$sDivId');", $iReloadInterval);

					function ReloadDashboard(sDivId)
					{
						var oExtraParams = $sExtraParams;
						// Do not reload when a dialog box is active
						if (!($('.ui-dialog:visible').length > 0))
						{
							$('.dashboard_contents#'+sDivId).block();
							$.post(GetAbsoluteUrlAppRoot()+'pages/ajax.render.php',
							   { operation: 'reload_dashboard', dashboard_id: '$sId', extra_params: oExtraParams},
							   function(data){
								 $('.dashboard_contents#'+sDivId).html(data);
								 $('.dashboard_contents#'+sDivId).unblock();
								}
							 );
						}
					}
EOF
                );
            }

            $bEdit = utils::ReadParam('edit', false);
            if ($bEdit) {
                $sId = addslashes($this->sMenuId);
                $oPage->add_ready_script("EditDashboard('$sId');");
            } else {
                $oParentMenu = ApplicationMenu::GetMenuNode($this->iParentIndex);
                $sParentTitle = $oParentMenu->GetTitle();
                $sThisTitle = $this->GetTitle();
                if ($sParentTitle != $sThisTitle) {
                    $sDescription = $sParentTitle . ' / ' . $sThisTitle;
                } else {
                    $sDescription = $sThisTitle;
                }
                if ($this->sMenuId == ApplicationMenu::GetDefaultMenuId()) {
                    $sIcon = '../images/breadcrumb_home.png';
                } else {
                    $sIcon = '../images/breadcrumb-dashboard.png';
                }
                $oPage->SetBreadCrumbEntry("ui-dashboard-" . $this->sMenuId, $this->GetTitle(), $sDescription, '', $sIcon);
            }
        } else {
            $oPage->p("Error: failed to load dashboard file: '{$this->sDashboardFile}'");
        }
    }

    /**
     * @param WebPage $oPage
     * @throws CoreException
     * @throws Exception
     */
    public function RenderEditor(WebPage $oPage) {
        $oDashboard = $this->GetDashboard();
        if ($oDashboard != null) {
            $oDashboard->RenderEditor($oPage);
        } else {
            $oPage->p("Error: failed to load dashboard file: '{$this->sDashboardFile}'");
        }
    }

    /**
     * @param $oDashlet
     * @throws Exception
     */
    public function AddDashlet($oDashlet) {
        $oDashboard = $this->GetDashboard();
        if ($oDashboard != null) {
            $oDashboard->AddDashlet($oDashlet);
            $oDashboard->Save();
        } else {
            throw new Exception("Error: failed to load dashboard file: '{$this->sDashboardFile}'");
        }
    }

}

/**
 * A shortcut container is the preferred destination of newly created shortcuts
 */
class ShortcutContainerMenuNode extends MenuNode {

    /**
     * @param string[] $aExtraParams
     * @return string
     */
    public function GetHyperlink($aExtraParams) {
        return '';
    }

    /**
     * @param WebPage $oPage
     * @param string[] $aExtraParams
     * @return mixed|void
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        
    }

    /**
     * @throws CoreException
     * @throws Exception
     */
    public function PopulateChildMenus() {
        // Load user shortcuts in DB
        //
		$oBMSearch = new DBObjectSearch('Shortcut');
        $oBMSearch->AddCondition('user_id', UserRights::GetUserId(), '=');
        $oBMSet = new DBObjectSet($oBMSearch, array('friendlyname' => true)); // ascending on friendlyname
        $fRank = 1;
        while ($oShortcut = $oBMSet->Fetch()) {
            $sName = $this->GetMenuId() . '_' . $oShortcut->GetKey();
            new ShortcutMenuNode($sName, $oShortcut, $this->GetIndex(), $fRank++);
        }

        // Complete the tree
        //
		parent::PopulateChildMenus();
    }

}

require_once(APPROOT . 'application/shortcut.class.inc.php');

/**
 * This class defines a menu item which content is a shortcut.
 */
class ShortcutMenuNode extends MenuNode {

    /**
     * @var Shortcut
     */
    protected $oShortcut;

    /**
     * Create a menu item based on a custom template and inserts it into the application's main menu
     * @param string $sMenuId Unique identifier of the menu (used to identify the menu for bookmarking, and for getting the labels from the dictionary)
     * @param object $oShortcut Shortcut object
     * @param integer $iParentIndex ID of the parent menu
     * @param float $fRank Number used to order the list, any number will do, but for a given level (i.e same parent) all menus are sorted based on this value
     * @param string $sEnableClass Name of class of object
     * @param integer $iActionCode Either UR_ACTION_READ, UR_ACTION_MODIFY, UR_ACTION_DELETE, UR_ACTION_BULKREAD, UR_ACTION_BULKMODIFY or UR_ACTION_BULKDELETE
     * @param integer $iAllowedResults Expected "rights" for the action: either UR_ALLOWED_YES, UR_ALLOWED_NO, UR_ALLOWED_DEPENDS or a mix of them...
     * @param string $sEnableStimulus
     */
    public function __construct($sMenuId, $oShortcut, $iParentIndex, $fRank = 0.0, $sEnableClass = null, $iActionCode = null, $iAllowedResults = UR_ALLOWED_YES, $sEnableStimulus = null) {
        parent::__construct($sMenuId, $iParentIndex, $fRank, $sEnableClass, $iActionCode, $iAllowedResults, $sEnableStimulus);
        $this->oShortcut = $oShortcut;
        $this->aReflectionProperties['shortcut'] = $oShortcut->GetKey();
    }

    /**
     * @param string[] $aExtraParams
     * @return string
     * @throws CoreException
     */
    public function GetHyperlink($aExtraParams) {
        $aContext = array();
        $sContext = $this->oShortcut->Get('context');
        $aContext = unserialize($sContext);
        if (isset($aContext['menu'])) {
            unset($aContext['menu']);
        }
        if(!empty($aContext)){
           foreach ($aContext as $sArgName => $sArgValue) {
                $aExtraParams[$sArgName] = $sArgValue;
            } 
        }
        return parent::GetHyperlink($aExtraParams);
    }

    /**
     * @param WebPage $oPage
     * @param string[] $aExtraParams
     * @return mixed|void
     * @throws DictExceptionMissingString
     */
    public function RenderContent(WebPage $oPage, $aExtraParams = array()) {
        ApplicationMenu::CheckMenuIdEnabled($this->GetMenuId());
        $this->oShortcut->RenderContent($oPage, $aExtraParams);
    }

    /**
     * @return string
     * @throws CoreException
     */
    public function GetTitle() {
        return $this->oShortcut->Get('name');
    }

    /**
     * @return string
     * @throws CoreException
     */
    public function GetLabel() {
        return $this->oShortcut->Get('name');
    }

}
