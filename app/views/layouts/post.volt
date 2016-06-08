<link rel="stylesheet/less" type="text/css" href="/css/post.less" />
<link rel="stylesheet/less" type="text/css" href="/css/singlePost.less" />
		<!--<meta property="fb:app_id" content="137206539707334" />
		<meta name="og:image" content="{{ post.imageUrl }}" />
		<meta name="og:title" content="{{ post.username}}" />
		<meta name="og:url" content="https://www.flickr.com/photos/lmslad/15717912990/"  data-dynamic="true">
		<meta name="og:site_name" content="DERP" />
		<meta name="og:type" content="flickr_photos:photo"  data-dynamic="true">
		<meta name="og:description" content="{{ post.caption }}" />
		-->
    <meta property="og:title" content="" />
    <meta property="og:image" content="http://distilleryimage0.ak.instagram.com/06b17a968d8e11e28e2022000a1cdd10_7.jpg" />
    <meta property="og:url" content="http://honzaik.eu/" />
	</head>
	<body>
		{{ partial("partials/userBarFluid") }}
		{{ content() }}
		{{ partial("partials/footer") }}
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		{{ javascript_include("js/jquery.lazyload.min.js") }}
		{{ javascript_include("js/bootstrap.min.js") }}
		{{ javascript_include("js/less.min.js") }}
		{{ javascript_include("js/plugins.js") }}
		{{ javascript_include("js/posts.js") }}
		{{ javascript_include("js/navBar.js") }}
	</body>
</html>