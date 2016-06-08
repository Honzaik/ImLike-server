$(function(){

	var broadcastMessageUrl = "http://10.0.0.1/api/broadcastGlobal/"

	$(".broadcastGlobalMessageButton").click(function(){
		$(this).text("...Broadcasting...")
		var loggedInUserId = $(".username").attr("data-user-id");
		var loggedInApiToken = $(".username").attr("data-user-token");
		var message = $(".globalMessageInput").val();
		var that = $(this);
		$.post(broadcastMessageUrl, {userId : loggedInUserId, apiToken : loggedInApiToken, message : message}, function(data){
			if($.isEmptyObject(data.errors) && data.response.success == true){
				$("#success-description").text("You have successfully broadcasted a message: \"" + message + "\"");
				$(".success-container").slideDown(500);
			}else{
				$("#error-description").text(data.errors);
				$(".error-container").slideDown(500);
			}
			$(that).text("Broadcast");
		});
	});

	$(".close").click(function(){
		$(this).parent().fadeOut(500);
	});

});