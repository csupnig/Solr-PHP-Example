<html>
<head>
	<title>Search Example</title>
	<style type="text/css">
		.ac_results ul {list-style-type:none; margin:0px; padding:0px;}
		.ac_results ul li {	
			background-color: #EAF2F6;
		}
		.ac_results ul li.ac_over {
			background-color: #FCF8DC;
		}
		ul.tarn { list-style-type:none; width: 600px; }
		ul.tarn li {	
			margin: 2px 0px 0px 0px;
			padding: 5px;
			background-color: #F4F4F4;
		}
		h1, a {
			color: #09C;
			text-decoration: none;
		}
		h1 {
			font-style: italic;
		}
		a:hover {
			text-decoration: underline;
		}
		p.info {
			font-size: 80%;
			font-style: italic;
		}
		.highlight{ color:orange; }
	</style>
</head>
<body>
<h1>Search example</h1>
<div class="form">
<form action="index.php" method="post">
	Query: <input type="text" name="q" id="input_search"/> <input type="submit" name="submit" value="Go"/>
</form>
<p class="info">Try out querying with lucene. You can use the <a href="http://lucene.apache.org/core/old_versioned_docs/versions/3_0_0/queryparsersyntax.html" target="_blank">query syntax</a> in the field above.</p>
</div>
<div class="result">
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
	
	if (isset($q) && $q != "") {
		$query->setQuery($q);
		

		$query->setStart(0);

		$query->setRows(50);

		$query->addField('title')->addField('id')->addField('description')->addField('links');
		$query->addHighlightField('description');
		$query->setHighlightSimplePre('<span class="highlight">');
		$query->setHighlightSimplePost('</span>');
		$query->setHighlight(true);

		$query_response = $client->query($query);

		$response = $query_response->getResponse();
		if (isset($response["response"]["numFound"]) && $response["response"]["numFound"] > 0) {
			//Calculate result info
			$start = $response["response"]["start"];
			$total = $response["response"]["numFound"];
			$count = count($response["response"]["docs"]);
			?>
			<p id="result_report">
      			Showing <?=$start; ?> - <?=($start + $count) ?> of <?=$total; ?> results.
			</p>
			<ul id="results" class="tarn">
			<?
			//List the search results
			foreach ($response["response"]["docs"] as $doc) {
				//title is a multivalue field
				$title = $doc["title"][0];
				//links is a mutlivalue field
				$link = $doc["links"][0];
				//fetch the document id
				$key = $doc["id"];

				//check for highlighting and replace description
				$description = $doc["description"];
				if (isset($response["highlighting"][$key]) && count($response["highlighting"][$key]["description"]) > 0) {
					$description = $response["highlighting"][$key]["description"][0];
				}

				?>
				<li>
						<h2><a class="title" href="<?=$link; ?>"><?=$title;?></a></h2>
						<p class="description"><?=$description;?>
						<br />
						<a title="<?=$title;?>" class="title" href="<?=$link; ?>"></a>
						</p>
				</li>
				<?
			}
			?>
			</ul>
			<?

		} else {
			?>
			<div id="result_report">Sorry. We could not find what you where looking for. Try a different query!</div>
			<?
		}
	}

	?>
</div>
</body>
</html>