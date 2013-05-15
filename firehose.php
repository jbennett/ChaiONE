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
<html class="nojs">
<head>
	<title>Drinking from the ADN Firehose</title>
	<style>
 	/* FIXME: Move this to a stylesheet */
 	#wrapper {
 		position: relative;
 		max-width: 960px;
 		margin: 0 auto;
 		padding: 1em 10px;
 	}

 	.refreshButton {
 		width: 100%;

 		font-size: 1.5em;
 		background: #ececec;
 		border: 1px solid #ddd;
 		cursor: pointer;
 	}
	.refreshButton:hover { background: #ddd; }
	.refreshButton:active { background: #aaa; }
	.nojs .refreshButton { display: none; } /* only allow JS triggering, could do a page refresh as fallback or something */

	.split { border-top: 1px solid red; } /* subtle */
	</style>
</head>

<body>
	<div id="wrapper">
		<h1>ADN Firehose</h1>
		<button class="refreshButton">Refresh</button>
		<ol class="posts">
			<? foreach ($posts as $post): ?>
			<li class="post" data-id="<?= $post->id ?>">
				<div class="username"><?= $post->user->username ?></div>
				<div class="message"><?= $post->html ?></div> <!-- trusting ADN here... -->
			</li>
			<? endforeach; ?>
		</ol>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<script> // NOTE: Inline scripts are bad, this is a simple demo though

	(function() {
		var loading = false,
			url = '<?= $globalURL ?>?since_id=',
			$posts = $('.posts'),
			highwater = $posts.find('.post').first().data('id'),

			createPost = function(username, message, id) {
				return ($('<li class="post" data-id="'+ id +'">\
						<div class="username">'+ username +'</div>\
						<div class="message">'+ message +'</div>\
					</li>')); // use template in full app
			};

		// show refresh button etc
		$('html').removeClass('nojs');

		$(document).on('click', '.refreshButton', function() {
			if (!loading) { // protected from multiple refreshes
				loading = true;
				$button = $(this);
				$button.text('Loading...');

				$.ajax(url + highwater, {
					success: function(data, text, xhr) {
						var nodes = []
						if (data.data && data.data.length) { // make sure
							data.data.forEach(function(post) {
								// push new posts onto array
								nodes.push(createPost(post.user.username, post.html, post.id));
							});

							// mark top of list before adding more
							$posts.find('.post').removeClass('split').first().addClass('split');

							highwater = data.meta.max_id; // update highwater mark to ensure we don't try to load old posts
							$posts.prepend(nodes); // add nodes to page
						}
					},
					complete: function(xhr, text) {
						loading = false;
						$button.text('Refresh');
					}
				});
			}
		});
	}());
	</script>
</body>
</html>