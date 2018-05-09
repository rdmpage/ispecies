<?php

// Wrap lifemap
require_once (dirname(__FILE__) . '/lib.php');


$ott = 253983; //Henckelia

if (isset($_GET['ott']))
{
	$ott = $_GET['ott'];
}

if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

$url = 'http://umr5558-treezoom3.univ-lyon1.fr:8983/solr/taxo/select?q=taxid:ott' . $ott . '&wt=json';


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