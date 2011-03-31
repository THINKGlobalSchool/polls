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

// should always use polls/poll_container
// Check if we've already voted
//if (has_user_completed_poll(elgg_get_logged_in_user_entity(), $poll)) {
//	echo elgg_view('polls/poll_results', $vars);
//	return true;
//}

$options = unserialize($poll->poll_content);

$vote_input = elgg_view('input/submit', array(
	'name' => 'submit_vote',
	'class' => 'elgg-polls-vote elgg-button-submit',
	'value' => elgg_echo('polls:label:vote')
));

$poll_input = elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $poll->getGUID(),
));

$form_body .= "<table class='elgg-polls-vote'>";
$form_body .= "<tr><td class='elgg-polls-title' colspan='3'>{$poll->title}</td></tr>";

// Get options
foreach($options as $key => $option) {
	$form_body .= "<tr>";
	$form_body .= "<td class='elgg-polls-option-num'><label>$key. </label></td>";
	$form_body .= "<td class='elgg-polls-option-text'><label>$option</label></td>";
	$form_body .= "<td class='elgg-polls-option-input'><input name='vote' value='$key' type='radio' /></td>";
	$form_body .= "</tr>";
}
$form_body .= "<tr><td class='elgg-polls-foot' colspan='3'>$vote_input</td></tr>";

$form_body .= "</table>";
$form_body .=  $poll_input;

echo $form_body;