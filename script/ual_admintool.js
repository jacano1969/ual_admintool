

var ual_admintool = ual_admintool || (function(){
	
	"use strict";

    $(document).ready(function(){
    
        // home page scripts
        if($('#home-page').length>0) {
            
            // programme filter change
            $('#programmes').change(function(){
                
                // get selected filter
                var selected_programme= $(this).val();
            
                alert(selected_programme);
            });
        }
    
    }); // end of document ready



}()); // closure