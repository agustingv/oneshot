$(document).ready(function(){   
    $(".showmore #loadcontent").on("click", function(event){  
       event.preventDefault();
       var $div = $('.articles');
      
       $.ajax({  
          url:        pager_url,  
          type:       'POST',   
          data:        {'page': page},
          dataType:   'html',  
          
          success: function(data, status) {  
            if (data.length > 0)
            {
                $div.append($.parseHTML(data, document, true));
            } 
            else if (data.length == 0)
            {
                $( ".showmore #loadcontent").replaceWith( "<h4>Ooops!. Si puedes leer esto es que has llegado al final de los contenidos de este listado</h4>" );
            }
          },  
          error : function(xhr, textStatus, errorThrown) {  
             console.log('Ajax request failed.');  
          }  
       });  
    });
 });  