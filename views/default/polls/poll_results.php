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
if (!check_entity_relationship(get_loggedin_userid(), HAS_VOTED_RELATIONSHIP, $poll->getGUID())) {
	return true;
}

$options = unserialize($poll->poll_content);
$content .= "<table id='poll-vote'>";
$content .= "<tr><td class='poll-title' colspan='2'>{$poll->title}</td></tr>";

// Count total votes
$total_count = 0;
foreach($options as $key => $option) {
	$total_count += count_annotations($poll->getGUID(), "object", "poll", (string)$key, (string)$key);
}

// Get options
foreach($options as $key => $option) {
	$count = count_annotations($poll->getGUID(), "object", "poll", (string)$key, (string)$key);
	$percent = round(($count / $total_count) * 100);
	$content .= "<tr>";
	$content .= "<td class='option-text'><label>$option</label></td>";
	$content .= "<td class='option-count'><label>$percent% ($count)</label></td>";
	$content .= "</tr>";
}
$content .= "<tr><td class='poll-foot' colspan='2'>&nbsp;</td></tr>";
$content .= "</table>";

echo $content;

?>