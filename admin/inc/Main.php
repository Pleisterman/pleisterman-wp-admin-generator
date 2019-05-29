<?php
/*
        @package    pleisterman/pleisterman-wp-admin-generator
  
        file:       dashboard.php
        function:   handles: 
                        register wordpress hooks
                        admin enqueue scripts 
                        dashboard create menu 
                        register wordpress options
                        open selected menu page
                        activate, 
                        deactivate
*/

namespace PleistermanWpAdminGenerator\Admin;

use PleistermanWpAdminGenerator\Common\Common;
use PleistermanWpAdminGenerator\Admin\Translations;
use PleistermanWpAdminGenerator\Admin\StyleSheetLoader;
use PleistermanWpAdminGenerator\Admin\ScriptLoader;
use PleistermanWpAdminGenerator\Admin\DeInstaller;
use PleistermanWpAdminGenerator\Admin\Request;
use PleistermanWpAdminGenerator\Admin\Menu;
use PleistermanWpAdminGenerator\Admin\Page;
use PleistermanWpAdminGenerator\Admin\ProjectsTab;

class Main {
    // members
    private $common = null;
    private $translations = null;
    private $adminPath = '../';
    private $menu = null;
    private $page = null;
    private $pageId = null;
    private $tabId = null;
    private $slug = null;
    private $imageUrl = '/../../images/';
    private $pageDir = '\..\pages\\';
    private $listsDir = "/../lists/";
    private $request = null;
    private $deInstaller = null;
    private $projectsTab = null;
    private $styleSheetsDir = '\..\css\\';
    private $styleSheetsUrl = '/../../css/';
    private $styleSheetLoader = null;
    private $scriptsDir = '/../js/';
    private $scriptsUrl = '/../../js/';
    private $scriptLoader = null;
    // members
    
    // construct
    public function __construct( ){
        
        // create common
        $this->common = new Common( );
        
        // create translations
        $this->translations = new Translations( $this->common );
        
        // create request
        $this->request = new Request( $this->common );
        
        // create de-Installer
        $this->deInstaller = new DeInstaller( $this->common );
        
        // set admin path
        $this->adminPath = plugin_dir_url( __FILE__ ) . $this->adminPath;
        
        // get image url
        $this->imageUrl = plugin_dir_url( __FILE__ ) . $this->imageUrl;
        
        // set javascripts dir
        $this->scriptsDir = dirname( __FILE__ ) . $this->scriptsDir; 
        
        // get javascript url
        $this->scriptsUrl = plugin_dir_url( __FILE__ ) . $this->scriptsUrl;

        // create script loader
        $this->scriptLoader = new ScriptLoader( $this->common );
        
        // set style sheets dir
        $this->styleSheetsDir = dirname( __FILE__ ) . $this->styleSheetsDir; 
        
        // get styleSheets url
        $this->styleSheetsUrl = plugin_dir_url( __FILE__ ) . $this->styleSheetsUrl;
        
        // create style sheet loader
        $this->styleSheetLoader = new StyleSheetLoader( $this->common );
        
        // create projects tab
        $this->projectsTab = new ProjectsTab( $this->common, $this->scriptLoader, $this->styleSheetLoader );
        // set projects tab loaders
        $this->projectsTab->setLoaders( $this->scriptLoader, $this->scriptsDir, $this->scriptsUrl, 
                                        $this->styleSheetLoader, $this->styleSheetsDir, $this->styleSheetsUrl );
                       
        // register wp hooks
        $this->registerWordpressHooks();
        
        // add wp actions
        $this->addWordpressActions();
        
        // create menu class
        $this->menu = new Menu( $this->common, $this, 'openPage' ); 
            
        // get page dir
        $this->pageDir = dirname( __FILE__ ) . $this->pageDir;
        
        // create page
        $this->page = new Page( $this->common );
        
        // set projects tab page
        $this->projectsTab->setPage( $this->pageDir, $this->page );
        
        // get lists dir
        $this->listsDir = dirname( __FILE__ ) . $this->listsDir;
        
    }
    // construct

    // register wordpress hooks
    private function registerWordpressHooks( ) {

        // register acivation hook
        register_activation_hook( __FILE__, array( $this, 'activate' ) );

        // register deacivation hook
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
        
        // register uninstall_hook
        register_uninstall_hook(  __FILE__, array( 'deInstall', 'deInstall' ) );
        
        // call projects
        $this->projectsTab->registerWordpressHooks();
    }
    // register wordpress hooks
    
    // add wordpress actions
    private function addWordpressActions( ) {

        // enqueue scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueueScripts' ) );

        // create dashboard menu
        add_action( 'admin_menu', array( $this, 'createMenu' ) );

        // register settings
        add_action( 'admin_init', array( $this, 'adminInit' ) );
        
        // call projects
        $this->projectsTab->addWordpressActions();
    }
    // add wordpress actions
    
