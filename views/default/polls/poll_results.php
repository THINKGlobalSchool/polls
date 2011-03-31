<?php
/**
 * Polls results
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$poll = $vars['entity'];

if (!elgg_instanceof($poll, 'object', 'poll')) {
	echo elgg_echo('polls:error:notfound');
	return true;
}

// should always use polls/poll_container
// Only display if we've already voted
//if (!has_user_completed_poll(get_loggedin_user(), $poll)) {
//	echo elgg_view_form('polls/vote', array(), $vars);
//	return true;
//}

$options = unserialize($poll->poll_content);
$content .= "<table class='elgg-polls-vote'>";
$content .= "<tr><td class='elgg-polls-title' colspan='3'>{$poll->title}</td></tr>";

// Count total votes
$total_count = 0;
$option_counts = array();
foreach($options as $key => $option) {
	 $option_count = (int) elgg_get_annotations(array(
		'guid' => $poll->getGUID(),
		'annotation_name' => (string)$key,
		'annotation_value' => (string)$key,
		'count' => true
	));

	 $option_counts[$key] = $option_count;
	 $total_count += $option_count;
}

// Get options
foreach($options as $key => $option) {
	$owner_content = "";
	$selected = '<input type="radio" disabled="disabled" />';
	
	if (elgg_is_logged_in()) {
		$my_vote =  elgg_get_annotations(array(
			'guid' => $poll->getGUID(),
			'annotation_name' => (string)$key,
			'annotation_value' => (string)$key,
			'annotation_owner_guid' => elgg_get_logged_in_user_guid()
		));

		if ($my_vote) {
			$selected = '<input type="radio" checked="checked" disabled="disabled" />';
		}
	}

	// show result details if you can edit
	if ($poll->canEdit() && $total_count) {
		$i = 0;
		$voted_users_html = array();

		$options = array(
			'guid' => $poll->getGUID(),
			'annotation_name' => (string)$key,
			'annotation_value' => (string)$key,
			'limit' => 0
		);

		$annotations = new ElggBatch('elgg_get_annotations', $options);

		foreach ($annotations as $vote) {
			$user = get_entity($vote->owner_guid);
			$voted_users_html[] = "<a href='{$user->getURL()}'>{$user->name}</a>";
			$i++;
		}

		// only show for options with results
		if ($i > 0) {
			$toggler_class = "elgg-polls-details-{$poll->getGUID()}";

			$owner_content = "<div class='$toggler_class elgg-polls-owner-content'>";
			$owner_content .= implode(', ', $voted_users_html);
			$owner_content .= "</div>";
		}
	}
	
	$count = $option_counts[$key];
	$percent = round(($count / $total_count) * 100);

	$content .= "<tr>";
	$content .= "<td class='elgg-polls-option-text'><label>$option</label><br />$owner_content</td>";
	$content .= "<td class='elgg-polls-option-count'><label>$percent% ($count)</label></td>";
	$content .= "<td class='elgg-polls-option-num'>$selected</td>";
	$content .= "</tr>";
}

$show_results = elgg_echo('polls:label:showresults');

if ($poll->canEdit() && $total_count) {
	$content .= <<<___HTML
	<tr>
		<td class='elgg-polls-foot' colspan='3'>
			<a class='elgg-toggler' href='.$toggler_class'>$show_results</a>
		</td>
	</tr>
___HTML;
}
	
$content .= '</table>';

echo $content;