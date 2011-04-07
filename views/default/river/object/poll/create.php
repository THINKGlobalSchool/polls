<?php
/**
 * Polls river create
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$performed_by = get_entity($vars['item']->subject_guid);
$object = get_entity($vars['item']->object_guid);
$url = $object->getURL();
$user_url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
$blurb = elgg_echo('polls:river:poll:create', array("<a href=\"{$object->getURL()}\">{$object->title}</a>"));

$poll = '';

if (elgg_is_logged_in()) {
	$poll = elgg_view('polls/poll_container', array('entity' => $object));
}

echo $blurb . $poll;