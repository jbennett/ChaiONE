<?php
// "Github rockstars"
// Consume the Github API and find out who were the most active
// contributors to Rails by month for the past 6 months. Should display top 3
// for each months.

$startDate = strtotime('6 months ago');
$endDate = mktime(0,0,0, date('m'), 1, date('Y'));
$request = "https://api.github.com/repos/rails/rails/stats/contributors";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $request);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // don't echo
curl_setopt($ch, CURLOPT_USERAGENT, 'JONS GITHUB CRAWLER'); // quit yelling at me Github
$content = curl_exec($ch);
curl_close($ch);

$content = utf8_encode($content);
$json = json_decode($content); // get json

$stats = array();
foreach ($json as $contributor) {
	$monthly = array();

	// agregate weekly commits by month
	foreach ($contributor->weeks as $week) {
		if ($week->w < $startDate || $week->w > $endDate) {
			continue;
		}

		$month = date('F', $week->w);

		if (!isset($monthly[$month])) {
			$monthly[$month] = 0;
		}

		$monthly[$month] += $week->c; // using commits as the measure of active, might be better to do additions + deletions
	}

	// update stats.
	foreach ($monthly as $index => $month) {
		if (!isset($stats[$index])) {
			$stats[$index] = array();
		}

		// for each month, insert the authors name at index specified by their commit count (FIXME: overwriting)
		$stats[$index][$contributor->author->login] = $monthly[$index];
	}
}

foreach ($stats as $month => $contributors) {
	arsort($contributors);

	echo "<p>Top Contributors for $month:</p>";
	echo "<ul>";

	foreach(array_slice($contributors, 0, 3) as $contributor => $amount) {
		echo "<li>$contributor ($amount)</li>";
	}

	echo "</ul>";

}