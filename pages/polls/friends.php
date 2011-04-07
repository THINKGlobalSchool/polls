<?php
/**
 * Polls friends page
 */

$owner = elgg_get_page_owner_entity();

elgg_push_breadcrumb($owner->name, "polls/owner/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

$title = elgg_echo('polls:title:friends');

// getting an array of friend guids so we can find the latest poll by our friends
// @todo inefficient
$friends = $owner->getFriends(null, 9999);
$friend_guids = array();
foreach ($friends as $friend) {
	$friend_guids[] = $friend->getGUID();
}

$header = '';

if ($friend_guids) {
	$latest_poll = polls_get_latest_poll_content($friend_guids);

	if ($latest_poll) {
		// include the latest poll in the header, then show the standard header
		$header .= elgg_view('page/layouts/content/header', array(
			'buttons' => '',
			'title' => elgg_echo('polls:title:latest')
		));

		$header .= $latest_poll;
	}
}

$header .= elgg_view('page/layouts/content/header');

// show the secondary filter menu.
$content = elgg_view_menu('polls-status', array(
	'sort_by' => 'priority',
	// recycle the menu filter css
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

if ($friend_guids) {
	$content .= polls_get_page_content_list($friend_guids);
} else {
	$content .= elgg_view('polls/noresults');
}

$body .= elgg_view_layout('content', array(
	'filter_context' => 'friends',
	'content' => $content,
	'title' => $title,
	'header' => $header
));

echo elgg_view_page($title, $body);
