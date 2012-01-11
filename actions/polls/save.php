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

// start a new sticky form session in case of failure
elgg_make_sticky_form('poll');

// Get inputs
$title = get_input('title');
$description = get_input('description');
$tags = string_to_tag_array(get_input('tags', array()));
$access = get_input('access_id');
$rows = get_input('num_rows');
$container_guid = get_input('container_guid');
$options = get_input('options', array());

// index starts at 1 because 0 as a metadata name is icky.
// surely there's a nicer way to do this?
$poll_content = array();
$i = 1;
foreach ($options as $option) {
	if ($option) {
		$poll_content[$i] = $option;
		$i++;
	}
}

if (!$title || !$poll_content) {
	register_error(elgg_echo('polls:error:missing_fields'));
	forward(REFERRER);
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

elgg_clear_sticky_form('poll');

// Add to river
add_to_river('river/object/poll/create', 'create', elgg_get_logged_in_user_guid(), $poll->getGUID());

// Forward on
system_message(elgg_echo('polls:success:save'));
forward($poll->getURL());