    // enqueue scripts 
    public function enqueueScripts() {
        
        // add style sheets
        $this->addStyleSheets( );
        // add javascripts
        $this->addJavascripts( );
        
    }	
    // enqueue scripts 

    // add style sheets
    private function addStyleSheets() {
        
        // debug 
        $this->common->debug( 'add-css', 'admin-css-main', 'before' );
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects tab
            $this->projectsTab->addStyleSheets();
            // no handling needed
            return;
        }
        // request needs handling
        
        // call style sheet loader
        $this->styleSheetLoader->add( $this->styleSheetsDir, $this->styleSheetsUrl );
        

        // debug 
        $this->common->debug( 'add-css', 'admin-css-main', 'after' );
        
    }	
    // add style sheets
    
    // add javascripts
    private function addJavascripts() {
        
        // debug 
        $this->common->debug( 'add-js', 'admin-js-main', 'before' );

        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects tab
            $this->projectsTab->addJavascripts();
            // no handling needed
            return;
        }
        // request needs handling

        // call javascripts
        $this->scriptLoader->add( $this->scriptsDir, $this->scriptsUrl );

        // debug 
        $this->common->debug( 'add-js', 'admin-js-main', 'after' );
    }	
    // add javascripts
    
    // create menu
    public function createMenu() {

        // debug 
        $this->common->debug( 'create-menu', 'admin-menu-main', 'before' );
        
        // create translations
        $this->translations->load();

        // get page id
        $this->pageId = $this->request->getPageId();
        
        // get tab id
        $this->tabId = $this->request->getTabId();

        // create menu
        $this->menu->create(); 
        
        // get slug from menu
        $this->slug = $this->menu->getSlug( $this->pageId );
        
        // call projects tab
        $this->projectsTab->createMenu( $this->slug, $this->pageId, $this->tabId );
        
        // debug 
        $this->common->debug( 'create-menu', 'admin-menu-main', 'after' );
    }	
    // create menu
    
    // admin init
    public function adminInit() {
        
        // debug 
        $this->common->debug( 'admin-init', 'admin-init-main', 'before' );
        
        // is ajax request
        if( $this->request->isAjax() ){
            // handle ajax request
            $this->ajax();
            // done
            return;
        }
        // is ajax request
        
        // get capabilities from menu
        $capabilities = $this->menu->getCapabilties( $this->pageId );
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects
            $this->projectsTab->adminInit( $capabilities );
            // debug 
            $this->common->debug( 'admin-init', 'admin-init-main request not handled', 'after' );
            // no handling needed
            return;
        }
        // request needs handling
        
        // debug 
        $this->common->debug( 'admin-init', 'page: ' . $this->pageId );

        // load page
        $this->page->load( $this->pageDir, $this->slug, $this->tabId );
            
        // register page
        $this->page->register( $capabilities );
        
        // debug 
        $this->common->debug( 'admin-init', 'admin-init-main request handled', 'after' );
    }
    // admin init
    
    // open page called by Wordpress when a menu is selected
    public function openPage( ){
        
        // debug 
        $this->common->debug( 'open-page', 'admin-init-main open page', 'before' );
        
        // request needs handling
        if( !$this->requestNeedsHandling() ){
            // call projects tab
            $this->projectsTab->openPage( $this->listsDir, $this->imageUrl );
            // debug 
            $this->common->debug( 'open-page', 'admin-init-main request not handled', 'after' );
            // no handling needed
            return;
        }
        // request needs handling
        
        // open page
        $this->page->open( $this->listsDir, $this->imageUrl );
        
        // debug 
        $this->common->debug( 'open-page', PHP_EOL . 'admin-init-main request handled', 'after' );
        
    } 
    // open page called by Wordpress when a menu is selected
    
    // request needs handling
    private function requestNeedsHandling() {
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // app-name in pageid
        if( strstr( $this->pageId, $appName ) ){

            // app-name-projects or ap-name-project- in pageId
            if( strstr( $this->pageId, $appName . '-projects' ) || 
                strstr( $this->pageId, $appName . '-project-' ) ){
                // debug 
                $this->common->debug( 'request-needs-handling', 'not admin-generator-main, page: ' . $this->pageId );
                // is projects or project page not a generator page
                return false;
            }
            // app-name-projects or ap-name-project- in pageId
            
            // debug
            $this->common->debug( 'request-needs-handling', 'admin-generator-main page' );
            // a generator page
            return true;
        }
        // app-name in page

        // debug 
        $this->common->debug( 'request-needs-handling', 'not admin-generator-main page' );
        // not a generator page
        return false;
        
    }
    // request needs handling
    
    // ajax
    public function ajax() {
        
        // call projects tab
        $this->projectsTab->ajax();
        
    }
    // ajax

    // activate
    public function activate() {
        
        // call projects tab
        $this->projectsTab->activate();
        
    }
    // activate

    // deActivate
    public function deActivate() {
        
        // call projects tab
        $this->projectsTab->deActivate();
        
    }
    // deActivate
}
