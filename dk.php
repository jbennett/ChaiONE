<?php

// "DonkeyKong"
// Print out numbers from 1 to 100. When the number is divisible by three, write "Donkey" instead of the number.
// If it is divisible by five, write "Kong" instead of the number. For multiples of 3 and 5, write "DonkeyKong".

$start = 1;
$end = 100;

for ($i = $start; $i <= $end; $i++) {

	if ($i % 3 == 0 && $i % 5 == 0) {
		echo "DonkeyKong<br/>";
	} else if ($i % 3 == 0) {
		echo "Donkey<br/>";
	} else if ($i % 5 == 0) {
		echo "Kong<br/>";
	} else {
		echo "$i<br/>";
	}

}