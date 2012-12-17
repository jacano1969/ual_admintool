

var ual_admintool = ual_admintool || (function(){
	
	"use strict";

    $(document).ready(function(){
    
	    function isNumber(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
			
        // home page scripts
        if($('#home-page').length>0) {
            
			
			//
			// data grid
			//
			
			if($("data-grid").length>0) {
			    $("data-grid").tablesorter();
		    }
			
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
                
				// set the help text
				$('#helpbox').hide();
				var selected_workflow_help = $("option:selected", this).attr("help");
				$('#helptext').html(selected_workflow_help);
				$('#helpbox').fadeIn('slow');
				
				if(selected_workflow_step=="10000") {
				    location.href="designer.php";	
				} else {
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
							// get the value from the selected option id
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $("option:selected", this).attr("id") +'"},';
                        });
                        
						// for hidden values
						$("#action input[type='hidden']").each(function(){
							
							// send email action
							if($(this).attr('id')=='email') {
                                jsonString += '{ "id": ' + $(this).attr("data") + ',"mailto": "' + $(this).val() +'"},';
							} else {					
							    // any other values need processing ?
								if(typeof($(this).attr("data"))!='undefined') {
								    jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
								}
							}
							
                        });
						
                        // chop off last comma
                        jsonString = jsonString.slice(0,-1);
                        
                        jsonString += ']';
                        
                        jsonString += '}';
						
						var action_desc = $('.container fieldset legend').text();
						
                        // submit data 
                        $.get('index.php?action=add&action_desc='+action_desc+'&record_data='+jsonString, function(data){
                            
                            
                            
                            if(data  && data!=false) {    
                                alert(action_desc+":\n\nNew record created successfully.");
                                
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
            
			// update button clicked
            $('#update').live("click", function() {
			    $("#action").validate({
                    submitHandler: function(form) {
                        
                        // construct json
                        var jsonString = '{ "update" : [';
                            
                        // get all data and item_ids to be added ...
                        
                        // for text box data
                        $("#action input[type='text']").each(function(){
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
                        });
						
						// for text area data
                        $("#action textarea").each(function(){
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
                        });
                        
                        // for dropdown selects
                        $("#action select").each(function(){
							// get the value from the selected option id
                            jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $("option:selected", this).attr("id") +'"},';
                        });
                        
						// for hidden values
						$("#action input[type='hidden']").each(function(){
							
							// send email action
							if($(this).attr('id')=='email') {
                                jsonString += '{ "id": ' + $(this).attr("data") + ',"mailto": "' + $(this).val() +'"},';
							} else {					
							    // any other values need processing ?
								if(typeof($(this).attr("data"))!='undefined') {
							        jsonString += '{ "id": ' + $(this).attr("data") + ',"data": "' + $(this).val() +'"},';
								}
							}
							
                        });
						
                        // chop off last comma
                        jsonString = jsonString.slice(0,-1);
                        
                        jsonString += ']';
                        
                        jsonString += '}';
						
						var action_desc = $('.container fieldset legend').text();
						
                        // submit data 
                        $.get('index.php?action=update&action_desc='+action_desc+'&record_data='+jsonString, function(data){
                            
                            
                            
                            if(data  && data!=false) {    
                                alert(action_desc+":\n\nRecord updated successfully.");
                                
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
		
		
		
		// workflow designer scripts
        if($('#designer-page').length>0) {
			
    		$('#workflows').live("change", function() {
				var workflow_id = $('option:selected',this).attr('id');
				if(workflow_id==0) {
					$('#workflow_name').prop('disabled', false);
					$('#workflow_description').prop('disabled', false);
					$('#workflow_id').val('0');
				}
				else {
					$('#workflow_name').prop('disabled', true);
					$('#workflow_description').prop('disabled', true);
					$('#workflow_id').val(workflow_id);
				}
			});
			
			$('#abort').live("click", function() {
				
				if(confirm("Are you sure you want to cancel?\nAll Progress will be lost!")==true) {
				    location.href="index.php"
			    }
				
				return false;
			});
			
			$('#continue').live("click", function() {
				
				// check the stage
				var stage = 0;
				
				if($('#stage').length>0) {
				    stage = $('#stage').val();
			    }
				
				// workflow
				if(stage==0 || stage==1) {	
					// check if either a workflow is selected or a new one is being created
					var workflow_id = $('option:selected','#workflows').attr('id');
					var new_workflow = $('#workflow_name').val();
					var new_workflow_desc = $('#workflow_description').val();
					
					new_workflow = new_workflow.replace(/^\s+|\s+$/g,"");
					new_workflow_desc = new_workflow_desc.replace(/^\s+|\s+$/g,"");
					
					if(new_workflow=="" && workflow_id==0) {
						alert("Please select an existing workflow or create a new workflow.");
						return false;
					} else {
						if(new_workflow_desc=="" && workflow_id==0) {
							alert("Please provide a description for your new workflow.");
							return false;
						}
					}
				}
				
				// workflow step
				if(stage==2) {
					// TODO:
					// check if we have a workflow
					//var workflow_id = $('#workflow_id').val();
							
					var workflow_step_name = $('#workflow_step_name').val();
					var workflow_step_desc = $('#workflow_step_description').val();
					
					workflow_step_name = workflow_step_name.replace(/^\s+|\s+$/g,"");
					workflow_step_desc = workflow_step_desc.replace(/^\s+|\s+$/g,"");
					
					if(workflow_step_name=="") {
						alert("Please enter a name for the new workflow step.");
						return false;
					} else {
						if(workflow_step_desc=="") {
							alert("Please provide a description for your new workflow step.");
							return false;
						}
					}			
				}
				
				// workflow sub step (optional)
				if(stage==3) {
					
					// this is optional so we are not checking this stage
					
				}
				
				// workflow action
				if(stage==4) {
					
					$("#designer_workflow").validate({
						rules: {
							workflow_form_elements: {
								required: true,
								digits: true, 
								min: 1,
								max: 20
							}
						}
					});					
				}					
				
				// workflow form
				if(stage==5) {
					
					var workflow_form_elements = $('#workflow_form_elements').val();
					
					for(var index=0; index<workflow_form_elements; index++) {
					    // update previews
					    $('*[id^=field_type]').live("change", function() {
							var field_type = $(this).attr('data');
							
							var preview ='';
							
							switch(field_type) {
								case 'text' : preview = '<input type="'+field_type+'">';
									          break;
								
								case 'dropdown' : preview = '<select><option></option></select>';
									              break;
								default : preview = '';
									      break;
							}
							
							$(this).closest('*[id^=preview]').html(preview);
						});
					}
					
					
				}
				
				return 'ok';   // allow form submit
			});
			
			
			//
			// general elements
			//
			
			// workflow action drop down
			$('#workflow_action').live("change", function() {
				var workflow_action_id = $('option:selected',this).attr('id');
				
				// set the help text
				$('#helpbox').hide();
				var selected_workflow_action_help='';
				selected_workflow_action_help = $("option:selected", this).attr("help");
				
				if(typeof(selected_workflow_action_help)!='undefined') {
				    $('#helptext').html(selected_workflow_action_help);
				    $('#helpbox').fadeIn('slow');
				}
			});
			
			// field type drop down
			$('#field_type').live("change", function() {
				var field_type_id = $('option:selected',this).attr('id');
				
				// set the help text
				$('#helpbox').hide();
				var selected_field_type_help='';
				selected_field_type_help = $("option:selected", this).attr("help");
				
				if(typeof(selected_field_type_help)!='undefined') {
				    $('#helptext').html(selected_field_type_help);
				    $('#helpbox').fadeIn('slow');
				}
			});
		}

    }); // end of document ready



}()); // closure