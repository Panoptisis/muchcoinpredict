<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Next Doge Coin Block Value</title>

		<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
		<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.0/css/bootstrap.css" rel="stylesheet">
		<link href="/css/doge.css" rel="stylesheet">

		<!--[if lt IE 9]>
			<script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="container text-center">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<img src="/img/doge.png" alt="Doge Coin Logo">
					<h2>Next Block Value</h2>
					<h1 id="next-value">Loading...</h1>

					<hr>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3 col-md-offset-3">
					<b>Next Height</b><br>
					<span id="next-height">&nbsp;</span>
				</div>
				<div class="col-md-3 hash-output">
					<b>Current Hash</b><br>
					<span id="previous-hash">&nbsp;</span>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-md-offset-3 hash-output">
					<hr>

					<b>Much donate</b><br>
					DGt1jovkXebFKrbrKq2o6NoiGsEekz3kLc
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<hr>

					<b>A note on accuracy</b><br>
					This calculator uses a slightly different version of the Mersenne
					Twister random number generator than the one used in the real dogecoin
					program. That being said, this page should be reasonably accuate.
				</div>
			</div>
		</div>

		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.0/js/bootstrap.min.js"></script>
		<script>
			$(function()
			{
				var nextValue = $('#next-value');
				var nextHeight = $('#next-height');
				var previousHash = $('#previous-hash');

				var fetchData = function(data)
				{
					nextValue.html("&#x110;" + data.prettyValue);
					nextHeight.html(data.height);
					previousHash.html(data.hash);

					setTimeout(function()
					{
						$.getJSON('/wow.php', fetchData);
					}, 60000);
				};

				$.getJSON('/wow.php', fetchData);
			});
		</script>
	</body>
</html>
