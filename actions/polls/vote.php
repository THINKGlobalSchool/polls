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

$vote = get_input('vote');
$guid = get_input('guid', null);
$poll = get_entity($guid);

if (!elgg_instanceof($poll, 'object', 'poll')) {
	register_error(elgg_echo('polls:error:notfound'));
	forward(REFERER);
}

// Make sure we have a poll, and that the user hasn't voted
$has_voted = has_user_completed_poll(elgg_get_logged_in_user_entity(), $poll);

if ($has_voted) {
	register_error(elgg_echo('polls:error:already_voted'));
	forward(REFERER);
}

if (!$vote) {
	register_error(elgg_echo('polls:error:no_vote'));
	forward(REFERER);
}

if ($poll->annotate($vote, $vote, $poll->access_id, elgg_get_logged_in_user_guid())) {
	add_entity_relationship(elgg_get_logged_in_user_guid(), HAS_VOTED_RELATIONSHIP, $guid);
	update_entity_last_action($guid);
	if (elgg_is_xhr()) {
		echo elgg_view('polls/poll_results', array('entity' => $poll));
	}
} else {
	register_error(elgg_echo('polls:error:vote'));
}

forward(REFERRER);