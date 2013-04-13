<?php
require 'Basecamp/Basecamp.php';

$appName = 'basecamp_dashboard';
$appContact = 'gunther@groenewege.com';

/**
 * CHANGE THESE SETTINGS TO YOUR BASECAMP ACCOUNT SETTINGS
 */
$basecampAccountId = 'xxxxxxx';
$basecampUsername = 'xxxxxxx';
$basecampPassword = 'xxxxxxx';

$basecamp = new Basecamp\Basecamp($appName, $appContact, $basecampAccountId, $basecampUsername, $basecampPassword);

try {
    $recent_events = $basecamp->getEvents();
} catch (Exception $e) {
    die($e->getMessage());
}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Cache-control" content="no-cache" />
	<title>Tasks</title>
	<style type="text/css">
		@font-face {
			font-family: "Roadgeek2005SeriesD";
			src: url("http://panic.com/fonts/Roadgeek 2005 Series D/Roadgeek 2005 Series D.otf");
		}
		body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td {
			margin: 0;
			padding: 0;
		}
		fieldset,img {
			border: 0;
		}
		#main{
			overflow: hidden;
		}
		body {
			font-family: 'Roadgeek2005SeriesD', sans-serif;
			color: white;
		}
		body, html, #main
		{
			background: transparent !important;
		}
		#events_container {
			padding: 16px;
			text-align: center;
		}
		h2 {
			width: 180px;
			margin: 0px auto;
			padding-bottom: 16px;
			font-size: 24px;
			font-weight: normal;
			line-height: 24px;
			color: #ace1fa;
			text-transform: uppercase;
		}
		.event {
			margin-bottom: 16px;
		}
		p {
			padding-left: 42px;
			font-size: 18px;
			line-height: 1.4;
			font-weight: normal;
			color: #fff;
			text-align: left;
		}
		.creator {
			color: #ace1fa;
		}
		.excerpt {
			font-size: 16px;
			color: #7e7e7e;
		}
		a {
			color: #fff;
			text-decoration: none;
		}
		.avatar {
			border-radius: 100px;
			vertical-align: top;
			float: left;
		}
	</style>
	<script type="text/javascript">
		function init()
		{
			// Change page background to black if the URL contains "?desktop", for debugging while developing on your computer
			if (document.location.href.indexOf('desktop') > -1)
			{
				document.getElementById('events_container').style.backgroundColor = 'black';
			}
		}
	</script>
</head>
<body onload="init()">
	<div id="main">
		<div id="events_container">
			<h2>Basecamp</h2>
			<?php foreach ($recent_events as $event): ?>
				<div class="event">
					<img class="avatar" height="30" width="30" src="<?php echo $event->creator['avatar_url'] ?>" alt="avatar">
					<p>
						<span class="creator"><?php echo $event->creator['name'] ?></span>
						<a href="<?php echo $event->html_url ?>"><?php echo $event->summary ?></a>
						<br />
						<span class="excerpt"><?php echo $event->excerpt ?></span>
					</p>
				</div>
			<?php endforeach ?>
		</div>
	</div>
</body>
</html>