
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Monster Monday Messenger - DKZ.io</title>

	<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.5/yeti/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#">Monster Monday Messenger</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav"></ul>
		</div><!--/.nav-collapse -->
	</div>
</nav>

<div class="container">

	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<h2 class="text-center">Available Commands:</h2>
			<p><code>list</code> Shopping list for Monster Monday</p>
			<p><code>next</code> Who's up next to buy</p>
			<p><code>broadcast: <em>message</em></code> Text everyone</p>
			<hr>
		</div>
	</div>
	<?php
		require_once(__DIR__.'/../classes/MonsterMonday.php');
		date_default_timezone_set('America/Chicago');
		$mm = new MonsterMonday();
		$messages = $mm->getNewestMessages();
	?>
	<div class="content">
		<?php if(count($messages)): foreach($messages as $single): ?>
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="testimonials">
						<div class="active item">
							<blockquote>
								<p><?=$single['message'];?></p>
								<h5 class="">
									<span class="testimonials-name"><?=$mm->getNameByNumber($single['phone']);?></span>
									<small>Sent <?= date('Y-m-d H:i:s',strtotime($single["timestamp"]));?></small>
								</h5>
							</blockquote>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; else: ?>
			<h3 class="text-center">No recent messages!</h3>
		<?php endif; ?>
	</div>
</div><!-- /.container -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<style>
	body{padding-top:50px}

	/* Content */
	.content {
		padding-top: 30px;
	}

	/* Testimonials */
	.testimonials blockquote {
		background: #f8f8f8 none repeat scroll 0 0;
		border: medium none;
		color: #666;
		display: block;
		font-size: 14px;
		line-height: 20px;
		padding: 15px;
		position: relative;
	}
	.testimonials blockquote::before {
		width: 0;
		height: 0;
		right: 0;
		bottom: 0;
		content: " ";
		display: block;
		position: absolute;
		border-right: 0 solid transparent;
		border-bottom: 20px inset #fff;
		border-left: 15px inset transparent;
	}
	.testimonials blockquote::after {
		width: 0;
		height: 0;
		right: 0;
		bottom: 0;
		content: " ";
		display: block;
		position: absolute;
		border-style: solid;
		border-width: 20px 20px 0 0;
		border-color: #e63f0c transparent transparent transparent;
	}
	.testimonials span.testimonials-name {
		color: #e6400c;
		font-size: 16px;
		font-weight: 300;
		margin: 23px 0 7px;
	}
	/* http://bootsnipp.com/snippets/featured/responsive-simple-testimonials */
</style>

</body>
</html>
