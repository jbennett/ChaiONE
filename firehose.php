<!DOCTYPE HTML>
<?php
// "App.Net"
// Consume the public feed and print out a simple list of
// posts displaying username & message.

$globalURL = 'https://alpha-api.app.net/stream/0/posts/stream/global';
$content = file_get_contents($globalURL);
$content = utf8_encode($content);
$json = json_decode($content);
$posts = $json->data;
?>
<html>
<head>
	<title>Drinking from the ADN Firehose</title>
	<style>
 	/* FIXME: Move this to a stylesheet */
 	#wrapper {
 		max-width: 960px;
 		margin: 0 auto;
 		padding: 1em 10px;
 	}
	</style>
</head>

<body>
	<div id="wrapper">
		<h1>ADN Firehose</h1>
		<ol class="posts">
			<? foreach ($posts as $post): ?>
			<li class="post">
				<div class="username" data-username="<?= $post->user->username ?>"><?= $post->user->username ?></div>
				<div class="message"><?= $post->html ?></div> <!-- trusting ADN here... -->
			</li>
			<? endforeach; ?>
		</ol>
	</div>
</body>
</html>