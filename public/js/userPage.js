var editProfileUrl = prefix + "://" + host + "/api/editProfile/";
var changeImageUrl = prefix + "://" + host + "/api/uploadProfileImage/";
var followUrl = prefix + "://" + host + "/api/follow/";
var unfollowUrl = prefix + "://" + host + "/api/unfollow/";
var followersListUrl = prefix + "://" + host + "/api/getFollowersList/";
var followingListUrl = prefix + "://" + host + "/api/getFollowsList/";
var followButtonPressed = false;
var unfollowButtonPressed = false;
var editing = false;
var followersOffset = 0;
var followingOffset = 0;

function follow(){
	var toFollow = $(".follow-button").attr("data");
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInApiToken = $(".username").attr("data-user-token");
	$.post(followUrl, {toFollow : toFollow, userId : loggedInUserId, apiToken : loggedInApiToken}, function(data){
		if(data.response.success == true){
			var button = $(".follow-button");
			$(button).removeClass("follow-button");
			$(button).addClass("unfollow-button");
			$(button).text("Unfollow");
		}
		followButtonPressed = false;
	});
}

function unfollow(){
	var toUnfollow = $(".unfollow-button").attr("data");
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInApiToken = $(".username").attr("data-user-token");

	$.post(unfollowUrl, {toUnfollow : toUnfollow, userId : loggedInUserId, apiToken : loggedInApiToken}, function(data){
		if(data.response.success == true){
			var button = $(".unfollow-button");
			$(button).removeClass("unfollow-button");
			$(button).addClass("follow-button");
			$(button).text("Follow");
		}
		unfollowButtonPressed = false;
	});
}

function editProfile(){
	editing = true;
	$(".edit-profile").text("Stop editing");

	var imageDiv = $("div.image");
	var userDescDiv = $("div.user-desc");
	var userDescText = $(userDescDiv).children(".user-text").text();
	var confirmDescButton = $(userDescDiv).children(".user-text-confirm");
	var userDescTextInput = $(userDescDiv).children(".user-text-input");
	var changeImageButton = $(imageDiv).children(".change-image");
	$(userDescDiv).children(".user-text").hide(0);
	$(confirmDescButton).removeClass("hidden");
	$(userDescTextInput).removeClass("hidden");
	if(!window.mobilecheck()) $(changeImageButton).removeClass("hidden");
	$(userDescTextInput).val(userDescText);
}

function cancelEdit(){
	var userDescDiv = $("div.user-desc");
	var imageDiv = $("div.image");
	$(".edit-profile").text("Edit profile");
	$(userDescDiv).children(".user-text").css("display", "inline");
	$(userDescDiv).children(".user-text-confirm").addClass("hidden");
	$(userDescDiv).children(".user-text-input").addClass("hidden");
	$(imageDiv).children(".change-image").addClass("hidden");
	editing = false;
}

function confirmDescEdit(){
	var userDescDiv = $("div.user-desc");
	var userDescText = $(userDescDiv).children(".user-text-input").val();
	if(validateUserDesc(userDescText)){
		$(userDescDiv).children(".user-text").css("display", "inline");
		$(userDescDiv).children(".user-text-confirm").addClass("hidden");
		$(userDescDiv).children(".user-text-input").addClass("hidden");
		var loggedInUserId = $(".username").attr("data-user-id");
		var loggedInApiToken = $(".username").attr("data-user-token");
		$(".edit-profile").prop('disabled', false);
		$.post(editProfileUrl, {userId : loggedInUserId, apiToken : loggedInApiToken, userDesc: userDescText}, function(data){
			if($.isEmptyObject(data.errors) && data.response.success == true){
				$(userDescDiv).children(".user-text").text(userDescText);
				$("#success-description").text("You profile has been successfully updated.");
				$(".success-container").slideDown(500);
			}else{
				$("#error-description").text(data.errors);
				$(".error-container").slideDown(500);
			}
		});
	}else{
		$("#error-description").text("Description can be 140 characters max.");
		$(".error-container").slideDown(500);
	}
}

function changeImage(){
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInUsername = $(".username").text();
	var loggedInApiToken = $(".username").attr("data-user-token");
	var file = $("#image-chooser")[0].files[0];
	var formData = new FormData();
	formData.append("userId", loggedInUserId);
	formData.append("apiToken", loggedInApiToken);
	formData.append("profileImage", file);
	$.ajax({
		url: changeImageUrl,
		type: 'POST',
		data: formData,
		cache: false,
		dataType: 'json',
		processData: false, 
		contentType: false,
		success: function(data, textStatus, jqXHR){
			if($.isEmptyObject(data.errors) && data.response.success == true){
				var imageUrl = $(".image > .holder").attr("src");
				$.get(imageUrl, function(){
					 $(".image > .holder").attr("src", imageUrl);
				});
				$("#success-description").text("You profile image has been successfully changed.");
				$(".success-container").slideDown(500);
			}else{
				$("#error-description").text(data.errors);
				$(".error-container").slideDown(500);
			}
		}
	});
}

