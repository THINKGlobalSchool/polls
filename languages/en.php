<?php
/**
 * Polls english language translation
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */
$english = array(
	
	// Generic
	'poll' => 'Polls',
	'polls' => 'Polls',
	'item:object:poll' => 'Polls',
	'polls:add' => 'New Poll',
	
	// Page titles 
	'polls:title:all' => 'All Polls',
	'polls:title:new' => 'New Poll', 
	'polls:title:edit' => 'Edit Poll',
	'polls:title:owner' => '%s\'s Polls',
	'polls:title:friends' => 'Friends\' Polls',
	'polls:title:latest' => "Latest Poll",

	// Menu items
	'polls:menu:all' => 'All Polls',
	'polls:menu:your' => 'Your Polls',
	'polls:menu:friends' => 'Friend\'s Polls',

	// Labels 
	'polls:label:new' => 'New Poll', 
	'polls:label:friends' => 'Friends',
	'polls:label:title' => 'Title',
	'polls:label:description' => 'Description',
	'polls:label:tags' => 'Tags',
	'polls:label:save' => 'Save',
	'polls:label:deleteconfirm' => 'Are you sure you want to delete this poll?',
	'polls:label:posted_by' => 'Posted by %s',
	'polls:label:options' => 'Poll Options',
	'polls:label:add_option' => 'Add Option',
	'polls:label:vote' => 'Submit',
	'polls:label:complete' => 'Complete',
	'polls:label:incomplete' => 'Incomplete',
	'polls:label:grouppolls' => 'Group Polls',
	'polls:label:results' => 'Detailed Results', 
	'polls:label:showresults' => 'Show Details',
	
	// River
	'polls:river:poll:create' => 'created a Poll titled %s',
		
	// Messages
	'polls:error:notfound' => 'Poll not found',
	'polls:error:missing_fields' => 'You must enter a title and some options.',
	'polls:error:save' => 'There was an error saving the poll',
	'polls:error:delete' => 'There was an error deleting the poll',
	'polls:error:vote' => 'There was an error voting!',
	'polls:error:already_voted' => 'You have already voted in this poll',
	'polls:error:no_vote' => 'You must pick an option',
	'polls:success:delete' => 'Poll deleted successfully!',
	'polls:success:save' => 'Poll saved successfully!',
	'polls:success:vote' => 'Thanks for voting!',

	// Other content
	'groups:enablepolls' => 'Enable group polls',

);

add_translation('en',$english);