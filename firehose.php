<?php
// "App.Net"
// Consume the public feed and print out a simple list of
// posts displaying username & message.

$globalURL = 'https://alpha-api.app.net/stream/0/posts/stream/global';
$content = file_get_contents($globalURL);
$content = utf8_encode($content);
$json = json_decode($content);

echo '<pre>';
print_r($json);
echo '</pre>';