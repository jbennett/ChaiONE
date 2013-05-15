<?php
// "Github rockstars"
// Consume the Github API and find out who were the most active
// contributors to Rails by month for the past 6 months. Should display top 3
// for each months.

$date = date('Y-m-d', strtotime('6 months ago'));
$request = 'https://api.github.com/repos/rails/rails/stats/contributors';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't echo
curl_setopt($ch, CURLOPT_USERAGENT, 'JONS GITHUB CRAWLER'); // quit yelling at me Github
$content = curl_exec($ch);
curl_close($ch);

$content = utf8_encode($content);
$json = json_decode($content);

$stats = array();


foreach ($json as $contributor) {
	$monthly = array();

	foreach ($contributor->weeks as $week) {
		$month = date('m', $week->w);

		if (!isset($monthly[$month])) {
			$monthly[$month] = 0;
		}

		$monthly[$month] += $week->c; // using commits as the measure of active
	}

	foreach ($monthly as $index => $month) {
		if (!isset($stats[$index])) {
			$stats[$index] = array();
		}

		// for each month, insert the authors name at index specified by their commit count (FIXME: overwriting)
		$stats[$index][$monthly[$index]] = $contributor->author->login;
	}
}



echo '<pre>';
print_r($stats);
echo '</pre>';