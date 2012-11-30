<?php
/**
 * View a poll
 *
 * @package Elggpolls
 */

$poll = get_entity(get_input('guid'));

if (!elgg_instanceof($poll, 'object', 'poll')) {
	register_error(elgg_echo('noaccess'));
	$_SESSION['last_forward_from'] = current_page_url();
	forward('');
}

$page_owner = elgg_get_page_owner_entity();

$crumbs_title = $page_owner->name;

if (elgg_instanceof($page_owner, 'group')) {
	elgg_push_breadcrumb($crumbs_title, "polls/group/$page_owner->guid/owner");
} else {
	elgg_push_breadcrumb($crumbs_title, "polls/owner/$page_owner->username");
}

$title = $poll->title;

elgg_push_breadcrumb($title);

$content = elgg_view_entity($poll, true);
$content .= elgg_view_comments($poll);

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'header' => '',
));

echo elgg_view_page($title, $body);
