<?php

include "bootstrap.php";

$options = array
(
    'hostname' => SOLR_SERVER_HOSTNAME,
    'login'    => SOLR_SERVER_USERNAME,
    'password' => SOLR_SERVER_PASSWORD,
    'port'     => SOLR_SERVER_PORT,
);

$client = new SolrClient($options);

$doc = new DOMDocument();
$contents = file_get_contents('http://www.rottentomatoes.com/syndication/rss/in_theaters.xml');
$doc->loadXML($contents);
$arrFeeds = array();
$id = 0;
foreach ($doc->getElementsByTagName('item') as $node) {
	$itemRSS = array ( 
	'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
	'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
	'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
	'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
	);
	$doc = new SolrInputDocument();

	$doc->addField('id', $id++);
	$doc->addField('title', $itemRSS['title']);
	$doc->addField('description', $itemRSS['desc']);
	$doc->addField('links', $itemRSS['link']);
	

	$updateResponse = $client->addDocument($doc);
}
$updateResponse = $client->commit();

print_r($updateResponse->getResponse());
?>
