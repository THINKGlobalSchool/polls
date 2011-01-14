<?php
/**
 * Polls vote form
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$poll = $vars['entity'];

// Check if we've already voted
if (check_entity_relationship(get_loggedin_userid(), HAS_VOTED_RELATIONSHIP, $poll->getGUID())) {
	return true;
}

$options = unserialize($poll->poll_content);
$action = elgg_get_site_url() . 'action/polls/vote';

$vote_input = elgg_view('input/submit', array(
	'internalid' => 'submit_vote',
	'internalname' => 'submit_vote',
	'value' => elgg_echo('polls:label:vote')
));

$poll_input = elgg_view('input/hidden', array(
	'internalid' => 'poll_guid',
	'internalname' => 'poll_guid',
	'value' => $poll->getGUID(),
));



$form_body .= "<table id='poll-vote'>";
$form_body .= "<tr><td class='poll-title' colspan='3'>{$poll->title}</td></tr>";

// Get options
foreach($options as $key => $option) {
	$number = $key + 1;
	$form_body .= "<tr>";
	$form_body .= "<td class='option-num'><label>$number. </label></td>";
	$form_body .= "<td class='option-text'><label>$option</label></td>";
	$form_body .= "<td class='option-input'><input name='poll_vote' value='$key' type='radio' /></td>";
	$form_body .= "</tr>";
}
$form_body .= "<tr><td class='poll-foot' colspan='3'>$vote_input</td></tr>";

$form_body .= "</table>";
$form_body .=  $poll_input;


echo elgg_view('input/form', array(
	'internalname' => 'polls_vote_form',
	'internalid' => 'polls-vote-form',
	'body' => $form_body,
	'action' => $action
)) . $script;


?>