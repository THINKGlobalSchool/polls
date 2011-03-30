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
if (has_user_completed_poll(elgg_get_logged_in_user_entity(), $poll)) {
	echo elgg_view('polls/poll_results', $vars);
	return true;
}

$options = unserialize($poll->poll_content);

$vote_input = elgg_view('input/submit', array(
	'name' => 'submit_vote',
	'class' => 'elgg-polls-vote',
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
					$('#polls-vote-form-{$poll->getGUID()} .poll-foot').append('<p class="elgg-polls-error">* You need to make a choice!</p>');
					return false;
				}
				
				
			}
		);
	});
	</script>
EOT;
?>