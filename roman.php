<?php
// "Roman Numerals"
// Write a program that lists out numbers from 1 to 100 and their roman numeral equivalent values. (Assume 4 is "IV" not "IIII")

function toRoman($int) {
	$int = intval($int); // making sure
	$roman = '';

	$romanDigits = array(
		'M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1,
	);

	foreach ($romanDigits as $char => $value) {
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