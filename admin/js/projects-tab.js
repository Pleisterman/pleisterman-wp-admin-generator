/*
 
        @package    pleisterman/pleisterman-wp-admin-generator
  
        file:       projectsTab.js
        function:   handles submit for projectTab

*/

// create function
( function( ) {

    // Function: pleisterman.projectsTab( void ) void
    
    // pleisterman object not exists
    window.pleisterman = window.pleisterman ? window.pleisterman : {};
        
    // ! window.$
    window.$ = window.$ ? window.$ : jQuery;

    // create projects tab if ! exists
    window.pleisterman.projectsTab = window.pleisterman.projectsTab ? window.pleisterman.projectsTab : {};
    
    // create self
    var self = window.pleisterman.projectsTab;
    // PRIVATE:
    
    // MEMBERS:
    // FUNCTIONS
    
    // FUNCTION: init( void ) void
    self.init = function( ) {
        
        // set focus on name
        $( '#name' ).focus();
        
        // add submit events
        self.addSubmitEvents( );
        
        console.log( 'projectsTab init' );
    };
    // DONE FUNCTION: init( void ) void
    
    // FUNCTION: addSubmitEvents( void ) void
    self.addSubmitEvents = function() {
        
        // add submit event
        $( "#submit-button" ).click( function( event ){ self.submit( event ); } );
        
        // set name events
        $( '#name' ).focus( function( event ){ $( '#error-message').html( '' ); });
        $( '#name' ).keyup( function( event ){ $( '#error-message').html( '' ); });
        // set name events
        
    };
    // DONE FUNCTION: addSubmitEvents( void ) void
    
    // FUNCTION: submit( void ) void
    self.submit = function() {
        
        // check project name
        if( !self.projectNameIsValid() ){
            // done with error
            return;
        }
        // check project name
        
        var mainContainer = $( document ).find( '.pleisterman-wp-admin-generator-main' ).first();
        var form = mainContainer.find( 'form' ).first();
        form.submit();
        
    };
    // DONE FUNCTION: submit( void ) void
    
    // FUNCTION: projectNameIsValid( void ) void
    self.projectNameIsValid = function() {
        
        if( $.trim( $( '#name').prop( 'value' ) ) === '' ){
            
            // set focus on name
            $( '#name' ).focus();
            // show error        
            $( '#error-message').html( 'name empty' );
            // return error
            return false;
        } 
        
        // return ok
        return true;
    };
    // DONE FUNCTION: projectNameIsValid( void ) void
    
    
    // DONDE FUNCTIONS
})( );
// done create function
 
// doc loaded
$( document ).ready( function( ) {
    
    // init projects tab
    window.pleisterman.projectsTab.init( );    
	
});
// doc loaded
