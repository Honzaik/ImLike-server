<div class="post" data-to-load="{{ post.imageUrl }}" id="{{ post.postCode }}" data-post-user-id="{{ post.userId }}" >
	<img class="post lazy" data-original="{{ post.imageUrl }}" src="ajax-loader2.gif">
	<div class="header hideable">
		<div class="left">
			<div><a class="user" target="_blank" href="//{{ baseUrl }}/u/{{ post.username }}">{{ post.username }}</a> was like</div>
		</div>
		<div class="right">
			<div><a href="//{{ baseUrl }}/p/{{ post.postCode }}" target="_blank">{{ post.createdAt }} ago</a></div>
		</div>
	</div>
	<div class="clickable"></div>
	<div class="post-footer hideable">
		<div class="caption">{{ post.caption }}</div>
		<div class="metadata {% if post.hasLiked %} imliked {% endif %}" data-postCode={{ post.postCode }}>
			<div class="comments"><span class="comments-count">{{ post.commentsCount }}</span><a href="http://{{ baseUrl }}/p/{{ post.postCode }}" target="_blank"><span class="logo"></span></a></div>
			<div class="imlikes"><span class="imlikes-count">{{ post.likesCount }}</span><span class="logo"></span></div> 
		</div> 
	</div>
</div>