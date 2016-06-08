<div id="header" class="navbar">
	<div class="bigLogo container-fluid">
		<img id="logo" src="/gfx/icon_128_r.png">
	</div>
</div>
<div class="container description">
	<div class="biggest">ImLike</div>
	<div>is a great mobile application which allows you to take a photo of something cool with yourself in it too!</div>
	<div class="row facts">
		<div class="col-xs-12 col-sm-12 col-md-4 fact">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title">Hm yay! ... but how it differs from a regular "selfie"?</h3>
			  </div>
			  <div class="panel-body">
			    ImLike is different by that it let's you use both of your phone's cameras.
			    <ul>
			    	<li>The main camera is used to capture the primary cool thing.</li>
			    	<li>And the secondary camera is used to capture yourself with it!</li>
			    </ul>
			    So basically the main benefit in my opinion is that you won't look like a wierdo doing a "selfie".
			  </div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 fact">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title">Why is the app called "ImLike"?</h3>
			  </div>
			  <div class="panel-body">
			    The idea came from a situation where, for example, you see something and <b>you are like</b> "expression on your face".<br>
			    So that brings another awesome use of this app, you can easily express your opinion on something with your face next to it. Basically react to it.
			  </div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-4 fact">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title">So I take a photo with my face on it and what is the next step?</h3>
			  </div>
			  <div class="panel-body">
			    My first idea was that this will be just an app for creating these kinds of photos. You would take a photo and share it on Facebook or Twitter.<br>But then I was like (^^) nah, I want a place where I could find all of my friends' "ImLikes" (yes, that's how I call them).<br>
			    So I've decided to create a website alongside the app. That doesn't mean that you won't be able to share these "ImLikes". You can easily share all your ImLikes on Facebook, Twitter and other social media.
			  </div>
			</div>
		</div>
	</div>
	<div class="row facts">
		<div class="col-xs-12 col-sm-12 col-md-4 fact">
			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h3 class="panel-title">Awesome! Where can I get it?</h3>
			  </div>
			  <div class="panel-body">
			    <div>Unfortunately the app is only available at Google Play. Of course I have planned an iOS/Windows Phone version but at the moment I don't have resources for that. It quite depends on a success at Android platform.</div>
			    <a href="https://play.google.com" target="_blank"><div class="android-download">
			    	<img id="android-logo" src="/gfx/glyphicons_social_48_purple_big.png"> <b>Google Play</b>
			    </div></a>
			  </div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-8 fact">
			<div id="carousel-wrapper">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
				  <!-- Indicators -->
				  <ol class="carousel-indicators">
				    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
				    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
				    <li data-target="#carousel-example-generic" data-slide-to="2"></li>
				    <li data-target="#carousel-example-generic" data-slide-to="3"></li>
				  </ol>

				  <!-- Wrapper for slides -->
				  <div class="carousel-inner" role="listbox">
				    <div class="item active">
				      <img src="/gfx/presentation/welcome_page.png" alt="...">
				    </div>
				    <div class="item">
				      <img src="/gfx/presentation/camera_2.png" alt="">
				    </div>
				    <div class="item">
				      <img src="/gfx/presentation/camera_1.png" alt="">
				    </div>
				    <div class="item">
				      <img src="/gfx/presentation/interact.png" alt="">
				    </div>
				    Some screenshots
				  </div>

				  <!-- Controls -->
				  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
				    <span class="glyphicon glyphicon-chevron-left"></span>
				    <span class="sr-only">Previous</span>
				  </a>
				  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
				    <span class="glyphicon glyphicon-chevron-right"></span>
				    <span class="sr-only">Next</span>
				  </a>
				</div>
			</div>
		</div>
	</div>
	<div class="row statistics">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title">Statistics</h1>
			</div>
			<div class="panel-body">
				<div class="col-xs-4">
					<p>Registered users</p>
					{{ numberRegisteredUsers }}
				</div>
				<div class="col-xs-4">
					<p>ImLikes posted</p>
					{{ numberImLikes }}
				</div>
				<div class="col-xs-4">
					<p>IDK</p>
					<br>
				</div>
			</div>
		</div>
	</div>
</div>