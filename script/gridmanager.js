

var gridmanager = gridmanager || (function() {
    
    
    "use strict";
    
    
    $(document).ready(function(){
        
        $('table tr').click(function() {
           var id=$(this).closest('tr').children('td:first').text();
           alert('This Id: ' + id);
           
           // check if id is a valid number
           if(id*1==id) {
               
               //$.get('ajax/prevcalldata.php', {"id": id }, function(){
               //    $('#prevcalldata').show();
               //});
           }
        });
       
    });
    
}()); // closure