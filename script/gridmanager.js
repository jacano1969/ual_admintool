

var gridmanager = gridmanager || (function() {
    
    
    "use strict";
    
    
    $(document).ready(function(){
        
        $('table tbody tr').click(function() {
           var id=$(this).closest('tr').children('td:first').text();
           var selectedRow=$(this).closest('tr');
                    
           // check if id is a valid number
           if(id*1==id) {
               
                if($('body').attr('id')=="user-enrolments") {
                   
                    if($(this).closest('tr').attr('data')=='removed') {
                       // do nothing
                    } else {
                        var remove = confirm("Are you sure you want to remove this enrolment?");
                        if(remove==true) {
                                                        
                            // remove the enrolment by roecord id
                            // $.get('removeenrolment.php?, {"id":id }, function(data){
                                $(this).closest('tr').css('color','red');
                                $(this).closest('tr').css('text-decoration','line-through');
                                $(this).closest('tr').attr('data','removed');
                                alert("Enrolment " + id +" has been removed.");
                            //});
                            
                       } else {
                            
                       }
                       remove = null;
                    }
               }
               
               //$.get('ajax/prevcalldata.php', {"id": id }, function(){
               //    $('#prevcalldata').show();
               //});
           }
        });
       
    });
    
}()); // closure