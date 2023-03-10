$(document).ready(function() {
    $('#post_tags').chosen({width: '95%'});

    $('.chosen-choices').addClass('form-control');

    $('.chosen-search-input').autocomplete({
        source: function( request, response ) {
          $.ajax({
            url: "/tag/autocomplete/"+request.term,
            dataType: "json",
            beforeSend: function(){$('ul.chosen-results').empty();},
            success: function( data ) {
                $.each(data, function (index, value){
                    $('ul.chosen-results').append('<li class="active-result" data-option-array-index="'+index+'">' + value + '</li>');
                    $('#post_tags').append($('<option>', {
                        value: index,
                        text: value
                    }));
                    $("#post_tags").trigger("chosen:updated");
                })
            }
          });
          
        }
      });
});