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

$object = $vars['item']->getObjectEntity();

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
));
$poll = '';

if (elgg_is_logged_in()) {
	$poll = elgg_view('polls/poll_container', array('entity' => $object));
}

echo $blurb . $poll;