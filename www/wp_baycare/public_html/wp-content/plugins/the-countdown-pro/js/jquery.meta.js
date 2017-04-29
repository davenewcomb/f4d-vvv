/**
 * @detail jQuery Total Countdown Pro Meta
 * Additional function to handle content
 * http://zourbuth.com/
 */

jQuery(document).ready(function($){

	// Callback select option function
	// @Since 1.1
	var
	postId = $("#post_ID").val(),
	callbackVal = $('select[name="tcp[callback]"]').find("option:selected").val();
	
	$.post(ajaxurl,{ action: tcpLocalize.action, id: postId, callback: callbackVal, nonce: tcpLocalize.nonce }, function(data){
		$("#callback-wrapper").empty().append(data);
	});
	
	$('select[name="tcp[callback]"]').change(function() {
		callbackVal = $(this).find("option:selected").val();
		$("#callback-wrapper").empty().append("<p class='loading'>Loading...</p>");
		$.post(ajaxurl,{ action: tcpLocalize.action, id: postId, callback: callbackVal, nonce: tcpLocalize.nonce }, function(data){
			$("#callback-wrapper").empty().append(data);
		});		
	});

});