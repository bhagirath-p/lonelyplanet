<?php
	$taxonomy_content    = file_get_contents($argv[1]);
	$destination_content = file_get_contents($argv[2]);
	$taxonomy_xml_object = simplexml_load_string($taxonomy_content) or die("Error: Cannot create object");
	$destination_xml_object = simplexml_load_string($destination_content) or die("Error: Cannot create object");

	print_destination($taxonomy_xml_object->taxonomy->node, $taxonomy_xml_object->taxonomy, $destination_xml_object);

	function print_destination($taxonomy_object, $parent_object, $destination_xml_object) {
		$parent = $parent_object;
		if(isset($taxonomy_object->node)) {
			$destination = $taxonomy_object->node_name;
			$parent_destination = $parent->node_name;
			foreach ($taxonomy_object->node as $key => $value) {
				print_destination($value, $taxonomy_object, $destination_xml_object);
			}
		}
		else {
			$destination = $taxonomy_object->node_name;
			$parent_destination = $parent->node_name;
			$attributes = $taxonomy_object->attributes();
			$atlas_node_id = $taxonomy_object->attributes()->atlas_node_id;
			build_webpage($atlas_node_id, $destination_xml_object);
		}
	}

	function build_webpage($id, $destination_xml_object) {
		$taxonomy_atlas_id = $id;
		$destination_item = null;
		foreach($destination_xml_object as $key => $value) {
			$destination_atlast_id = $value->attributes()->atlas_id;
			if(strcmp($taxonomy_atlas_id,$value->attributes()->atlas_id) == 0){
				$destination_title = $value->attributes()->title;
				$destinationfile = fopen($taxonomy_atlas_id.".html", "w") or die("Unable to open file!");
				$destination = 'test';
				$txt =
				'<!DOCTYPE html>
				 <html>
				   <head>
				     <meta http-equiv="content-type" content="text/html; charset=UTF-8">
				     <title>Lonely Planet</title>
				     <link href="static/all.css" media="screen" rel="stylesheet" type="text/css">
				   </head>

				   <body>
				     <div id="container">
				       <div id="header">
				         <div id="logo"></div>
				         <h1>Lonely Planet: '.$destination_title.'</h1>
				       </div>

				       <div id="wrapper">
				         <div id="sidebar">
				           <div class="block">
				             <h3>Navigation</h3>
				             <div class="content">
				               <div class="inner">
				                 HIERARCHY NAVIGATION GOES HERE
				               </div>
				             </div>
				           </div>
				         </div>

				         <div id="main">
				           <div class="block">
				             <div class="secondary-navigation">
				               <ul>
				                 <li class="first"><a href="#">'.$destination.'</a></li>
				               </ul>
				               <div class="clear"></div>
				             </div>
				             <div class="content">
				               <div class="inner">
				                 <p>Content</p>
				               </div>
				             </div>
				           </div>
				         </div>
				       </div>
				     </div>
				   </body>
				 </html>
				';
				fwrite($destinationfile, $txt);
				fclose($destinationfile);
			}
		}
	}

?>