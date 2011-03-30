<?php
/**
 * Add poll page
 */

$page_owner = elgg_get_page_owner_entity();

$title = elgg_echo('polls:add');
elgg_push_breadcrumb($title);

$vars = polls_prepare_form_vars();

$content = elgg_view_form('polls/save', array(), $vars);

$body = elgg_view_layout('content', array(
	'filter' => '',
	'buttons' => '',
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);