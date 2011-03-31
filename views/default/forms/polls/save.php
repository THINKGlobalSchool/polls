<?php
/**
 * Polls Edit Form
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 */

// once elgg_view stops throwing all sorts of junk into $vars, we can use extract()
$title = elgg_extract('title', $vars, '');
$desc = elgg_extract('description', $vars, '');
$tags = elgg_extract('tags', $vars, '');
$access_id = elgg_extract('access_id', $vars, ACCESS_LOGGED_IN);
$container_guid = elgg_extract('container_guid', $vars);

// @todo you can't edit a poll
$guid = elgg_extract('guid', $vars, null);
$entity = elgg_extract('entity', $vars, null);

$table_id = 'poll';

// Some defaults
$num_rows = 4;

// If we have an entity, we're editing
if ($vars['entity']) {
	// Hidden field to identify poll
	$poll_guid 	= elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $vars['entity']->getGUID()
	));
} else { // Creating a new poll
	$access_id = ACCESS_LOGGED_IN;
}

// Container input
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));


// Labels/Inputs
$title_label = elgg_echo('polls:label:title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'value' => $title
));

$description_label = elgg_echo('polls:label:description');
$description_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'value' => $desc
));

$tags_label = elgg_echo('polls:label:tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'value' => $tags
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'value' => $access_id
));

$add_label = elgg_echo('polls:label:addoption');  
$poll_label = elgg_echo('polls:label:options');

// Build poll input form
$poll_input = "<ul class='elgg-polls-options'>";
for ($i = 0; $i < $num_rows; $i++) {
	$class = ($i % 2 == 1) ? 'elgg-polls-input-odd' : 'elgg-polls-input-even';
	$poll_input .= "<li class=\"$class\">";
   	$poll_input .=  elgg_view('input/text', array('name' => 'options[]', 'class' => 'elgg-poll-option'));
	$poll_input .= "<a class=\"elgg-polls-remove-option\"><span class=\"elgg-icon elgg-icon-delete\"></span></a>";
	$poll_input .= "</li>";
}
$poll_input .= "</ul><br />";

// the add button
$poll_input .= elgg_view('output/url', array(
	'text' => elgg_echo('polls:label:add_option'),
	'class' => 'elgg-button elgg-button-action elgg-polls-add-option'
));

$polls_save_input = elgg_view('input/submit', array(
	'name' => 'save_input',
	'class' => 'elgg-polls-add elgg-button-submit',
	'value' => elgg_echo('polls:label:save')
));

$form_body = <<<EOT
	<p>
		<label>$title_label</label>
		$title_input
	</p>
	<p>
		<label>$description_label</label>
		$description_input
	</p>
	<p>
		<label>$poll_label</label>
		$poll_input
	</p>
	<p>
		<label>$subtypes_label</label>
		$subtypes_input
	</p>
	<div style='clear: both;'></div>
	<br />
	<p>
		<label>$tags_label</label>
		$tags_input
	</p>
	<br />
	<p>
		<label>$access_label</label>
		$access_input
	</p>
	<p>
		$polls_save_input
		$poll_guid
		$container_guid_input
	</p>
EOT;

echo $form_body;