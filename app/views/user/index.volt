<div class="container">
	<div class="error-container alert alert-danger closeable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p id="error-description"></p>
	</div>
	<div class="success-container alert alert-success closeable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<p id="success-description"></p>
	</div>
	<div class="row userInfo">
		<div class="col-xs-6 col-md-1 image">
			<img class="holder img-circle " src='{{ profileImageUrl }}'>
			<button class="hidden change-image">Change image</button><input id="image-chooser" class="hidden" type="file">
		</div>
		<div class="col-xs-6 col-md-1 follow">
		{% if loggedInUsername == profileUsername %}
			<button class="vertCent edit-profile">Edit profile</button>
		{% else %}
			{% if isFollowing %}
				<button data="{{ userId }}" class="vertCent unfollow-button">Unfollow</button>
			{% else %}	
				<button data="{{ userId }}" class="vertCent follow-button">Follow</button>
			{% endif %}	
		{% endif %}	
		</div>
		<div class="col-xs-12 col-md-6 user-info">
			<div class="vertCent user-desc">
				<span class="profile-username fontResize" data-user-id="{{ userId }}">{{ profileUsername }}</span><span class="user-text">{{ userDesc }}</span>
				<button class="hidden user-text-confirm">Confirm edit</button>
				<textarea class="hidden user-text-input"></textarea>
			</div>
		</div>
		<div class="col-xs-12 col-md-4 extra-info">
			<table class="extra-info-table">
				<tr><td class="value">{{ postsCount }}</td><td class="value followers">{{ followersCount }}</td><td class="value following">{{ followingCount }}</td></tr>
				<tr><td class="title">posts</td><td class="title">followers</td><td class="title">following</td></tr>
			</table>
		</div>
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