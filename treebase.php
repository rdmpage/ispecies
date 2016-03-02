<?php

// Fetch trees/studies from TreeBASE
require_once (dirname(__FILE__) . '/lib.php');

$type = 'study';

$q = trim($_GET['q']);

if (isset($_GET['type']))
{
	$type = $_GET['type'];
}

if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

// Names with spaces need special treatment
if (preg_match('/\s/', $q))
{
	$q = '%22' . str_replace(' ', '+', $q) . '%22';
}

$obj = new stdclass;



switch ($type)
{
	case 'one_tree':
		$url = 'http://treebase.org/treebase-web/tree_for_phylowidget/' . $q;
		$nexus = get($url);
		
		$obj->nexus = $nexus;

		break;
		
		
	case 'trees':
		$obj->items = array();
		
		// Search URL
		$url = 'http://purl.org/phylo/treebase/phylows/taxon/find?query=tb.title.taxon+%3D+' . $q . '&format=rss1&recordSchema=tree';

		$xml = get($url);

		// parse
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		$xpath = new DOMXPath($dom);

		$xpath->registerNamespace('rss', 'http://purl.org/rss/1.0/');
		$xpath->registerNamespace('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
		$xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
		$xpath->registerNamespace('dcterms', 'http://purl.org/dc/terms/');
		$xpath->registerNamespace('prism', 'http://prismstandard.org/namespaces/1.2/basic/');

		
		$items = $xpath->query ('//rss:item');
		foreach ($items as $item)
		{
			$tree = new stdclass;
			
			$nc = $xpath->query ('rss:link', $item);
			foreach ($nc as $n)
			{
				$tree->id = $n->firstChild->nodeValue;
			}			

			$nc = $xpath->query ('rss:description', $item);
			foreach ($nc as $n)
			{
				$tree->title = $n->firstChild->nodeValue;
			}

			$nc = $xpath->query ('rdfs:isDefinedBy', $item);
			foreach ($nc as $n)
			{
				$tree->isDefinedBy = $n->firstChild->nodeValue;
				
				$tree->study = str_replace('http://purl.org/phylo/treebase/phylows/study/TB2:S', '', $tree->isDefinedBy);
			}
	
	
			$obj->items[] = $tree;
						
		}
	
		break;


	case 'study':
		$obj->items = array();
		
		// Search URL
		$url = 'http://purl.org/phylo/treebase/phylows/taxon/find?query=tb.title.taxon+%3D+' . $q . '&format=rss1&recordSchema=study';

		$xml = get($url);


		// parse
		$dom = new DOMDocument;
		$dom->loadXML($xml);
		$xpath = new DOMXPath($dom);

		$xpath->registerNamespace('rss', 'http://purl.org/rss/1.0/');
		$xpath->registerNamespace('dc', 'http://purl.org/dc/elements/1.1/');
		$xpath->registerNamespace('dcterms', 'http://purl.org/dc/terms/');
		$xpath->registerNamespace('prism', 'http://prismstandard.org/namespaces/1.2/basic/');

		
		$items = $xpath->query ('//rss:item');
		foreach ($items as $item)
		{
			$study = new stdclass;
			
			$nc = $xpath->query ('rss:link', $item);
			foreach ($nc as $n)
			{
				//echo $n->firstChild->nodeValue . '<br />';
		
				$study->id = $n->firstChild->nodeValue;
			}
			

			$nc = $xpath->query ('dc:title', $item);
			foreach ($nc as $n)
			{
				//echo $n->firstChild->nodeValue . '<br />';
		
				$study->title = $n->firstChild->nodeValue;
			}
	
	
			$nc = $xpath->query ('dcterms:bibliographicCitation', $item);
			foreach ($nc as $n)
			{
				//echo $n->firstChild->nodeValue . '<br />';
		
				$study->bibliographicCitation = $n->firstChild->nodeValue;
		
			}
	
	
			$nc = $xpath->query ('prism:doi', $item);
			foreach ($nc as $n)
			{
				//echo $n->firstChild->nodeValue . '<br />';
				if (isset($n->firstChild->nodeValue))
				{
					if ($n->firstChild->nodeValue != '')
					{
						$study->doi = $n->firstChild->nodeValue;
					}
				}
			}
	
			$obj->items[] = $study;
	
					
		}
		break;
		
	default:
		break;
}



//echo '<pre>';

if ($callback != '')
{
	echo $callback . '(';
}
echo json_format(json_encode($obj));
if ($callback != '')
{
	echo ')';
}

//echo '</pre>';

?>