<?php
// Taken straight from laravel
function array_except($array, $keys)
{
	return array_diff_key( $array, array_flip((array) $keys) );
}