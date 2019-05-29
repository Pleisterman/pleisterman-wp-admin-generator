<?php
/*
        @package    pleisterman/pleisterman-wp-admin-generator
  
        file:       ProjectTab.php
        function:   handles: 
                        projectsTab
*/

namespace PleistermanWpAdminGenerator\Admin;

use PleistermanWpAdminGenerator\Common\Common;
use PleistermanWpAdminGenerator\Admin\Base\CommonClass;
use PleistermanWpAdminGenerator\Admin\Page;
use PleistermanWpAdminGenerator\Admin\Projects\Projects;


class ProjectsTab extends CommonClass {
    
    // members
    private $pageDir = null;
    private $page = null;
    private $pageId = null;
    private $tabId = null;
    private $slug = null;
    private $projects = null;
    private $projectsTabs = array();
    private $scriptLoader = null;
    private $styleSheetLoader = null;
    private $styleSheetsDir = '';
    private $styleSheetsUrl = '';
    private $scriptsDir = '';
    private $scriptsUrl = '';
    // members

    // construct
    public function __construct( Common $common ){
    
        // call parent constructor
        parent::__construct( $common );

    }
    // construct

    // set loaders
    public function setLoaders( $scriptLoader, $scriptsDir, $scriptsUrl, $styleSheetLoader, $styleSheetsDir, $styleSheetsUrl ){
        
        // set script loader
        $this->scriptLoader = $scriptLoader;
        
        // set stylesheet loader
        $this->styleSheetLoader = $styleSheetLoader;

        // set script dir
        $this->scriptsDir = $scriptsDir;

        // set script url
        $this->scriptsUrl = $scriptsUrl;

        // set styleSheets dir
        $this->styleSheetsDir = $styleSheetsDir;

        // set styleSheets url
        $this->styleSheetsUrl = $styleSheetsUrl;
        
        // create projects
        $this->projects = new Projects( $this->common, $this->scriptLoader, $this->styleSheetLoader );
        
        // set projects loaders
        $this->projects->setLoaders( $scriptLoader, $styleSheetLoader );
        
    }
    // set loaders
    
    // set page
    public function setPage( $pageDir, Page $page ){
        // set page dir
        $this->pageDir = $pageDir;
        // set page
        $this->page = $page;
        // call projects
        $this->projects->setPage( $page );
    }
    // set page
        
    // register wordpress hooks
    public function registerWordpressHooks( ) {
        // call projects
        $this->projects->registerWordpressHooks();
    }
    // register wordpress hooks
    
    // add wordpress actions
    public function addWordpressActions( ) {
        // call projects
        $this->projects->addWordpressActions();
        
    }
    // add wordpress actions
    
    // add style sheets
    public function addStyleSheets() {
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects
            $this->projects->addStyleSheets();
            // no handling needed
            return;
        }
        // request needs handling
        
        // debug 
        $this->common->debug( 'projects-tab', 'projects tab add stylesheets' );
        
