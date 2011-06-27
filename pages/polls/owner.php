<?php
/**
 * Polls owner page
 */

$owner = elgg_get_page_owner_entity();
elgg_push_breadcrumb($owner->name);

$header = '';
$latest_poll = polls_get_latest_poll_content($owner->getGUID());

if ($latest_poll) {
	// include the latest poll in the header, then show the standard header
	$header .= elgg_view('page/layouts/content/header', array(
		'title' => elgg_echo('polls:title:latest')
	));

	$header .= $latest_poll;
}

if ($owner->getGUID() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
	elgg_register_add_button();
} else if (elgg_instanceof($owner, 'group')) {
	$filter = FALSE;
	elgg_register_add_button();
} else {
	$filter_context = 'none';
}

$header .= elgg_view('page/layouts/content/header', array(
	'title' => elgg_echo('polls:title:owner', array($owner->name))
));

// show the secondary filter menu.
$content = elgg_view_menu('polls-status', array(
	'sort_by' => 'priority',
	// recycle the menu filter css
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));
$content .= polls_get_page_content_list($owner->getGUID());

$options = array(
	'content' => $content,
	'title' => $title,
	'header' => $header
);

$options['filter_context'] = $filter_context;
$options['filter'] = $filter;

$body .= elgg_view_layout('content', $options);

echo elgg_view_page($title, $body);