function validateUserDesc(text){
	return (text.length <= 140) ? true : false;
}

function getFollowersList(userId, offset){
	var url = followersListUrl + "?userId=" + userId + "&offset=" + offset;
	$.get(url, function(data){
		if(data.response){
			var users = data.response.users;
			for(var i = 0; i < users.length; i++){
				followersOffset++;
			}
			showFollowBox(users, "followers");
		}
	}); 
}

function getFollowingList(userId, offset){
	var url = followingListUrl + "?userId=" + userId + "&offset=" + offset;
	$.get(url, function(data){
		if(data.response){
			var users = data.response.users;
			for(var i = 0; i < users.length; i++){
				followingOffset++;
			}
			showFollowBox(users, "following");
		}
	}); 
}

function showFollowBox(users, type){
	var old = $(".followBox > .users").html();
	var isUp = (old == undefined) ? false : true;
	var width = 400;
	var height = 600
	var x = $(window).width() / 2 - width / 2;
	var y = $(window).height() / 2 - height / 2 + document.body.scrollTop;
	var elements = (old == undefined) ? '' : old;
	for(var i = 0; i < users.length; i++){
		elements += '<a href="' + prefix + '://' + host + '/u/' + users[i].username + '"><div class="follow-username">' + users[i].username + '</div></a>';
	}
	if(isUp){
		$(".followBox > .users").html(elements);
	}else{
		var element = '<div class="followBox" style="width: ' + width + 'px; height:' + height + 'px;top:' + y + 'px; left:' + x + 'px; z-index: 600;"><div class="title">' + type + 
		'</div><span class="glyphicon glyphicon-remove close close-follow-box">' + 
		'</span><div class="users">'+ elements +'</div><button class="load-more-followers-button" data-type="' + type + '">Load more</button></div>';
		$("body").append(element);
		$("#wrapper").hide(0).show(0); // rerender for chrome it bugs out


	}
	console.log("here");
};


$(function(){
	$(".closeable > button").click(function(){
		$(this).parent().slideUp(250);
	});

	$(".edit-profile").click(function(){
		if(editing){
			cancelEdit();
		}else{
			editProfile();
		}
		FontResize.resize();
		VertCent.center();
	});

	$(".following").click(function(){
		if(!$(".followBox")[0]){
			followingOffset = 0;
			var userId = $(".profile-username").attr("data-user-id");
			getFollowingList(userId, followingOffset);
		}
	});

	$(".followers").click(function(){
		if(!$(".followBox")[0]){
			followersOffset = 0;
			var userId = $(".profile-username").attr("data-user-id");
			getFollowersList(userId, followingOffset);
		}
	});

	$(".user-text-confirm").click(function(){
		confirmDescEdit();
	});

	$(".follow > button").click(function(){
		if(isLoggedIn()){
			if($(this).hasClass("follow-button")){
				if(!followButtonPressed){
					follow();
					followButtonPressed = true;
				}
			}else if($(this).hasClass("unfollow-button")){
				if(!unfollowButtonPressed){
					unfollow();
					unfollowButtonPressed = true;
				}
			}
		}else{
			window.location = gateUrl;
		}
	});

});

if(!window.mobilecheck()){ // functions only for desktop 

	$("body").delegate(".close-follow-box", "click", function() {
		$("#wrapper").css("opacity", "1");
		$("#wrapper").hide(0).show(0);
	 	$(this).parent().remove();
	 	followingOffset = 0;
	 	followersOffset = 0;
	});

	$("body").delegate(".load-more-followers-button", "click", function() {
		var argument = $(".load-more-followers-button").attr("data-type");
		var userId = $(".profile-username").attr("data-user-id");
		if(argument == "following") getFollowingList(userId, followingOffset);
		else getFollowersList(userId, followersOffset);
	});

	$(document).mouseup(function (e){
		var container = $(".imageBig");
	  if (!container.is(e.target) && container.has(e.target).length === 0){
			$("#wrapper").css("opacity", "1");
			$("#wrapper").hide(0).show(0); // rerender for chrome it bugs out
			container.remove(0);
	  }
	});
}else{ // functions only for mobile
	var posts = document.getElementsByClassName("post");
	for(var i = 0; i < posts.length; i++){
		posts[i].addEventListener('touchstart', function(){
			var currentOpacity = $(this).children(".header").css("opacity");
			if(currentOpacity < 0.8) $(this).children(".hideable").css("opacity", "0.8");
			else $(this).children(".hideable").css("opacity", "0.3");
		});
	}

	$(function(){
		var screenW = $(window).width();
		var screenH = $(window).height();
		var postH = screenW/(9/16);
		$(".post").css({
			"width": screenW + "px",
			"height": postH + "px", 
		});
	});
}