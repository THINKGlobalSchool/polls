<?php
/**
 * Polls all page
 */

$title = elgg_echo('polls:title:all');
$header = '';

$latest_poll = polls_get_latest_poll_content();

if ($latest_poll) {
	// include the latest poll in the header, then show the standard header
	$header .= elgg_view('page/layouts/content/header', array(
		'buttons' => '',
		'title' => elgg_echo('polls:title:latest')
	));

	$header .= $latest_poll;
}

$header .= elgg_view('page/layouts/content/header');

// show the secondary filter menu.
$content = elgg_view_menu('polls-status', array(
	'sort_by' => 'priority',
	// recycle the menu filter css
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));
$content .= polls_get_page_content_list();

$body .= elgg_view_layout('content', array(
	'filter_context' => 'all',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('bookmarks/sidebar'),
	'header' => $header
));

echo elgg_view_page($title, $body);