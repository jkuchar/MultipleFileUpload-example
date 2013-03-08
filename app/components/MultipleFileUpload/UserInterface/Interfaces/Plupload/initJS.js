var fallbackController = this;
(function($){
	// API: http://www.plupload.com/plupload/docs/api/index.html

	// Convert divs to queue widgets when the DOM is ready
	$(function(){
		// TODO: auto fallback
		var uploader = $("#"+{!$id|escapeJs}).pluploadQueue({
			// General settings
			runtimes : 'gears,browserplus,silverlight,flash,html5,html4',
			{* runtimes : 'gears,html5,browserplus,silverlight,html4', *}
			{* runtimes : 'flash',*}
			url : {!$uploadLink|escapeJs},
			max_file_size : {!$sizeLimit},
			chunk_size : '5mb',
			
			// Intentionally do not use headers, because not all interfaces allows you to send them.
			// insted using parameters in URL or POST
			
			// Flash settings
			flash_swf_url : {!$baseUrl|escapeJs}+'/swf/MultipleFileUpload/plupload/plupload.flash.swf',

			// Silverlight settings
			silverlight_xap_url : {!$baseUrl|escapeJs}+'/xap/MultipleFileUpload/plupload/plupload.silverlight.xap'
		});
		uploader = $(uploader).pluploadQueue();
		console.log(uploader);
		var refreshFn = function(){ // if plupload moves around page, good to recompute position of uploader
			uploader.refresh();
		};
		setInterval(refreshFn,1000);
		refreshFn();
		
		uploader.bind("Error",function(){
			fallbackController.fallback();
		})
	});

	return true; // OK

})(jQuery);