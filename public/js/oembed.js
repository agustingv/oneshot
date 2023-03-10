$(document).ready(function(){   
    $('oembed').each(function( emb ) {
        $(this).oembed($(this).attr('url'), {
            embedMethod: 'auto',	// "auto", "append", "fill"	
        })
    })
});