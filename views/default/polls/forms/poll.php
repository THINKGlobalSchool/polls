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
if (has_user_completed_poll(get_loggedin_user(), $poll)) {
	echo elgg_view('polls/poll_results', $vars);
	return;
}

$options = unserialize($poll->poll_content);
$action = elgg_get_site_url() . 'action/polls/vote';

$vote_input = elgg_view('input/submit', array(
	'internalname' => 'submit_vote',
	'class' => "submit-vote-{$poll->getGUID()}",
	'value' => elgg_echo('polls:label:vote')
));

$poll_input = elgg_view('input/hidden', array(
	'internalid' => 'poll_guid',
	'internalname' => 'poll_guid',
	'value' => $poll->getGUID(),
));



$form_body .= "<table class='poll-vote'>";
$form_body .= "<tr><td class='poll-title' colspan='3'>{$poll->title}</td></tr>";

// Get options
foreach($options as $key => $option) {
	$form_body .= "<tr>";
	$form_body .= "<td class='option-num'><label>$key. </label></td>";
	$form_body .= "<td class='option-text'><label>$option</label></td>";
	$form_body .= "<td class='option-input'><input name='poll_vote' value='$key' type='radio' /></td>";
	$form_body .= "</tr>";
}
$form_body .= "<tr><td class='poll-foot' colspan='3'>$vote_input</td></tr>";

$form_body .= "</table>";
$form_body .=  $poll_input;

$script = <<<EOT
	<script type='text/javascript'>
	$(document).ready(function () {
		$(".submit-vote-{$poll->getGUID()}").click(
			function() {
				// Check to make sure we have selected an option
				if ($('#polls-vote-form-{$poll->getGUID()}  input:radio[name=poll_vote]:checked').val()) {
					$(".submit-vote-{$poll->getGUID()}").attr('disabled', 'disabled');
					data = $("#polls-vote-form-{$poll->getGUID()}").serialize();
					submitPollVote(data, "{$poll->getGUID()}");
					return false;
				} else {
					$('#polls-vote-form-{$poll->getGUID()} .poll-foot').append('<p class="poll-error">* You need to make a choice!</p>');
					return false;
				}
				
				
			}
		);
	});
	</script>
EOT;


echo elgg_view('input/form', array(
	'internalname' => 'polls_vote_form',
	'internalid' => 'polls-vote-form-' . $poll->getGUID(),
	'class' => 'polls-vote-form',
	'body' => $form_body,
	'action' => $action
)) . $script;


?>