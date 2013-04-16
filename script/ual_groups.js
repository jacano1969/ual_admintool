
var ual_groups = ual_groups || (function(){
	
	"use strict";
    
    $(document).ready(function(){
        // load group members
        $('#group').live("change", function() {
           
            var groupId = $(this).children(":selected").attr("id");
            
			$('#groupmembers').hide();
			
            if(groupId!=0) {
                $('#loading').html('<h1>Loading, please wait ...</h1>');
               
                // load group members
                $.get('group.php?groupId='+groupId, function(data){
                    $('#groupmembers').hide();
                    $('#groupmembers').html(data);
                        
                    // get users already in group
                    $.get('groupmembers.php?groupId='+groupId, function(usernames){

						if(usernames) {
							// loop through members of this group
							$.each(usernames, function(index, row) {

								// check through list of users to add
								$('#userstsms').children().each(function() {
										 
									// is this user a group member?
									if($(this).val()==row) {
										
										// add user to group members
										$(this).remove().appendTo('#users');
										
										// break out - no need to keep searching this time
										return;
									}
								});
							});
						}
						
                        $('#groupmembers').show();
                        $('#loading').html('');
                    }, "json");
                });
            } else {
                $('#groupmembers').hide();
            }
        });
		
        // cancel button clicked
        $('#cancel').live("click", function() {
            // show the home screen
            window.location.href='index.php';
        });
    }); // end of document ready
}()); // closure