        // call style sheet loader
        $this->styleSheetLoader->add( $this->styleSheetsDir, $this->styleSheetsUrl, array( 'projects-tab.json' ) );
        
    }	
    // add style sheets
    
    // add javascripts
    public function addJavascripts() {
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects
            $this->projects->addJavascripts();
            // no handling needed
            return;
        }
        // request needs handling
        
        // debug 
        $this->common->debug( 'projects-tab', 'projects tab add javascripts' );
        
        // call script loader
        $this->scriptLoader->add( $this->scriptsDir, $this->scriptsUrl, array( 'projects-tab.json' ) );

    }	
    // add javascripts

    // create menu
    public function createMenu( $slug, $pageId, $tabId ) {
        
        // set members 
        $this->slug = $slug;
        $this->pageId = $pageId;
        $this->tabId = $tabId;
        // set members 

        // call projects
        $this->projects->createMenu( $this->slug, $this->pageId, $this->tabId );
        
    }	
    // create menu
    
    // create load translations
    public function loadTranslations() {
        
        // call projects
        $this->projects->loadTranslations();
        
    }	
    // create load translations
    
    // admin init
    public function adminInit( $capabilities ) {

        // debug 
        $this->common->debug( 'admin-init', 'admin-init-projects-tab', 'before' );
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // debug 
            $this->common->debug( 'admin-init', 'admin-init-projects-tab request not handled', 'after' );
            // call projects
            $this->projects->adminInit();
            // no handling needed
            return;
        }
        // request needs handling

        // get project tabs
        $this->getProjectTabs();
        
        // load page
        $this->page->load( $this->pageDir, $this->slug, $this->tabId );
        
        // register page
        $this->page->register( $capabilities );

        // debug 
        $this->common->debug( 'admin-init', 'admin-init-projects-tab request handled', 'after' );
        
    }
    // admin init
    
    // get project tabs
    private function getProjectTabs( ){
        
        // get file name
        $dirFileName = $this->pageDir . $this->slug . '.json';
        
        // json exists
        if( !is_file( $dirFileName ) ){
            
            // debug error
            $this->common->debug( 'error', 'project file exists ' );
            // done
            return [];
            
        }
        // json exists
        
        // get page settings
        $pageSettings = json_decode( file_get_contents( $dirFileName ), true );
        
        // get tabs dir
        $projectsTabDir = $this->pageDir . $pageSettings['tabs'] . '\\';
        
        // get file list from tabs dir
        $tabsDirList = scandir( $projectsTabDir );
        
        // loop over tabsdir list
        for( $i = 0; $i < count( $tabsDirList ); $i++ ){

            // ! dir
            if( !is_dir( $projectsTabDir . $tabsDirList[$i] ) ){
    
                // get file name
                array_push( $this->projectsTabs, basename( $tabsDirList[$i], '.json' ) );
            }
            // done ! dir
        }
        // done loop over tabsdir list
               
    }
    // get project tabs    
    
    // open page called by Wordpress when a menu is selected
    public function openPage( $listsDir, $imageUrl ){
        
        // debug 
        $this->common->debug( 'open-page', 'admin-init-projects-tab open page', 'before' );
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects
            $this->projects->openPage();
            // debug 
            $this->common->debug( 'open-page', 'admin-init-projects-tab request not handled', 'after' );
            // no handling needed
            return;
        }
        // request needs handling
        
        // no tabs found
        if( count( $this->projectsTabs ) == 0 ){
            // debug 
            $this->common->debug( 'error', 'admin-init-projects-tab tabs not found' );
            // no handling needed
            return;
        }
        // no tabs found
        
        // create before elements
        $beforeFormElements = null;
        // debug 
        $this->common->debug( 'open-page', 'admin-init-projects-tab tabId: ' . $this->tabId );
        
        // is first tab
        if( empty( $this->tabId ) || $this->tabId == $this->projectsTabs[0] ){
            // get before elements
            $beforeFormElements = $this->getBeforeFormElements();
        }
        // is first tab
        
        // open page
        $this->page->open( $listsDir, $imageUrl, $beforeFormElements );
        
        // debug 
        $this->common->debug( 'open-page', PHP_EOL . 'admin-init-projects-tab request handled', 'after' );
    } 
    // open page called by Wordpress when a menu is selected
    
    // request needs handling
    private function getBeforeFormElements() {
        
        // is first tab
        
        // get text domain
        $textDomain = $this->common->getSetting( 'text-domain' ); 
        
        // create count text
        $countText = count( $this->projectsTabs ) - 1;
        
        // count project tabs is 0
        if( count( $this->projectsTabs ) == 0 ){
            // set count text
            $countText = __( 'no', $textDomain );
        }
        // count project tabs is 0
        
        // create projects text
        $projectsText = __( 'project', $textDomain );
        
        // count project tabs is ! 1
        if( count( $this->projectsTabs ) != 1 ){
            // add plural
            $projectsText = __( 'projects', $textDomain );
        }
        // count project tabs is ! 1
        
        // create projects
        $projects = array();
        
        // loop over project tabs
        for( $i = 0; $i < count( $this->projectsTabs ); $i++ ){
            // not the first
            if( $i != 0 ){
                // add projects tab
                array_push( $projects, substr( $this->projectsTabs[$i], 2 ) );
            }
            // not the first
        }
        // loop over project tabs
        
        
        // create before form elements
        $beforeformElements = array (
            array (
                "element" =>  "div",
                "text"  =>  __( "Welcome to your projects.", $textDomain ),
                "class" =>  "project-tab-title"
            ),
            array (
                "element" =>  "div",
                "text"  =>  __( "You have ", $textDomain ) . $countText . ' ' .$projectsText . '.',
                "class" =>  "project-tab-title"
            ),
            array (
                "element" =>  "input",
                "type" => "hidden",
                "id" => "existingProjects",
                "value"  =>  implode( ',', $projects ),
                "class" =>  "project-tab-title"
            )
        );
        
        // return before form elements
        return $beforeformElements;
    } 
    // open page called by Wordpress when a menu is selected
        
    
    // request needs handling
    private function requestNeedsHandling() {
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // app-name-projects in pageid
        if( strstr( $this->pageId, $appName . '-projects' ) ){
            // debug 
            $this->common->debug( 'request-needs-handling', 'is a projects page' );
            // is projects page
            return true;
        }
        // app-name-projects in pageId
            
        // debug 
        $this->common->debug( 'request-needs-handling', 'not a projects page' );
        // not a generator page
        return false;
        
    }
    // request needs handling
    
    // ajax
    public function ajax() {
        
        // call projects
        $this->projects->ajax();
        
    }
    // ajax
    
    // activate
    public function activate() {
        
        // call projects
        $this->projects->activate();
        
    }
    // activate

    // deActivate
    public function deActivate() {
        
        // call projects
        $this->projects->deActivate();
        
    }
    // deActivate
    
}
