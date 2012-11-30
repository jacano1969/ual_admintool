

var ual_admintool = ual_admintool || (function(){
	
	"use strict";

    $(document).ready(function(){
    
        // home page scripts
        if($('#home-page').length>0) {
            
            
            //
            // Workflow
            //
            
            // show workflow
            $('#hiddenlightbox').lightbox_me({
                centered: true,
                appearEffect: 'show',
                lightboxSpeed: 'fast',
                overlaySpeed: 'fast',
                closeClick: false,
                closeEsc: false, 
                onLoad: function() { 
                    // do anything after lightbox is loaded?
                    $('#hiddenlightbox').css('width','400px');
                }
            });
            
            // workflow selected
            $('#workflows').live("change", function() {
                
                var selected_workflow_step = $("option:selected", this).attr("id");
                
                if(selected_workflow_step!="0") {
                    // get workflow sub steps
                    $.get('workflow.php?step='+selected_workflow_step, function(data){
                        $('#hiddenlightbox').hide();
                        // replace workflow with new data
                        $('#hiddenlightbox').html(data);
                        $('#hiddenlightbox').show();
                    });
                } else {
                    // show all workflows
                    $.get('workflow.php?step=false', function(data){
                        $('#hiddenlightbox').hide();
                        // replace filters with new data
                        $('#hiddenlightbox').html(data);
                        $('#hiddenlightbox').show();
                    });
                }
            });
            
            // workflow sub step selected
            $('#workflow_sub_steps').live("change", function() {
                
                var sub_step_id = $(this).children(":selected").attr("id");
                var sub_step_action = $(this).children(":selected").attr("data");
                
                // set sub step action and enable ok button (submit)
                if(sub_step_action!="0"){
                    $('#sub_step_action').val(sub_step_action);
                    $('#sub_step_id').val(sub_step_id);
                    $('#ok').removeAttr('disabled');
                } else {
                    $('#sub_step_action').val(0);
                    $('#ok').attr('disabled','disabled');
                }
            });
            
            // commence workflow action
            $('#ok').live("click", function() {
                
                // get work flow action
                var step_action = $('#step_action').val();
                var sub_step_action = $('#sub_step_action').val();
                
                // check if this is a step action
                if(typeof(step_action)!='undefined'){
                    
                    // get the step id
                    var step_id = $('#step_id').val();
                    
                    // get workflow action
                    $.get("action.php?step_id="+step_id+"&action_id="+step_action, function(data) {
                        $('div.container fieldset').hide();
                        $('div.container fieldset').html(data);
                        $('div.container fieldset').show();
                    });
                    
                    return false;
                }
                
                // check if its a sub step action
                if(typeof(sub_step_action)!='undefined'){
                    
                    // get the sub step id
                    var sub_step_id = $('#sub_step_id').val();
                    
                    // get workflow action
                    $.get("action.php?sub_step_id="+sub_step_id+"&action_id="+sub_step_action, function(data) {
                        $('div.container fieldset').hide();
                        $('div.container fieldset').html(data);
                        $('div.container fieldset').show();
                    });
                    
                    return false;
                }
                
                // something has happened we did not intend
                alert("An error has occured, please re-try.");
                
                // show the home screen (and all workflows)
                window.location.href='index.php';
                
                return false;
            });
            
            // workflow reset
            $('#reset').live("click", function() {
                
                // show all workflows
                $.get('workflow.php?step=false', function(data){
                    $('#hiddenlightbox').hide();
                    // replace filters with new data
                    $('#hiddenlightbox').html(data);
                    $('#hiddenlightbox').show();
                });
            });
            
            
            //
            // Workflow Validation and links
            //
                
            // add button clicked
            $('#add').live("click", function() {
                
                $("#action").validate({
                    submitHandler: function(form) {
                        
                        // construct json
                        var jsonString = '{ "add" : [';
                            
                        // get all data and item_ids to be added ...
                        
                        // for text box data
                        $("#action input[type='text']").each(function(){
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
                        });
                        
                        // for dropdown selects
                        $("#action select").each(function(){
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
                        });
                        
						// for hidden values
						$("#action input[type='hidden']").each(function(){
							
							// send email action
							if($(this).attr('id')=='email') {
                                jsonString += '{ "id": ' + $(this).attr("data") + ',"mailto": "' + $(this).val() +'"},';
							}
							
							// any other values need processing ?
							
                        });
						
						
						
						
						
						
                        // chop off last comma
                        jsonString = jsonString.slice(0,-1);
                        
                        jsonString += ']';
                        
                        jsonString += '}';
                        
						alert(jsonString);
						
                        // submit data 
                        $.get('index.php?action=add&record_data='+jsonString, function(data){
                            
                            var action = $('.container fieldset legend').text();
                            
                            if(data  && data!=false) {    
                                alert(action+":\n\nNew record created successfully.");
                                
                                // TODO: 
                                // check for workflow links
                                // if(there are workflow links){
                                    // get workflow links
                                //}
                                //else {
                                    // show the home screen (and all workflows)
                                    window.location.href='index.php';
                                //}
                                
                                return false;
                            } else {
                                alert(action+":\n\nAn error occurred, please try again.");
                                return false;
                            }
                            
                            return false;
                        });
                        
                        return false;
                    
                    }
                });
            });
            
            // reset button clicked
            $('#resetform').live("click", function() {
                
                // reset form
                $("#action").each(function(){  this.reset(); });
                
                // hide all errors
                $('label.error').each(function(){  $(this).hide(); });
                
                return false;
            });
            
            // cancel button clicked
            $('#cancel').live("click", function() {
                
                // show the home screen
                window.location.href='index.php';
                
            });
            
            
            //
            // Filters
            //
            
            // programme filter change
            $('#programmes').live("change", function(){
                
                // get selected filter
                var selected_programme = $(this).children(":selected").attr("id");
                
                if(selected_programme!="0") {
                    // filter based on selected programme
                    $.get('filter.php?type=P&data='+selected_programme, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // show all
                    $.get('filter.php?type=$data=', function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
            
            // course filter change
            $('#courses').live("change", function(){
                
                // get selected filter
                var selected_course = $(this).children(":selected").attr("id");
                
                if(selected_course!="0") {
                    // filter based on selected programme
                    $.get('filter.php?type=C&data='+selected_course, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                } else {
                    // course filter has been cleared ...
                    // get the currently selected programme
                    var selected_programme = $('#programmes').children(":selected").attr("id");
                    
                    // show filters based on the selected programme
                    $.get('filter.php?type=P$data='+selected_programme, function(data){
                        $('#mainfilters').hide();
                        // replace filters with new data
                        $('#mainfilters').html(data);
                        $('#mainfilters').show();
                    });
                }
            });
        }
    
    }); // end of document ready



}()); // closure