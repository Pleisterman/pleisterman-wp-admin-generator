<?php
/*
        @package    pleisterman/pleisterman-wp-admin-generator
  
        file:       Scripts.php
        function:   handles: 
                        all calls for projects
*/

namespace PleistermanWpAdminGenerator\Admin\Projects;

use PleistermanWpAdminGenerator\Common\Common;
use PleistermanWpAdminGenerator\Admin\Base\CommonClass;
use PleistermanWpAdminGenerator\Admin\Page;

class Projects extends CommonClass {
    
    // members
    private $page = null;
    private $pageId = null;
    private $tabId = null;
    private $slug = null;
    private $projectsDir = "\..\..\..\projects\\";
    private $projectsUrl = "/../../../projects/";
    private $projectsSettings = null;
    private $scriptLoader = null;
    private $styleSheetLoader = null;
    // members
        
    // construct
    public function __construct( Common $common ){
        
        // call parent constructor
        parent::__construct( $common );

        // set projects dir
        $this->projectsDir = dirname( __FILE__ ) . $this->projectsDir;

        // set projects url
        $this->projectsUrl = dirname( __FILE__ ) . $this->projectsUrl;

        // projects dir ! exists
        if( !is_dir( $this->projectsDir ) ) {

            // debug 
            $this->common->debug( 'error', 'projects dir not found projectsDir: ' . $this->projectsDir );
            
        }
        // projects dir ! exists
        
    }
    // construct

    // register wordpress hooks
    public function registerWordpressHooks( ) {

    }
    // register wordpress hooks
    
    // add wordpress actions
    public function addWordpressActions( ) {
        
    }
    // add wordpress actions
    
    // set loaders
    public function setLoaders( $scriptLoader, $styleSheetLoader ){
        
        // set script loader
        $this->scriptLoader = $scriptLoader;
        
        // set stylesheet loader
        $this->styleSheetLoader = $styleSheetLoader;
        
    }
    // set loaders
    
    // set page
    public function setPage( Page $page ){
        // set page
        $this->page = $page;
    }
    // set page
    
    // load translations
    public function loadTranslations() {
    }
    // load translations

    // add style sheets
    public function addStyleSheets() {
        
    }	
    // add style sheets


    // add javascripts
    public function addJavascripts() {
        
    }	
    // add javascripts
    
    // create menu
    public function createMenu( $slug, $pageId, $tabId ) {
        
        // set members 
        $this->slug = $slug;
        $this->pageId = $pageId;
        $this->tabId = $tabId;
        // set members 

    }	
    // create menu
    
    // initAdmin
    public function adminInit() {
    }
    // initAdmin
    
    // open page
    public function openPage( ){
    } 
    // open page
    
    // ajax
    public function ajax() {
        
    }
    // ajax
    
    // activate
    public function activate() {
        
    }
    // activate

    // deActivate
    public function deActivate() {
        
        
    }
    // deActivate
    
    // is projects page
    private function isProjectsPage( $slug, $pageId, $tabId ) {
        
        
    }
    // is projects page
    
    // add project
    private function addProject( $slug, $pageId, $tabId ){
        
        // is post
        if( $this->request->isPost() ){
            
            // is projects page
            if( $this->isProjectsPage( $slug, $pageId, $tabId ) ){
                
                // create empty project values
                $projectValues = array(
                    'project-name' => $this->common->getSetting( 'app-name' ),
                    'project-action-add' => 0                    
                );
                // create empty project values
                
                // set empty project values        
                $updated = update_option( 'pleisterman-wp-admin-generator-projects', $projectValues );
                
            }
            // is projects page
            
            // done 
            return;
        }
        // is post
        
        // is post
        if( !$this->request->isPost() ){
            // debug 
            $this->common->debug( 'save-page', 'admin init is post pageId: ' . $pageId );
            // debug 
            $this->common->debug( 'save-page', 'admin init is post slug: ' . $slug . ' tab: ' . $tabId );
            // get option
            $options = $this->common->getOption( 'pleisterman-wp-admin-generator-projects');
            // debug 
            $this->common->debug( 'save-page', 'admin init is post option value = : ' . json_encode( $options ) );
            
            add_settings_error( $pageId, $slug, 'hiero' );
        }
        // is post
        
    }
    // add project
    
    // remove updated messages
    public function removeUpdatedMessages( $messages ){
        error_log( 'messages: ' . json_encode( $messages ) );
        // return empty
        return array();
    }
    // remove updated messages
    

    // add project
}
