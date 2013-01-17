

var gridmanager = gridmanager || (function() {
    
    
    "use strict";
    
    
    $(document).ready(function(){
        
        $('table tr').click(function() {
           var id=$(this).closest('tr').children('td:first').text();
           var selectedRow=$(this).closest('tr');
           //var dt = $('#table-example').DataTable();
                    
           // check if id is a valid number
           if(id*1==id) {
               
                if($('body').attr('id')=="user-enrolments") {
                   
                    if($(this).closest('tr').attr('data')=='removed') {
                       // do nothing
                    } else {
                        if(confirm("Are you sure you want to remove this enrolment?")==true) {
                            alert("record: " + id +" has been removed.");
                            //$('#table-example').DataTable().fnDeleteRow(0);
                            //dt.fnDeleteRow(selectedRow.prev('tr'), null, true);
                            //$(this).closest('tr').remove();
                            //$('.paginate_active').click();
                            $(this).closest('tr').css('color','red');
                            $(this).closest('tr').css('text-decoration','line-through');
                            $(this).closest('tr').attr('data','removed');
                       } else {
                            alert('no action');
                       }
                    }
               }
               
               //$.get('ajax/prevcalldata.php', {"id": id }, function(){
               //    $('#prevcalldata').show();
               //});
           }
        });
       
    });
    
}()); // closure