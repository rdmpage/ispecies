<?php

// Wrap DBPedia
require_once (dirname(__FILE__) . '/lib.php');


$q = 'Pinnotheres';

if (isset($_GET['q']))
{
	$q = $_GET['q'];
}

if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

$q = str_replace(' ', '_', $q);

$url = 'http://dbpedia.org/data/' . $q . '.json';


$json = get($url);


if ($callback != '')
{
	echo $callback . '(';
}
echo $json;
if ($callback != '')
{
	echo ')';
}

//echo '</pre>';

?>