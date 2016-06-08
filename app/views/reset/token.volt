<div class="container">
	<div class="form-container">
		<strong><div id="response" class="alert alert-info">
		</div></strong>
		{{ form("reset/", "method" : "post", "role" : "form", "id" : "form-forgot") }}
			<div class="form-group">
				{{ forgotForm.label('forgot-password') }}
				{{ forgotForm.render('forgot-password') }}
				<span class="glyphicon glyphicon-remove"></span>
			</div>
			<div class="form-group">
				{{ forgotForm.label('forgot-password-retyped') }}
				{{ forgotForm.render('forgot-password-retyped') }}
				<span class="glyphicon glyphicon-remove"></span>
			</div>
			{{ forgotForm.render('forgot-csrfToken', ['value' : csrfToken]) }}
			{{ forgotForm.render('forgot-resetToken', ['value' : resetToken]) }}
			{{ forgotForm.render('forgot-submit') }}
		</form>
	</div>
</div>