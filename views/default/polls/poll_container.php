<?php
/**
 * Polls Container
 *
 * Shows the correct view for a poll--The results of the vote form
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$poll = elgg_extract('entity', $vars);
$user = elgg_get_logged_in_user_entity();

if (!elgg_instanceof($poll, 'object', 'poll')) {
	return true;
}

if ($user && !has_user_completed_poll($user, $poll)) {
	$content = elgg_view_form('polls/vote', array('class' => 'elgg-polls-vote'), $vars);
} else {
	$content = elgg_view('polls/poll_results', $vars);
}

echo "<div class=\"elgg-polls-container\">$content</div>";