

var gridmanager = gridmanager || (function() {
    
    
    "use strict";
    
    
    $(document).ready(function(){
        
        $('table tr').click(function() {
           var id=$(this).closest('tr').children('td:first').text();
           var selectedRow=$(this).closest('tr');
           var dt = $('#table-example').dataTable().dataTableExt.oApi;
                    
           // check if id is a valid number
           if(id*1==id) {
               
               if($('body').attr('id')=="user-enrolments") {
                   
                   if(confirm("Are you sure you want to remove this enrolment?")==true) {
                        alert("record: " + id +" has been removed.");
                        dt.fnDeleteRow(selectedRow, null, true);
                        //$(this).closest('tr').remove();
                        //$('.paginate_active').click();
                   } else {
                        alert('no action');
                   }
               }
               
               //$.get('ajax/prevcalldata.php', {"id": id }, function(){
               //    $('#prevcalldata').show();
               //});
           }
        });
       
    });
    
}()); // closure