

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
                var sub_step_action = $(this).children(":selected").attr("id");
                
                // set sub step action and enable ok button (submit)
                if(sub_step_action!="0"){
                    $('#sub_step_action').val(sub_step_action);
                    $('#ok').disabled=false;
                }                
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