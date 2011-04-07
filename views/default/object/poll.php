<?php
/**
 * Polls Entity Display
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$full = elgg_extract('full_view', $vars, FALSE);
$poll = elgg_extract('entity', $vars, FALSE);

if (!$poll) {
	return true;
}

$owner = $poll->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$container = $poll->getContainerEntity();
$categories = elgg_view('output/categories', $vars);

$owner_link = elgg_view('output/url', array(
	'href' => "polls/owner/$owner->username",
	'text' => $owner->name,
));
$author_text = elgg_echo('byline', array($owner_link));

$tags = elgg_view('output/tags', array('tags' => $poll->tags));
$date = elgg_view_friendly_time($poll->time_created);

$comments_count = $poll->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $poll->getURL() . '#comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'polls',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $categories $comments_link";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

if ($full && !elgg_in_context('gallery')) {
	$header = elgg_view_title($poll->title);

	$params = array(
		'entity' => $poll,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$list_body = elgg_view('page/components/summary', $params);
	$poll_info = elgg_view_image_block($owner_icon, $list_body);
	$poll = elgg_view('polls/poll_container', $vars);

	echo <<<HTML
$header
$poll_info
<div class="elgg-content mts">
	$poll
</div>
HTML;

} else {
	$params = array(
		'entity' => $poll,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);

	$body .= elgg_view('page/components/summary', $params);
	echo elgg_view_image_block($owner_icon, $body);
}