<?php

// Wrap EOL images
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

$eol = 0;

$url = 'http://eol.org/api/search/1.0/' . urlencode($q) . ".json";

$json = get($url);

if ($json != '')
{
	$obj = json_decode($json);
	
	
	
	foreach ($obj->results as $result)
	{
		$title = $result->title;
		
		$title = preg_replace('/\s+\(?\w+(-\w+)?, [0-9]{4}\)?$/', '', $title);
		if ($title == $q)
		{
			$eol = $result->id;
		}
	}
}

$json = '';

if ($eol != 0)
{
	$url = 'http://eol.org/api/pages/1.0/' . $eol . ".json?details=1&images=10";

	$json = get($url);
}

//echo $eol;

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