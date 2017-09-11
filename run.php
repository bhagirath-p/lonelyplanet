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
			foreach ($attributes as $k => $v) {
				if($k == 'atlas_node_id'){
					build_webpage($v, $destination_xml_object);
					break;
				}
			}
		}
	}

	function build_webpage($id, $destination_xml_object) {
		$atlas_id = $id;
		$destination_item = null;
		foreach($destination_xml_object as $value) {
			$destination_attributes = $value->attributes();
			// print_r($destination_attributes);
			foreach($destination_attributes as $key => $val) {
				// print_r($key);
				if($key == 'atlas_id') {
				    if ($atlas_id == $val) {
				    	printf($atlas_id);
						printf("\n>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>\n");
				        break;
				    }
				}
			}
		}
		$destinationfile = fopen($atlas_id.".html", "w") or die("Unable to open file!");
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
		         <h1>Lonely Planet: '.$destination.'</h1>
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

?>