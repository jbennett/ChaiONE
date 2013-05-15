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
$contributors = json_decode($content); // get json

$stats = array(); // $stats['may']['jonbca'] = 123;
foreach ($contributors as $contributor) {

	// agregate weekly commits by month
	foreach ($contributor->weeks as $week) {
		if ($week->w < $startDate || $week->w > $endDate) {
			continue;
		}

		$month = date('F', $week->w);

		if (!isset($stats[$month])) {
			$stats[$month] = array();
		}

		if (!isset($stats[$month][$contributor->author->login])) {
			$stats[$month][$contributor->author->login] = 0;
		}

		$stats[$month][$contributor->author->login] += $week->c; // using commits as the measure of active, might be better to do additions + deletions
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