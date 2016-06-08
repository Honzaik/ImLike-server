<link rel="stylesheet/less" type="text/css" href="/css/welcomePage.less" />
	<meta property="og:image" content="" />
	<meta property="og:title" content="ImLike" />
	<meta property="og:site_name" content="DERP" />
	<meta property="og:description" content="Welcome to ImLike.in" />
	</head>
	<body>
		{{ partial("partials/userBarFluid") }}
		{{ content() }}
		{{ partial("partials/footer") }}
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		{{ javascript_include("js/bootstrap.min.js") }}
		{{ javascript_include("js/less.min.js") }}
		{{ javascript_include("js/plugins.js") }}
		{{ javascript_include("js/navBar.js") }}
	</body>
</html>