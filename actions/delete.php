<?php
/**
 * Polls delete action
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid', null);
$poll = get_entity($guid);

if (elgg_instanceof($poll, 'object', 'poll') && $poll->canEdit()) {
	$container = get_entity($poll->container_guid);
	if ($poll->delete()) {
		system_message(elgg_echo('polls:success:delete'));
		forward("pg/polls/{$container->username}");
	} else {
		register_error(elgg_echo('polls:error:delete'));
	}
} else {
	register_error(elgg_echo('polls:error:notfound'));
}

forward(REFERER);