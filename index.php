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

$query = new SolrQuery();

$q = $_REQUEST["q"];
if (!isset($q)) {
	$q = "*";
}

$query->setQuery('title:('.$q.') OR description:('.$q.')');

$query->setStart(0);

$query->setRows(50);

$query->addField('title')->addField('id')->addField('description');
$query->addHighlightField('description');
$query->setHighlight(true);

$query_response = $client->query($query);

$response = $query_response->getResponse();

print_r($response);

?>
