<link rel="stylesheet/less" type="text/css" href="/css/gate.less" />
	</head>
	<body>
		{{ content() }}
		{{ partial("partials/footer") }}
		{{ javascript_include("https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js", false) }}
		{{ javascript_include("js/bootstrap.min.js") }},
		{{ javascript_include("js/less.min.js") }}
	</body>
</html>