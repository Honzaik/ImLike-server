<link rel="stylesheet/less" type="text/css" href="/css/home.less" />
	<link rel="stylesheet/less" type="text/css" href="/css/post.less" />
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