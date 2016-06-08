<div class="container">
	<div class="form-container">
		<strong><div id="response" class="alert alert-info">
		</div></strong>
		<nav class="navbar navbar-login">
			<div class="container-fluid">
				<div class="row">
					<div class="col-xs-6 col-md-6 col-sm-6">
						<a href="#login" id="button-login" class="btn btn-primary btn-lg btn-block btn-choice">Login</a>
					</div>
					<div class="col-xs-6 col-md-6 col-sm-6">
						<a href="#register" id="button-register" class="btn btn-primary btn-lg btn-block btn-choice">Register</a>
					</div>
				</div>
			</div>
		</nav>
		{{ form("api/login", "method" : "post", "role" : "form", "id" : "form-login") }}
			<div class="form-group">
				{{ loginForm.label('login-email') }}
				{{ loginForm.render('login-email') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			<div class="form-group">
				{{ loginForm.label('login-password') }}
				{{ loginForm.render('login-password') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			{{ loginForm.render('login-csrf', ['value' : token]) }}
			{{ loginForm.render('login-submit') }}
		</form>
		{{ form("api/register", "method" : "post", "role" : "form", "id" : "form-register") }}
			<div class="form-group">
				{{ registerForm.label('register-email') }}
				{{ registerForm.render('register-email') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			<div class="form-group">
				{{ registerForm.label('register-username') }}
				{{ registerForm.render('register-username') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			<div class="form-group">
				{{ registerForm.label('register-password') }}
				{{ registerForm.render('register-password') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			<div class="form-group">
				{{ registerForm.label('register-password-retyped') }}
				{{ registerForm.render('register-password-retyped') }}
				<span class="glyphicon glyphicon-ok"></span>
			</div>
			{{ registerForm.render('register-csrf', ['value' : token]) }}
			{{ registerForm.render('register-submit') }}
		</form>
	</div>
</div>