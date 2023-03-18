$(document).ready(function(){   
   
   if ($('#articles').length > 0){
      PagerLoadContent();
   }

   $(window).on('scroll', PagerLoadContent);

   function PagerLoadContent()
   {
      var position = $(window).scrollTop() + $(window).height();
      var bottom = $(document).height()-0.50;
      var $div = $('.articles');
      var page = 0;
      if ($('#pager').attr('data-page')) {
         page = $('#pager').attr('data-page');
      } 
      if(position >= bottom) {
         $.ajax({  
            url:        '/posts/pager',  
            type:       'POST',   
            data:        {'page': page},
            dataType:   'html',  
            
            success: function(data, status) {  
               $("#pager").remove();
               if (data.length > 0)
               {
                  $div.append($.parseHTML(data, document, true));
               } else {
                  $(window).off('scroll', PagerLoadContent);
               }
            },  
            error : function(xhr, textStatus, errorThrown) {  
               console.log('Ajax request failed.');  
            }  
         }); 
      }
   }
 });  