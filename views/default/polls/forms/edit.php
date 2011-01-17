<?php
/**
 * Polls Edit Form
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */
$table_id = 'poll';

?>
<script type="text/javascript">
	var table_id = '<?php echo $table_id; ?>';
	// Fire this when document is 100% loaded
	$(document).ready(function() {	
		counter = $("#num_rows").val();

		// Set up event bindings
		bindRemoveRowClickHandler(table_id);
	
		// Set up click event for adding rows
		$("a#add").click(function() {
			// re-bind all the remove buttons when a new row is added
			bindRemoveRowClickHandler(table_id);
		});
	});

</script>
<?php

// Add in the JS
elgg_extend_view('metatags', 'polls/editpoll_js');

// Some defaults
$num_rows = 4;

// Num rows input
$num_rows_input = elgg_view('input/hidden', array('internalname' => 'num_rows', 'internalid' => 'num_rows' , 'value' => $num_rows));

// If we have an entity, we're editing
if ($vars['entity']) {
	$action 		= 'action/polls/edit';
	$title 			= $vars['entity']->title;
	$description 	= $vars['entity']->description;
	$tags 			= $vars['entity']->tags;
	$access_id 		= $vars['entity']->access_id;
	
	
	// Hidden field to identify poll
	$poll_guid 	= elgg_view('input/hidden', array(
		'internalid' => 'poll_guid', 
		'internalname' => 'poll_guid',
		'value' => $vars['entity']->getGUID()
	));
	

} else { // Creating a new poll
	$action = 'action/polls/create';
	$access_id = ACCESS_LOGGED_IN;
}


// Load sticky form values
if (elgg_is_sticky_form('polls_save_form')) {
	$title = elgg_get_sticky_value('polls_save_form', 'poll_title');
	$description = elgg_get_sticky_value('polls_save_form', 'poll_description');
	$tags = elgg_get_sticky_value('polls_save_form', 'poll_tags');
	$access_id = elgg_get_sticky_value('polls_save_form', 'poll_access');
}


// Labels/Inputs
$title_label = elgg_echo('polls:label:title');
$title_input = elgg_view('input/text', array(
	'internalid' => 'poll_title',
	'internalname' => 'poll_title',
	'value' => $title
));

$description_label = elgg_echo('polls:label:description');
$description_input = elgg_view('input/longtext', array(
	'internalid' => 'poll_description',
	'internalname' => 'poll_description',
	'value' => $description
));

$tags_label = elgg_echo('polls:label:tags');
$tags_input = elgg_view('input/tags', array(
	'internalid' => 'poll_tags',
	'internalname' => 'poll_tags',
	'value' => $tags
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'internalid' => 'poll_access',
	'internalname' => 'poll_access',
	'value' => $access_id
));

$add_label = elgg_echo('polls:label:addoption');

// Graphic URLS
$add 	= $vars['url'] . "mod/polls/graphics/plus.gif";
$remove = $vars['url'] . "mod/polls/graphics/minus.gif";
  
$poll_label = elgg_echo('polls:label:options');

// Build poll input form
$poll_input = "<table class='poll_table' id='poll'>";
for ($i = 0; $i < $num_rows; $i++) {
	$poll_input .= "<tr id='row" . $i . "'>";
	$input_class = 'poll_input';
	$poll_input .= "<td>";
   	$poll_input .=  elgg_view('input/text', array('internalname' => (string)$i, 'value' => elgg_echo($contents[$i][$j]), 'class' => $input_class));
	$poll_input .= "</td>";
	$poll_input .= "<td style='vertical-align: middle;'><div id='remove_row' class='remove_img' onmouseout='this.className=\"remove_img\"'  onmouseover='this.className=\"remove_img_over\"'></div></td>";
	$poll_input .= "</tr>";
} 
$poll_input .= "</table><br />";

$polls_save_input = elgg_view('input/submit', array(
	'internalid' => 'polls_save_input',
	'internalname' => 'polls_save_input',
	'value' => elgg_echo('polls:label:save')
));

$form_body = <<<EOT
	<div id='polls_save'>
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
			<label>$add_label</label> <a id='add' href="#" onclick="addOptionRow('$table_id'); return false;"><img src="$add" /></a><br /><br />
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
			$search_input
			$poll_guid
			$num_rows_input
		</p>
	</div>
EOT;


echo elgg_view('input/form', array(
	'internalname' => 'polls_save_form',
	'internalid' => 'polls_save_form',
	'body' => $form_body,
	'action' => $action
)) . $script;


