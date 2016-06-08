var current;
var liking = false;
var gateUrl = prefix + "://" + host + "/gate/";
var likeUrl = prefix + "://" + host + "/api/like/";
var unlikeUrl = prefix + "://" + host + "/api/unlike/";
var deletePostUrl = prefix + "://" + host + "/api/deletePost/";
var loadCommentsUrl = prefix + "://" + host + "/api/getComments/"
var postCommentUrl = prefix + "://" + host + "/api/postComment/";
var commentsLoaded = 0; 

function isLoggedIn(){
	return ($(".username")[0]) ? true : false;
}

function like(that, postCode){
	if(!liking){
		liking = true;
		var loggedInUserId = $(".username").attr("data-user-id");
		var loggedInUsername = $(".username").text();
		var loggedInApiToken = $(".username").attr("data-user-token");
		var postUserId = $(that).parent().parent().attr("data-post-user-id");
		console.log(postUserId);
		console.log("liking " + postCode);
		$.post(likeUrl, {userId : loggedInUserId, username: loggedInUsername, apiToken : loggedInApiToken, postUserId: postUserId, postCode: postCode}, function(data){
			if($.isEmptyObject(data.errors) && data.response.success == true){
				$(that).addClass("imliked");
				var likesCount = parseInt($(that).children().children(".imlikes-count").text()) + 1;
				$(that).children().children(".imlikes-count").text(likesCount);
				liking = false;
			}else{
				$("#error-description").text(data.errors);
				$(".error-container").slideDown(500);
			}
		});
	}
	
}

function unlike(that, postCode){
	if(!liking){
		liking = true;
		var loggedInUserId = $(".username").attr("data-user-id");
		var loggedInUsername = $(".username").text();
		var loggedInApiToken = $(".username").attr("data-user-token");
		var postUserId = $(that).parent().parent().attr("data-post-user-id");
		console.log("liking " + postCode);
		$.post(unlikeUrl, {userId : loggedInUserId, username: loggedInUsername, apiToken : loggedInApiToken, postUserId: postUserId, postCode: postCode}, function(data){
			if($.isEmptyObject(data.errors) && data.response.success == true){
				$(that).removeClass("imliked");
				var likesCount = parseInt($(that).children().children(".imlikes-count").text()) - 1;
				$(that).children().children(".imlikes-count").text(likesCount);
				liking = false;
			}else{
				$("#error-description").text(data.errors);
				$(".error-container").slideDown(500);
			}
		});
	}
}

function isUserLoggedIn(){
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInUsername = $(".username").text();
	var loggedInApiToken = $(".username").attr("data-user-token");
	if(loggedInUserId != "" && loggedInUsername != "" && loggedInApiToken !="") return true;
	else return false;
}

function deletePost(){
	var postCode = $("div.post").attr("id");
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInUsername = $(".username").text();
	var loggedInApiToken = $(".username").attr("data-user-token");
	$.post(deletePostUrl, {userId : loggedInUserId, apiToken : loggedInApiToken, postCode: postCode}, function(data){
		if($.isEmptyObject(data.errors) && data.response.success == true){
			window.location = "http://" + host +"/u/" + loggedInUsername;
		}else{
			$("#error-description").text(data.errors);
			$(".error-container").slideDown(500);
		}
	});
}

function loadMoreComments(){
	var postCode = $("div.post").attr("id");
	var url = loadCommentsUrl + "?postCode=" + postCode + "&offset=" + commentsLoaded;
	$.get(url, function(data){
		if($.isEmptyObject(data.errors) && data.response.success == true){
			if($.isEmptyObject(data.response.comments)){
				$("#loadMoreButton").text("No more comments");
				$("#loadMoreButton").attr("disabled", "disabled");
				console.log("no comments");
			}else{
				comments = data.response.comments;
				commentsLoaded += comments.length;

				for(var i = 0; i < comments.length; i++){
					var element = '<div class="comment-container"><div class="comment-header"><span class="comment-username">' + comments[i].username + 
					'</span><span class="comment-created-at">' +  comments[i].createdAt + '</span></div><div class="comment-content">' + comments[i].content + '</div></div>';
					$("#comments-container").append(element);
				}
			}
		}else{
			$("#error-description").text(data.errors);
			$(".error-container").slideDown(500);
		}
	});
}

