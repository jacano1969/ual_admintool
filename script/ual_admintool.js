

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
                
                // show all workflows
                $.get('workflow.php?step=false', function(data){
                    $('#hiddenlightbox').hide();
                    // replace filters with new data
                    $('#hiddenlightbox').html(data);
                    $('#hiddenlightbox').show();
                });
                
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
                        
                        // TODO: 
                        /*{
                            "add": [
                                {
                                    "id": 1,
                                    "data": "1"
                                },
                                {
                                    "id": 2,
                                    "data": "2"
                                },
                                {
                                    "id": 3,
                                    "data": "3"
                                }
                            ]
                        }*/
                        
                        // construct json
                        jsonString = '{ "add" : [';
                            
                        // get all data and item_ids to be added
                        $("#action").each(function(){
                            
                            // for text box data
                            if($(this).is('input[type="text"]')) {
                                
                                jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
                                
                            }
                            
                            // TODO:
                            // for dropdown selects
                            
                        });
                        
                        // chop off last comma
                        jsonString = jsonString.replace(/,$/, "");
                        
                        jsonString += ']';
                        
                        alert(jsonString);
                        
                        //form.submit();
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