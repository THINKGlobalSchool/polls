<?php
/**
 * Polls create action
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */
gatekeeper();

// Get inputs
$title = get_input('poll_title');
$description = get_input('poll_description');
$tags = string_to_tag_array(get_input('poll_tags'));
$access = get_input('poll_access');
$rows = get_input('num_rows');
$container_guid = get_input('container_guid');
 
// Get poll content
for($i = 0; $i < $rows; $i++) {
	$poll_content[$i] = get_input("$i");	
}

// Sticky form
elgg_make_sticky_form('polls_save_form');
if (!$title) {
	register_error(elgg_echo('polls:error:titlerequired'));
	forward(elgg_get_site_url() . 'pg/polls/search#' . $search);
}


$poll = new ElggObject();
$poll->subtype = 'poll';
$poll->title = $title;
$poll->description = $description;
$poll->tags = $tags;
$poll->access_id = $access;
$poll->container_guid = $container_guid;
$poll->poll_content = serialize($poll_content);

// If error saving, register error and return
if (!$poll->save()) {
	register_error(elgg_echo('polls:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('polls_save_form');

// Add to river
add_to_river('river/object/poll/create', 'create', get_loggedin_userid(), $poll->getGUID());

// Forward on
system_message(elgg_echo('polls:success:save'));
forward($poll->getURL());


?>