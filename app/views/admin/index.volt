<div id="container">
	<div class="error-container alert alert-danger closeable">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<p id="error-description"></p>
	</div>
	<div class="success-container alert alert-success closeable">
		<button type="button" class="close" aria-hidden="true">&times;</button>
		<p id="success-description"></p>
	</div>

{% if isAllowed %}

	<h1> Welcome <span class="username">{{ loggedInUsername }}</span></h1>
	<div id="globalMessageContainer">
		<span>Broadcast global message on phones.</span>
		<br>
		<textarea class="globalMessageInput"></textarea>
		<br>
		<button class="broadcastGlobalMessageButton">Broadcast</button>
	</div>

{% else %}	

	<h1> You shouldn't be here <span class="username">{{ loggedInUsername }}</span></h1>
	<a href="//{{ baseUrl }}"><button class="goBackButton">Go back</button></a>

{% endif %}	

</div>
