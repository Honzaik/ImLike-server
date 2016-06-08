<div class="container">
	<div class="error-container alert alert-danger closeable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p id="error-description"></p>
	</div>
	<div class="success-container alert alert-success closeable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p id="success-description"></p>
	</div>
	<div id="wrapper">
		{{ partial("partials/post") }}
		{% if showDelete %}
		<div class="delete-cont">
			<button type="button" class="btn btn-danger delete-btn">Delete this post</button>
			<button type="button" class="btn btn-success imsure-delete-btn hidden">I'm sure</button>
			<button type="button" class="btn btn-warning cancel-delete-btn hidden">I changed my mind</button>
		</div>
		{% endif %}
		{% if loggedIn %}
			<div id="post-comment">
				<textarea class="form-control comment-form" rows="3"></textarea>
				<button type="button" class="btn btn-success post-comment-btn" disabled="disabled">Post comment</button>
			</div>
		{% endif %}
		<div id="comments-container">
			{% for comment in comments %}
				<div class="comment-container">
					<div class="comment-header">
						<a href="http://{{ baseUrl }}/u/{{ comment.username }}" target="_blank"><span class="comment-username">{{ comment.username }}</span></a>
						<span class="comment-created-at">{{ comment.createdAt }}</span>
					</div>
					<div class="comment-content">
						{{ comment.content }}
					</div>
				</div>
			{% endfor %}
		</div>
		<button id="loadMoreButton" type="button" class="btn btn-success load-more-button">Load more</button>
	</div>
</div>