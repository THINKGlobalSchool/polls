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
if (elgg_instanceof($poll, 'object', 'poll') && !check_entity_relationship(get_loggedin_userid(), HAS_VOTED_RELATIONSHIP, $poll->getGUID())) {
	if ($poll->annotate($vote, $vote, $poll->access_id, get_loggedin_userid())) {
		add_entity_relationship(get_loggedin_userid(), HAS_VOTED_RELATIONSHIP, $guid);
		system_message(elgg_echo('polls:success:vote'));
		forward(REFERER);
	} else {
		system_message(elgg_echo('polls:error:vote'));
		forward(REFERER);
	}
} else {
	register_error(elgg_echo('polls:error:notfound'));
	forward(REFERER);
}

?>