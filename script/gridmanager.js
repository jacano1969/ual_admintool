

var gridmanager = gridmanager || (function() {
    
    
    "use strict";
    
    
    $(document).ready(function(){
        
        // back button clicked
        $('#back').live("click", function() {
        
            // show the home screen
            window.location.href='index.php';
                
        });
            
            
        $('table tbody tr').click(function() {
           var id=$(this).closest('tr').children('td:first').text();
           var selectedRow=$(this).closest('tr');
                    
           // check if id is a valid number
           if(id*1==id) {
               
                if($('body').attr('id')=="user-enrolments") {
                   
                    if($(this).closest('tr').attr('data')=='removed') {
                       // do nothing
                    } else {
                        var remove = confirm("Are you sure you want to remove this enrollment?");
                        if(remove==true) {
                                                        
                            // remove the enrolment by record id
                            $.get('actions/rmenrolment.php', {"id":id }, function(data){
                                selectedRow.css('color','red');
                                selectedRow.css('text-decoration','line-through');
                                selectedRow.attr('data','removed');
                                remove = null;
                                alert("Enrollment " + id +" has been removed.");                                
                            }).fail(function() { alert("An error has occurred deleting enrollment " + id +"."); });
                            
                       } else {
                            
                       }
                       
                    }
               }
           } else {
                
                // add new course enrollments
                if($('body').attr('id')=="possible-enrolments") {
                    
                    if($(this).closest('tr').attr('data')=='added') {
                       // do nothing
                    } else {
                        var add = confirm("Are you sure you want to add this enrollment?");
                        if(add==true) {
                                                        
                            // add the enrolment by course id
                            $.get('actions/adenrolment.php', {"courseid":id }, function(data){
                                selectedRow.css('color','green');
                                selectedRow.css('font-weight','bold');
                                selectedRow.attr('data','added');
                                add = null;
                                alert("Enrollment for course " + id +" has been added.");
                            }).fail(function() { alert("An error has occurred adding enrollment for course: " + id +"."); });
                            
                       } else {
                            
                       }
                    }                    
                }
           }
        });
       
    });
    
}()); // closure