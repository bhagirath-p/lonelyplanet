<?php
	$taxonomy_content    = file_get_contents($argv[1]);
	$destination_content = file_get_contents($argv[2]);
	$taxonomy_xml_object = simplexml_load_string($taxonomy_content) or die("Error: Cannot create object");
	$destination_xml_object = simplexml_load_string($destination_content) or die("Error: Cannot create object");

	// To create the heirarchy string
	function make_string($xml_object) {
	}

	// To fetch heirarchy string
	function get_heirarchy_string() {
		$empty_xml_object = new SimpleXMLElement("<destination></destination>");
		$heirarchy_object = make_heirarchy($GLOBALS['taxonomy_xml_object']->taxonomy->node, $empty_xml_object);
		$heirarchy_string = make_string($heirarchy_object);
		return $heirarchy_string;
	}

	// To make a heirarchy object
	function make_heirarchy($taxonomy_object, $heirarchy_object) {
		$parent_destination_object = $heirarchy_object;
		$child_destination_object = $parent_destination_object->addChild("destination");
		$child_destination_object->addAttribute('atlas_id', $taxonomy_object->attributes()->atlas_node_id);
		$child_destination_object->addAttribute('destination_name', $taxonomy_object->node_name);
		if(isset($taxonomy_object->node)) {
			foreach ($taxonomy_object->node as $key => $value) {
				make_heirarchy($value, $child_destination_object);
			}
		}
		return $heirarchy_object;
	}

	// To print destination according to heirarchy
	function print_destination($taxonomy_object, $parent_object) {
		$parent = $parent_object;
		if(isset($taxonomy_object->node)) {
			$destination = $taxonomy_object->node_name;
			$parent_destination = $parent->node_name;
			foreach ($taxonomy_object->node as $key => $value) {
				print_destination($value, $taxonomy_object);
			}
		}
		else {
			$destination = $taxonomy_object->node_name;
			$parent_destination = $parent->node_name;
			$attributes = $taxonomy_object->attributes();
			$atlas_node_id = $taxonomy_object->attributes()->atlas_node_id;
			build_webpage($atlas_node_id);
		}
	}

	// To build respective webpage
	function build_webpage($id) {
		$taxonomy_atlas_id = $id;
		$destination_xml_object = $GLOBALS['destination_xml_object'];
		foreach($destination_xml_object as $key => $value) {
			$destination_atlast_id = $value->attributes()->atlas_id;
			if(strcmp($taxonomy_atlas_id,$value->attributes()->atlas_id) == 0){
				$destination_title = $value->attributes()->title;
				$destinationfile = fopen($taxonomy_atlas_id.".html", "w") or die("Unable to open file!");
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
				                 <li class="first"><a href="#">'.$destination_title.'</a></li>
				               </ul>
				               <div class="clear"></div>
				             </div>
				             <div class="content">
				               <div class="inner">
				                 <p>'.$value->introductory->introduction->overview.'</p>
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

	// Initiate the methods
	print_destination($taxonomy_xml_object->taxonomy->node, $taxonomy_xml_object->taxonomy);
?>