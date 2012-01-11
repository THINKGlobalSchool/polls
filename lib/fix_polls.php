<?php

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");

global $CONFIG;


$guid = get_input('guid');

$poll = get_entity($guid);

$options = unserialize($poll->poll_content);

// Get annotations
$annotations = get_annotations($poll->getGUID(), "object", "poll", "", "", 0, 99999);


$new_options = array();
foreach ($options as $key => $option) {
	$new_options[$key+1] =$option;
}

$poll->poll_content = serialize($new_options);
$poll->save();

foreach($annotations as $vote) {
	
	$name = (int)$vote->name + 1;
	$value = (int)$vote->value +1;
	echo "Name: " . $vote->name . "<br/>Value: " . $vote->value . "</br>";
	
	create_annotation($poll->getGUID(), $name, $value, $vote->value_type, $vote->owner_guid, $vote->access_id);
	elgg_delete_annotations(array('annotation_id' => $vote->id));
}

print_r_html($annotations);


?>