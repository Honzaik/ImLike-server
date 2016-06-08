<div class="container-fluid">
	<div class="error-container alert alert-danger closeable">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<p id="error-description"></p>
	</div>
	<div class="success-container alert alert-success closeable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p id="success-description"></p>
	</div>
	<div id="wrapper">
		{% if !hasPosts %} 
			<div class="no-posts">Nothing here yet :(</div>
		{% endif %}	
		{% for post in posts %}
			{{ partial("partials/post") }}
		{% endfor %}
	</div>
</div>