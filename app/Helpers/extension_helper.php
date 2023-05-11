<?php

function uri_segment($index){
   $uri = explode("/", uri_string(true));

   return count($uri) > $index ? $uri[$index] : '';
}

function groupby($datas, $by){
	$array = [];

	foreach ($datas as $data) {
		$array[$data->$by][] = $data;
	}

	return $array;
}

function unwrap_null($var, $default){
	return isset($var) === TRUE ? $var : $default;
}