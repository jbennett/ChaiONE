<?php
// "Roman Numerals"
// Write a program that lists out numbers from 1 to 100 and their roman numeral equivalent values. (Assume 4 is "IV" not "IIII")

function toRoman($int) {
	$int = intval($int); // making sure
	$roman = '';

	$romanDigits = array(
		1000 => 'M',
		500 => 'D',
		100 => 'C',
		50 => 'L',
		10 => 'X',
		5 => 'V',
		1 => 'I',
	);

	foreach ($romanDigits as $value => $char) {
		$matches = intval($int / $value);

		$roman .= str_repeat($char, $matches);
		$int = $int % $value;
	}

	return $roman;
}

$tests = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 14, 15, 16, 19, 20, 21, 100, 2013);

foreach ($tests as $value) {
	echo $value .' -> '. toRoman($value) .'<br/>';
}