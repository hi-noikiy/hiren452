require(["jquery"], function( $ ) {
    "use strict";

    $.fn.progressBar = function( options ) {
		var settings = $.extend( {
		  'url'         : '',
                  'waitLabel'   : ''
		}, options);
		var element = this;
        //setInterval(function(){worker(update_url, element)}, 10000);
      
	worker(settings.url, this, settings.waitLabel);
    };

    function progress_animate(element, width) {
        
        $('#progress-count').html(width+'%');
        element
            .data("origWidth", element.width())
            .animate({
                width: width+'%'
            }, 1200);
            if(width >= '100'){
			$('.meter span span').css('-webkit-animation','none');
		}
    }
   
    function worker(update_url, element, waitLabel) {
		try{
			$.get(update_url, function(data) {
				// Now that we've completed the request schedule the next one.
				var width = 0;
				if(data != '' && data != 'undefined') {
                                        if(isNaN(parseInt(data))) {
                                        	setTimeout(function(){
							$('#link').html(waitLabel);
						},1200)
						width = 100;
					} else {
						width = parseInt(data);
						if(width < 100) {
							setTimeout(function() {worker(update_url, element, waitLabel);}, 3000);
						}
					}
					progress_animate(element, width);
				} else {
					setTimeout(function() {worker(update_url, element, waitLabel);}, 3000);
				}
			});
		}catch(e){console.log(e);}		
    }
});
