

var ual_admintool = ual_admintool || (function(){
	
	"use strict";

    $(document).ready(function(){
    
        // home page scripts
        if($('#home-page').length>0) {
            $('#programmes').change(function(){
                var selected_programme = $(this).id;
                
                alert(selected_programme);
            });
        }
    
    }); // end of document ready



}()); // closure