function postComment(content){
	var postCode = $("div.post").attr("id");
	var loggedInUserId = $(".username").attr("data-user-id");
	var loggedInUsername = $(".username").text();
	var loggedInApiToken = $(".username").attr("data-user-token");
	$.post(postCommentUrl, {userId : loggedInUserId, apiToken : loggedInApiToken, postCode: postCode, username: loggedInUsername, content: content}, function(data){
		if($.isEmptyObject(data.errors) && data.response.success == true){
			$("#success-description").text("Comment posted.");
			$(".success-container").slideDown(500);
			setInterval(function () {location.reload()}, 500);
		}else{
			$("#error-description").text(data.errors);
			$(".error-container").slideDown(500);
		}
	});
}

$(function(){
	$("img.lazy").lazyload();

	$(".imlikes").click(function(){
		var parent = $(this).parent();
		if(isUserLoggedIn()){
			if($(parent).hasClass("imliked")) unlike(parent, $(parent).attr("data-postCode"));
			else like(parent, $(parent).attr("data-postCode"));
		}else{
			$("#error-description").text("You are not logged in.");
			$(".error-container").slideDown(500);
		}
		
	});

	$("#loadMoreButton").click(function(){
		loadMoreComments();
	});

	$(".delete-btn").click(function(){
		var that = this;
		var cont = $(this).parent();
		var imsure = $(cont).children(".imsure-delete-btn");
		var cancel = $(cont).children(".cancel-delete-btn");
		$(imsure).removeClass("hidden");
		$(cancel).removeClass("hidden");
		$(that).addClass("hidden");
		$(imsure).click(function(){
			deletePost();
		});
		$(cancel).click(function(){
			$(imsure).addClass("hidden");
			$(cancel).addClass("hidden");
			$(that).removeClass("hidden");
		});
	});

	$(".comment-form").bind('keyup',function (){
		if($(this).val().length > 0 && $(this).val().length < 255){
			$(this).next().removeAttr("disabled");
		}else{
			$(this).next().attr("disabled", "disabled");
		}
	});

	$(".post-comment-btn").click(function(){
		var that = this;
		var cont = $(this).parent();
		var content = $(this).prev().val();
		if(content.length > 0 && content.length < 255) postComment(content);
		else{
				$("#error-description").text("YOU WOT M8");
				$(".error-container").slideDown(500);
		}
	});
});

if(!window.mobilecheck()){ // functions only for desktop 

	$(".change-image").click(function(){
		$("#image-chooser").trigger("click");
	});

	$("#image-chooser").change(function(){
		if($(this).val() != '') changeImage();
	});
	
	$(".clickable").click(function(){
		var imageUrl = $(this).parent().attr("data-to-load");
		var width = $(this).width() * 1.3;
		var height = $(this).height() * 1.3;
		var x = $(window).width() / 2 - width / 2;
		var y = $(window).height() / 2 - height / 2 + document.body.scrollTop;
		var element = '<div class="imageBig" style="\
		width: ' + width + 'px;\
		height: ' + height + 'px;\
		background: url(' + imageUrl + ');\
		background-size: contain;\
		top: ' + y + 'px;\
		left: ' + x + 'px;">\
		<span class="glyphicon glyphicon-remove close"></span>\
		</div>';
		$("body").append(element);
		$("#wrapper").css("opacity", "0.2");
		$("#wrapper").hide(0).show(0); // rerender for chrome it bugs out
	});

	$("body").delegate(".imageBig > .close", "click", function() {
		$("#wrapper").css("opacity", "1");
		$("#wrapper").hide(0).show(0);
	 	$(this).parent().remove();
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