<?php
/*
        @package    pleisterman/pleisterman-wp-admin-generator
  
        file:       Menu.php
        function:   handles: 
                        reads menu settings from json
                        creates the admin menu
*/


namespace PleistermanWpAdminGenerator\Admin;

use PleistermanWpAdminGenerator\Common\Common;
use PleistermanWpAdminGenerator\Admin\Base\CommonClass;
use PleistermanWpAdminGenerator\Admin\Main;
use PleistermanWpAdminGenerator\Admin\Authorisation;

class Menu extends CommonClass {
    // members
    private $dashboard = null;
    private $authorisation = null;
    private $menuJsonDirFileName = '/../menus/menus.json';
    private $menuJson = null;
    private $pages = array();
    // members
    
    // construct
    public function __construct( Common $common, Main $dashboard, $openPageCallback ){
       
        // call parent constructor
        parent::__construct( $common );

        // set dashboard
        $this->dashboard = $dashboard;
        
        // create authorisation class
        $this->authorisation = new Authorisation( $this->common ); 
            
        // remember open page callback
        $this->openPageCallback = $openPageCallback;

        // read menu
        $this->readMenu();

    }
    // construct
    
    // read menu
    private function readMenu(  ){

        // ! show admin menu
        if( !$this->common->getSetting( 'show-admin-menu' ) ){
            // done
            return;
        }
        // ! show admin menu
        
        // create file name
        $fileName = dirname( __FILE__ ) . $this->menuJsonDirFileName;
        
        // file ! exists
        if( !is_file( $fileName ) ){

            // debug 
            $this->common->debug( 'error', 'menu file not found : ' . $fileName );

            // return with error
            return;
        }
        // file ! exists
        
        // read menu json
        $this->menuJson = json_decode( file_get_contents( $fileName ), true );
       
    }
    // read menu
    
    // create
    public function create(  ){
        
        // menu json !exists
        if( !$this->menuJson ){
            // done with error
            return;
        }
        // menu json !exists

        // get top menu capabilities
        $topMenuCapabilities = $this->menuJson['top-menu']['capabilities'];
        // is authorised for top menu
        if( $this->authorisation->isAuthorised( $topMenuCapabilities ) ){
            // add top menu
            $this->addTopMenu( );
            // add sub menus
            $this->addSubMenus( );
        }
        // is authorised for top menu       
    }
    // create

    // get menu pages
    public function getMenuPages( ){
     
        // create pages
        $pages = array();
        
        // add top menu page
        array_push( $pages, $this->menuJson['top-menu']['slug'] );
        
        // loop over submenus
        foreach ( $this->menuJson['sub-menus'] as $key => $subMenu ){
            // add sub menu page
            array_push( $pages, $subMenu['slug'] );
        } 
        // loop over submenus
        
        // remove duplicates
        $pages = array_keys( array_flip( $pages ) );

        // return pages
        return $pages;
    }
    // get menu pages
    
    // get slug
    public function getSlug( $pageId ) {
        
        // debug 
        $this->common->debug( 'menu', 'menu-get slug ' . $pageId );        
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // create menu
        $menu = null;
        
        // slug is top menu slug
        if( $pageId == $appName . '-' . $this->menuJson['top-menu']['slug'] ){
            // set menu
            $menu = $this->menuJson['top-menu'];
        }
        // slug is top menu slug
        
        // loop over submenus
        foreach ( $this->menuJson['sub-menus'] as $key => $subMenu ){
                       
            // pageId is sub menu slug
            if( $pageId == $appName . '-' . $subMenu['slug'] ){
                
                // set menu
                $menu = $subMenu;
            }
            // pageId is top menu slug
            
        } 
        // loop over submenus
                
        // debug 
        $this->common->debug( 'menu', 'menu-found slug ' . $menu['slug'] );
        
        // return page info
        return $menu['slug'];
    }
    // get slug
    
    // get capabilities
    public function getCapabilties( $pageId ) {
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // create menu
        $menu = null;
        
        // pageId is top menu slug
        if( $pageId == $appName . '-' . $this->menuJson['top-menu']['slug'] ){
            // set menu
            $menu = $this->menuJson['top-menu'];
        }
        // pageId is top menu slug
        
        // loop over submenus
        foreach ( $this->menuJson['sub-menus'] as $key => $subMenu ){
                       
            // pageId is sub menu slug
            if( $pageId == $appName . '-' . $subMenu['slug'] ){
                
                // set menu
                $menu = $subMenu;
            }
            // pageId is top menu slug
            
        } 
        // loop over submenus
        
        // return cpabilities
        return $menu['capabilities'];
    }
    // get capabilities
    
    // add top menu
    private function addTopMenu( ){
        
        // get top menu
        $topMenu = $this->menuJson['top-menu'];
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // add top menu
        add_menu_page(  __( $topMenu['page-title'], $this->common->getSetting( 'text-domain' ) ), 
                        __( $topMenu['menu-title'], $this->common->getSetting( 'text-domain' ) ), 
                        $topMenu['capabilities'], 
                        $appName . '-' . $topMenu['slug'], 
                        array( $this->dashboard, $this->openPageCallback ), 
                        $topMenu['icon'], 
                        $topMenu['position'] );	
        
    } 
    // add top menu
    
    // add sub menus
    private function addSubMenus( ){
        
        // get app name
        $appName = $this->common->getSetting( 'app-name' );
        
        // loop over sub menu options
        foreach ( $this->menuJson['sub-menus'] as $key => $options ){
            // is authorised for sub menu
            if( $this->authorisation->isAuthorised( $options['capabilities'] ) ){
                // add sub menu
                add_submenu_page(   $appName . '-' . $options['parent-slug'], 
                                    __( $options['page-title'], $this->common->getSetting( 'text-domain' ) ), 
                                    __( $options['menu-title'], $this->common->getSetting( 'text-domain' ) ), 
                                    $options['capabilities'], 
                                    $appName . '-' . $options['slug'], 
                                    array( $this->dashboard, $this->openPageCallback ) );
            }
            // is authorised for sub menu
        }
        // done loop over sub menu options
    } 
    // add sub menus
    
}
