{% if loggedInUsername %}
<div class="search-bar">
	Search for users: <input type="text" class="searchInput" /><button type="button" class="searchBtn"></button>
</div>
{% endif %}
<div class="login-info">
	{% if loggedInUsername %}
		You are logged in as {{ link_to("u/" ~ loggedInUsername, loggedInUsername, "class":"username", "data-user-id":loggedInUserId, "data-user-token":loggedInApiToken) }} {{ link_to("api/logout", "Logout", "class":"btn btn-login" ) }}
	{% else %}
		You are not logged in  {{ link_to("gate/", "Login / Register", "class":"btn btn-login") }}
	{% endif %}
</div>