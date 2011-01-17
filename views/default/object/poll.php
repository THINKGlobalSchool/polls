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

// Get entity info
$title = $vars['entity']->title;
$owner = $vars['entity']->getOwnerEntity();
$owner_poll_link = "<a href=\"".elgg_get_site_url()."pg/polls/$owner->username\">{$owner->name}</a>";
$friendlytime = elgg_view_friendly_time($vars['entity']->time_created);
$owner_text = elgg_echo('polls:label:posted_by', array($owner_poll_link)) . ' ' . $friendlytime;
$address = $vars['entity']->getURL();
$parsed_url = parse_url($address);
$object_acl = elgg_view('output/access', array('entity' => $vars['entity']));

// Comments
$comments_count = elgg_count_comments($vars['entity']);

if ($comments_count != 0) { // only display if there are commments
	$comments_link = " | <a href=\"{$vars['entity']->getURL()}#annotations\">" . elgg_echo("comments") . " (". $comments_count .")</a>";
}else{
	$comments_link = '';
}

// Edit/Delete
$edit = $object_acl;	// Display access level
if ($vars['entity']->canEdit()) {
//	$edit_url = elgg_get_site_url()."pg/polls/edit/{$vars['entity']->getGUID()}/";
//	$edit_link = "<span class='entity_edit'><a href=\"$edit_url\">" . elgg_echo('edit') . '</a></span>';

	$delete_url = "action/polls/delete?guid=" . $vars['entity']->guid;
	$delete_link .= "<span class='delete_button'>" . elgg_view('output/confirmlink',array(
				'href' => $delete_url,
				'text' => elgg_echo("delete"),
				'confirm' => elgg_echo("polls:label:deleteconfirm"),
				)) . "</span>";

	$edit .= "$edit_link $delete_link";
}

// View to override
$edit .= elgg_view("polls/options",array('entity' => $vars['entity']));

// Add favorites and likes
$favorites .= elgg_view("favorites/form",array('entity' => $vars['entity']));
$likes .= elgg_view_likes($vars['entity']); // include likes

// Tags
$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
if (!empty($tags)) {
	$tags = '<p class="tags">' . $tags . '</p>';
}


if ($vars['full']) { // Full view
	// Owner Icon 
	$owner_icon = elgg_view('profile/icon', array('entity' => $owner, 'size' => 'tiny'));
	$poll_container = elgg_view('polls/poll_container', $vars);
	
	// Display content
	echo <<<___END
	<div class="polls clearfix">
		<div id="content_header" class="clearfix">
		</div>
		<div class="clearfix">
		<div class="entity_listing_icon">
			$owner_icon
		</div>
		<div class="entity_listing_info">
			<div class="entity_metadata">
				$edit 
				$favorites 
				$likes
			</div>
			<p class="entity_subtext">
				$owner_text
				$date
				$comments_link
			</p>
			$tags
		</div>
		</div>
		$poll_container
		<div class='polls_bottom'></div>
	</div>
___END;
} else {// Listing
	
	$icon = elgg_view("profile/icon", array('entity' => $owner,'size' => 'tiny',));
	
	// View description pop-down
	if ($vars['entity']->description != '') {
		$view_desc = "| <a class='link' onclick=\"elgg_slide_toggle(this,'.entity_listing','.note');\">" . elgg_echo('description') . "</a>";
		$description = "<div class='note hidden'>". $vars['entity']->description . "</div>";	
	} 
		
	$info = <<<___END
	<div class='entity_metadata'>
		$edit 
		$favorites 
		$likes
	</div>
	<p class='entity_title'>
		<a  href="$address">$title</a>
	</p>
	<p class='entity_subtext'>
		$owner_text
		$date
		$view_desc
		$comments_link
	</p>
	$tags
	$description
___END;
	echo "<div class='polls'>" . elgg_view_listing($icon, $info) . "</div>";
}
