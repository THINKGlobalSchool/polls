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

// Only display if we haven't voted yet
if (!has_user_completed_poll(get_loggedin_user(), $poll)) {
	echo elgg_view('polls/forms/poll', $vars);
	return;
} 

$options = unserialize($poll->poll_content);
$content .= "<table class='poll-vote'>";
$content .= "<tr><td class='poll-title' colspan='2'>{$poll->title}</td></tr>";

// Count total votes
$total_count = 0;
foreach($options as $key => $option) {
	$total_count += count_annotations($poll->getGUID(), "object", "poll", (string)$key, (string)$key);
}

// Get options
foreach($options as $key => $option) {
	$annotations = get_annotations($poll->getGUID(), "object", "poll", (string)$key, (string)$key, 0, 99999);
	
	$owner_content = "";
	// Owner content
	if ($poll->canEdit() && $annotations) {
		$i = 0;
		$box_id = "poll-details-{$poll->getGUID()}";
		$owner_content .= "<div class='$box_id poll-owner-content'>";
		foreach ($annotations as $vote) {
			$user = get_entity($vote->owner_guid);
			$owner_content .= "<a href='{$user->getURL()}'>{$user->name}</a>";
			$i++;
			if (count($annotations) > 1 && $i != count($annotations)) {
				$owner_content .= ', ';
			}
		}
		$owner_content .= "</div>";
	}
	
	$count = $annotations ? count($annotations) : 0;
	$percent = round(($count / $total_count) * 100);
	$content .= "<tr>";
	$content .= "<td class='option-text'><label>$option</label><br />$owner_content</td>";
	$content .= "<td class='option-count'><label>$percent% ($count)</label></td>";
	$content .= "</tr>";
	

}
$content .= "<tr><td class='poll-foot' colspan='2'><a class='poll-show-link' onclick='javascript:toggle_poll_details(\"$box_id\");'>" . elgg_echo('polls:label:showresults') . "</a></td></tr>";
$content .= "</table>";

// Add script if we're the owner
if ($poll->canEdit()) {
	$script = <<<EOT
		<script type='text/javascript'>
			function toggle_poll_details(id) {
				$('.' + id).toggle('fast');
				return false;
			}
		</script>
EOT;
}

echo $content . $owner_content . $script;

?>