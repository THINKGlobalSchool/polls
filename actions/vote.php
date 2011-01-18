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

$vote = get_input('poll_vote');
$guid = get_input('poll_guid', null);
$poll = get_entity($guid);

// Make sure we have a poll, and that the user hasn't voted
$has_not_voted = !has_user_completed_poll(get_loggedin_user(), $poll);

if (elgg_instanceof($poll, 'object', 'poll') && $has_not_voted) {
	if ($poll->annotate($vote, $vote, $poll->access_id, get_loggedin_userid())) {
		add_entity_relationship(get_loggedin_userid(), HAS_VOTED_RELATIONSHIP, $guid);
		echo $guid;
		exit;
		//forward(REFERER);
	} else {
		//forward(REFERER);
		echo false;
		exit;
	}
} else {
	//forward(REFERER);
	echo false;
	exit;

}

?>