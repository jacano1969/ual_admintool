

var ual_admintool = ual_admintool || (function(){
	
	"use strict";

    $(document).ready(function(){
    
        // home page scripts
        if($('#home-page').length>0) {
            
            // programme filter change
            $('#programmes').change(function(){
                
                // get selected filter
                var selected_programme= $(this).val();
            
                $.get('filter.php?type=P&data='+selected_programme, function(data){
                    $('#mainfilters').hide();
                    // replace filters with new data
                    $('#mainfilters').html(data);
                    $('#mainfilters').show();
                });
            });
        }
    
    }); // end of document ready



}()); // closure