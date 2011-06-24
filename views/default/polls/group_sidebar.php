<?php
/**
 * Polls group sidebar entry
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$group = elgg_get_page_owner_entity();

if (!elgg_instanceof($group, 'group') || !$group->polls_enable) {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => 'polls/group/' . $group->getGUID() . '/owner/',
	'text' => elgg_echo('link:view:all')

));

$group_polls = elgg_echo('polls:label:grouppolls');

$content = '';

// get the group's polls
$polls = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'poll',
	'container_guids' => $group->getGUID(),
	'limit' => 6,
	'wheres' => polls_get_incomplete_where_clause()
));

if ($polls){
	foreach ($polls as $poll) {
		$owner = $poll->getOwnerEntity();
		$time = elgg_view_friendly_time($poll->time_created);
		$icon = elgg_view_entity_icon($owner, 'tiny');
		$title = "<a href=\"{$poll->getURL()}\">{$poll->title}</a>";

		$params = array(
				'entity' => $poll,
				'title' => $title,
				'subtitle' => $time,
			);
		$list_body = elgg_view('object/elements/summary', $params);
		$content .= elgg_view_image_block($icon, $list_body);
	}
}

$create_url = "polls/add/" . $group->getGUID();
$content .= elgg_view('output/url', array(
	'href' => $create_url,
	'text' => elgg_echo('polls:add'),
	'class' => 'elgg-button elgg-button-action mtm clearfix'
));

$title = "$group_polls <span class=\"right small elgg-polls-subtext\">$all_link</span>";

echo elgg_view_module('aside', $title, $content, array('class' => 'elgg-polls-sidebar'));