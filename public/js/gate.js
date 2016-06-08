// GLOBALS 
var ajaxSent = false;
var emailRegex = /^\w+@\w+\.\w+$/;
var usernameRegex = /^[a-zA-Z0-9_]+$/;
var formValid = new Array(0,0,0,0) // 4 boolean values [0] email, [1] password, [2] username, [3] password retyped

$(function(){
	// FORM SELECTION START
	$(".btn-choice").click(function(){
		var whatToDo = $(this).attr("id");
		if(whatToDo == "button-login" && !$("#form-login").is(":visible")){
			$("#button-login:active");
			$("#form-register").slideUp(function(){
				$("#form-login").slideDown();
			});
		}else if(whatToDo == "button-register" && !$("#form-register").is(":visible")){
			$("#button-register:active");
			$("#form-login").slideUp(function(){
				$("#form-register").slideDown();
			});
		}
	});
	// FORM SELECTION END

	// AJAX REQUESTS START
	$(".submit").click(function(){
		var url = $(this).parent().attr("action");
		var action = (url.indexOf("login") !== -1) ? "login" : "register";
		if(ajaxSent) action = null;
		if(action === "login"){
			ajaxSent = true;
			$(".input-error").remove();
			loginUser(url);
			return false;
		}else if(action == "register"){
			$(".input-error").remove();
			ajaxSent = true;
			registerUser(url);
			return false;
		}
	});

	$(".form-control").bind('keyup change', function(){
		validate($(this));
		var isLogin = true;
		if($(this)[0].id.indexOf("login") === -1){ // validating register form
			isLogin = false;
		}else{ // validating login form
			isLogin = true;
		}
		for(var i = 0; i < formValid.length; i++){
			if(formValid[i] === 1){
				if(isLogin && i > 0 && i < 2) $("#login-submit").removeAttr("disabled");
				else $("#register-submit").removeAttr("disabled");
			}else{
				if(isLogin && i > 0 && i < 2) $("#login-submit").attr("disabled", "disabled");
				else $("#register-submit").attr("disabled", "disabled");
			}
		}
	});

});

function validate(that){
	var type = that[0].id.indexOf("retyped") === -1 ? that.context.type : "password-retyped";
	var value = that.val();
	var ok = false;
	switch(type){
		case "email": 
			if(emailRegex.test(value)){
				ok = true;
				formValid[0] = 1;
			}else{
				ok = false
				formValid[0] = 0;
			}
		break;
		case "text": 
			if(usernameRegex.test(value)){
				ok = true;
				formValid[2] = 1;
			}else{
				ok = false;
				formValid[2] = 0;
			}
		break;
		case "password":
			if(value.length > 5){
				ok = true;
				formValid[1] = 1;
				validate($("#register-password-retyped"));
			}else{
				ok = false;
				formValid[1] = 0;
			}
		break;
		case "password-retyped": 
			if(value === $("#register-password").val() && value.length > 5){
				ok = true;
				formValid[3] = 1;
			}else{
				ok = false;
				formValid[3] = 0;
			}
		break;
	}
	if(ok){
		$(that).next().removeClass("glyphicon-remove");
		$(that).next().addClass("glyphicon-ok");
	}else{
		$(that).next().removeClass("glyphicon-ok");
		$(that).next().addClass("glyphicon-remove");
	}
}

function registerUser(url){
	//check inputs
	var username = $("#register-username").val();
	var email = $("#register-email").val();
	var password = $("#register-password").val();
	var passwordRetyped = $("#register-password-retyped").val();
	var csrfToken = $("#register-csrf").val();
	var postParams = {
		"register-username" : username,
		"register-email" : email,
		"register-password" : password,
		"register-password-retyped" : passwordRetyped,
		"register-csrf" : csrfToken
	};
	$.post(url, postParams, function(responseJson){
		if(!$.isEmptyObject(responseJson.errors)){
			handleErrors(responseJson);
		}else if($.isEmptyObject(responseJson.errors) && !$.isEmptyObject(responseJson.response)){
			if(responseJson.response.registerSuccess){
				var token = responseJson.response.token;
				var username = responseJson.response.username;
				$("#response").removeClass("alert-danger");
				$("#response").addClass("alert-info");
				$("#response").text("You have been successfully registered.");
				$("#response").slideDown(500);
			}else{
				var message = "Something went wrong, try again.";
				$("#response").removeClass("alert-info");
				$("#response").addClass("alert-danger");
				$("#response").html(message);
				$("#response").slideDown(500);
			}
		}else{
			$("#response").removeClass("alert-info");
			$("#response").addClass("alert-danger");
			$("#response").text("Something went wrong, try again.");
			$("#response").slideDown(500);
		}
		ajaxSent = false;
	});
}

function loginUser(url){
	// check inputs
	var email = $("#login-email").val();
	var password = $("#login-password").val();
	var csrfToken = $("#login-csrf").val();
	var postParams = {
		"login-email" : email,
		"login-password" : password,
		"login-csrf" : csrfToken
	};
	$.post(url, postParams, function(responseJson){
		if(!$.isEmptyObject(responseJson.errors)){
			handleErrors(responseJson);
		}else if($.isEmptyObject(responseJson.errors) && !$.isEmptyObject(responseJson.response)){
			if(responseJson.response.loggedIn){
				/*
				var token = responseJson.response.token;
				var username = responseJson.response.username;
				$("#response").removeClass("alert-danger");
				$("#response").addClass("alert-info");
				$("#response").html('User: ' + username + '\n<br>Token: ' + token);
				$("#response").slideDown(500);*/
				window.location.replace("/home/");
			}else{
				var message = "Wrong credentials.";
				$("#response").removeClass("alert-info");
				$("#response").addClass("alert-danger");
				$("#response").html(message);
				$("#response").slideDown(500);
			}
		}else{
			$("#response").removeClass("alert-info");
			$("#response").addClass("alert-danger");
			$("#response").text("Something went wrong, try again.");
			$("#response").slideDown(500);
		}
		ajaxSent = false;
	});
}

function handleErrors(responseJson){
	$.each(responseJson.errors, function(key, value){
		if($("#error-" + key).length === 0){
			$('<div class="input-error" id="error-'+ key +'">' + value +'</div>').insertAfter($("#" + key).next());
		}
	});
}
