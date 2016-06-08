<?php
namespace ImLike;

class Localization{
	private static $messages = array(
		"en" => array( // pattern ControllerName:functionName-whateverName
			"ImLike\Controllers\ApiController::getUsersAuthTokenAndAesKey-tokenError" => "Something went wrong. Ouch!",
			"ImLike\Controllers\ApiController::getUsersAuthTokenAndAesKey-loginSuccess" => "You have successfuly logged in!",
			

			"ImLike\Controllers\ApiController::loginAction-loginFailed" => "Your login information is not correct.",

			"ImLike\Controllers\ApiController::registerAction-duplicateEmail" => "Your email is already in use.",
			"ImLike\Controllers\ApiController::registerAction-duplicateUsername" => "Your username is already in use.",
			"ImLike\Controllers\ApiController::registerAction-registerSuccess" => "Your have been successfuly registered. Check email for verification link.",


			"ImLike\Forms\LoginForm::initialize-emailPlaceholder" => "Enter your email",
			"ImLike\Forms\LoginForm::initialize-emailWrongFormat" => "The email has wrong format.",
			"ImLike\Forms\LoginForm::initialize-emailMessageMax" => "Your email is too long. (Over 255 characters)",
			"ImLike\Forms\LoginForm::initialize-emailMessageMin" => "The email is too short. (Minimum 3 characters)",
			"ImLike\Forms\LoginForm::initialize-emailLabel" => "Email address",

			"ImLike\Forms\LoginForm::initialize-passwordPlaceholder" => "Enter your password",
			"ImLike\Forms\LoginForm::initialize-passwordMessageMax" => "Your password is too long. (Over 1024 characters)",
			"ImLike\Forms\LoginForm::initialize-passwordMessageMin" => "Your password is too short. (Minimum is 6 characters)",
			"ImLike\Forms\LoginForm::initialize-passwordLabel" => "Password",

			"ImLike\Forms\LoginForm::initialize-csrfFailed" => "CSRF validation failed. This shouldn't normally happen. Delete your cookies and try again.",

			"ImLike\Forms\LoginForm::initialize-submitValue" => "Log in",


			"ImLike\Forms\RegisterForm::initialize-emailPlaceholder" => "Enter your email",
			"ImLike\Forms\RegisterForm::initialize-emailWrongFormat" => "The email has wrong format.",
			"ImLike\Forms\RegisterForm::initialize-emailMessageMax" => "Your email is too long. (Over 255 characters)",
			"ImLike\Forms\RegisterForm::initialize-emailMessageMin" => "The email is too short. (Minimum 3 characters)",
			"ImLike\Forms\RegisterForm::initialize-emailLabel" => "Email address",

			"ImLike\Forms\RegisterForm::initialize-usernamePlaceholder" => "Enter your username",
			"ImLike\Forms\RegisterForm::initialize-usernameMessageMax" => "Your name is too long. (Over 60 characters)",
			"ImLike\Forms\RegisterForm::initialize-usernameMessageMin" => "The username is required.",
			"ImLike\Forms\RegisterForm::initialize-usernameRegexMatch" => "Username can only contain aplhanumeric characters and '_'.",
			"ImLike\Forms\RegisterForm::initialize-usernameLabel" => "Username",

			"ImLike\Forms\RegisterForm::initialize-passwordPlaceholder" => "Enter your password",
			"ImLike\Forms\RegisterForm::initialize-passwordMessageMax" => "Your password is too long. (Over 1024 characters)",
			"ImLike\Forms\RegisterForm::initialize-passwordMessageMin" => "Your password is too short. (Minimum is 6 characters)",
			"ImLike\Forms\RegisterForm::initialize-passwordLabel" => "Password",

			"ImLike\Forms\RegisterForm::initialize-passwordRetypedPlaceholder" => "Enter your password again",
			"ImLike\Forms\RegisterForm::initialize-passwordRetypedFailed" => "Passwords don't match.",
			"ImLike\Forms\RegisterForm::initialize-passwordRetypedLabel" => "Retype your password",

			"ImLike\Forms\RegisterForm::initialize-csrfFailed" => "CSRF validation failed. This shouldn't normally happen. Delete your cookies and try again.",

			"ImLike\Forms\RegisterForm::initialize-submitValue" => "Register",

			"ImLike\Forms\ForgotPasswordForm::initialize-passwordPlaceholder" => "Enter your password",
			"ImLike\Forms\ForgotPasswordForm::initialize-passwordMessageMax" => "Your password is too long. (Over 1024 characters)",
			"ImLike\Forms\ForgotPasswordForm::initialize-passwordMessageMin" => "Your password is too short. (Minimum is 6 characters)",
			"ImLike\Forms\ForgotPasswordForm::initialize-passwordLabel" => "Password",

			"ImLike\Forms\ForgotPasswordForm::initialize-passwordRetypedPlaceholder" => "Enter your password again",
			"ImLike\Forms\ForgotPasswordForm::initialize-passwordRetypedFailed" => "Passwords don't match.",
			"ImLike\Forms\ForgotPasswordForm::initialize-passwordRetypedLabel" => "Retype your password",

			"ImLike\Forms\ForgotPasswordForm::initialize-csrfFailed" => "CSRF validation failed. This shouldn't normally happen. Delete your cookies and try again.",

			"ImLike\Forms\ForgotPasswordForm::initialize-submitValue" => "Change password",




		),
		"cz" => array(
		),
	);

	public static function getText($language, $textId){
		return self::$messages[$language][$textId];
	}
}