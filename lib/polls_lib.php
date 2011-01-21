<?php
/**
 * Polls helper function
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

/* Get edit/create content */
function polls_get_page_content_edit($page_type, $guid) {
	$vars = array();
	if ($page_type == 'edit') {
		$title = elgg_echo('polls:title:edit');
		$poll = get_entity((int)$guid);

		if (elgg_instanceof($poll, 'object', 'poll') && $poll->canEdit()) {
			$vars['entity'] = $poll;

			elgg_push_breadcrumb($poll->title, $poll->getURL());
			elgg_push_breadcrumb(elgg_echo('edit'));

			$content = elgg_view_title($title) . elgg_view('polls/forms/edit', $vars);
	
		} else {
			$content = elgg_echo('polls:error:notfound');
		}
		
	
	} else {
		$title = elgg_echo('polls:title:new');
		if (!$guid) {
			$container = get_loggedin_user();
		} else {
			$container = get_entity($guid);
		}
		elgg_set_page_owner_guid($container->guid);
		
		elgg_push_breadcrumb(elgg_echo('polls:label:new'));
		
		$content = elgg_view_title($title) . elgg_view('polls/forms/edit', $vars);
	}


	return array('content' => $content, 'title' => $title, 'layout' => 'one_column_with_sidebar');
}

/* View a poll  */
function polls_get_page_content_view($guid) {
	$poll = get_entity($guid);
	if (elgg_instanceof($poll, 'object', 'poll')) {
		$owner = get_entity($poll->container_guid);
		set_page_owner($owner->getGUID());
		if (elgg_instanceof($owner, 'group')) {
			elgg_push_breadcrumb($owner->name, elgg_get_site_url() . "pg/polls/group/{$owner->getGUID()}/owner");
		} else {
			elgg_push_breadcrumb($owner->name, elgg_get_site_url() . 'pg/polls/owner/' . $owner->username);
		}
		elgg_push_breadcrumb($poll->title, $poll->getURL());
		$return['title'] = $poll->title;
		$return['content'] = elgg_view_entity($poll, true);
		$return['layout'] = 'one_column_with_sidebar';
	}

	return $return;
}

/**
 * Get page components to list a user's or all polls
 *
 * @param int $owner_guid The GUID of the page owner or NULL for all polls
 * @return array
 */
function polls_get_page_content_list($container_guid = NULL) {

	$return = array();
	$return['layout'] = 'one_column_with_sidebar'; // @TODO Temporary.. until we're up to latest code level

	$options = array(
		'type' => 'object',
		'subtype' => 'poll',
		'full_view' => FALSE,
		'limit' => 10
	);

	$loggedin_userid = get_loggedin_userid();
	if ($container_guid) {
		$options['container_guid'] = $container_guid;
		$container = get_entity($container_guid);
		if (!$container) {

		}
		$return['title'] = elgg_echo('polls:title:userpolls', array($container->name));
		elgg_set_page_owner_guid($container_guid);

		$crumbs_title = elgg_echo('polls:title:ownedpoll', array($container->name));
		elgg_push_breadcrumb($crumbs_title);

		if ($container_guid == $loggedin_userid) {
			$return['filter_context'] = 'mine';
		} 

		/*Groups..
		if (elgg_instanceof($container, 'group')) {
			$return['filter'] = '';
			if ($container->isMember(get_loggedin_user())) {
				$url = "pg/polls/new/$container->guid";
				$params = array(
					'href' => $url,
					'text' => elgg_echo("polls:new"),
					'class' => 'elgg-action-button',
				);
				$buttons = elgg_view('output/url', $params);
				$return['buttons'] = $buttons;
			}
		}*/
		
	} else {
		$return['filter_context'] = 'everyone';
		$return['title'] = elgg_echo('polls:title:allpolls');
	}
	
	// Get latest poll for display
	$latest = polls_get_latest_poll_content();
	
	$header = elgg_view('page_elements/content_header', array(
		'context' => $return['filter_context'],
		'type' => 'poll',
		'all_link' => elgg_get_site_url() . "pg/polls",
		'mine_link' => elgg_get_site_url() . "pg/polls/owner/" . get_loggedin_user()->username,
		'friend_link' => elgg_get_site_url() . "pg/polls/friends/" . get_loggedin_user()->username,
		'new_link' => elgg_get_site_url() . "pg/polls/new/" . $container_guid,
	));
	
	// Complete/Incomplete menu
	$header .= elgg_view('polls/nav_show_by_complete', array('return_url' => 'pg/polls'));
	
	if ($container_guid && ($container_guid != $loggedin_userid)) {
		if (elgg_instanceof($container, 'group') && $container->isMember(get_loggedin_user())) {
			// Get latest group poll for display
			$latest = polls_get_latest_poll_content($container_guid);
			$url = "pg/polls/new/$container->guid";
			$header = elgg_view('page_elements/content_header', array('type' => 'poll', 'new_link' => $url));
		} else {
			// do not show content header when viewing other users' posts
			$header = elgg_view('page_elements/content_header_member', array('type' => 'poll'));
		}	
	}

	// Check status.. 
	if (get_input('status') == 'complete') {
		$options['relationship'] = HAS_VOTED_RELATIONSHIP;
		$options['relationship_guid'] = get_loggedin_userid(); 
		$options['inverse_relationship'] = FALSE;
		// Nice and easy here, just grab entities with the voted relationship
		$list = elgg_list_entities_from_relationship($options);
	} else {
		$options['pagination'] = TRUE;
		// Little funky, registering my own function to grab incomplete polls
		// since theres not such thing as 'elgg_list_entities_WITHOUT_relationship'
		$list = polls_list_incomplete($options);
	}

	if (!$list) {
		$return['content'] = elgg_view('polls/noresults');
	} else {
		$return['content'] = $list;
	}
	
	$return['content'] = $latest . $header . $return['content'];

	return $return;
}

/**
 * Get page components to list of the user's friends' polls
 *
 * @param int $user_guid
 * @return array
 */
function polls_get_page_content_friends($user_guid) {

	elgg_set_page_owner_guid($user_guid);
	$user = get_user($user_guid);

	$return = array();

	$return['filter_context'] = 'friends';
	$return['title'] = elgg_echo('polls:title:friendspolls');
	$return['layout'] = 'one_column_with_sidebar';

	$crumbs_title = elgg_echo('polls:title:ownedpoll', array($user->name));
	elgg_push_breadcrumb($crumbs_title, "pg/polls/owner/{$user->username}");
	elgg_push_breadcrumb(elgg_echo('polls:label:friends'));

	if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
		$return['content'] .= elgg_echo('friends:none:you');
		return $return;
	} else {
		$options = array(
			'type' => 'object',
			'subtype' => 'poll',
			'full_view' => FALSE,
			'limit' => 5
		);

		foreach ($friends as $friend) {
			$options['container_guids'][] = $friend->getGUID();
		}

		$list = elgg_list_entities($options);
		if (!$list) {
			$return['content'] = elgg_view('polls/noresults');
		} else {
			$return['content'] = $list;
		}
	}

	$header = elgg_view('page_elements/content_header', array(
		'context' => $return['filter_context'],
		'type' => 'poll',
		'all_link' => elgg_get_site_url() . "pg/polls",
		'mine_link' => elgg_get_site_url() . "pg/polls/owner/" . get_loggedin_user()->username,
		'friend_link' => elgg_get_site_url() . "pg/polls/friends/" . get_loggedin_user()->username,
		'new_link' => elgg_get_site_url() . "pg/polls/new/" . $container_guid,
	));
	
	// Get latest poll for display
	$latest = polls_get_latest_poll_content();
	
	$return['content'] = $latest . $header . $return['content'];
	
	return $return;
}

/**
 * Helper function to grab and display the latest poll
 * @return html
 */
function polls_get_latest_poll_content($container_guid = ELGG_ENTITIES_ANY_VALUE) {	
	$latest_poll = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'poll',
		'limit' => 1, 
		'container_guid' => $container_guid
	));
	
	if($latest_poll[0]) {
		$content = elgg_view_title(elgg_echo('polls:title:latest'));
		$content .= elgg_view('polls/poll_container', array('entity' => $latest_poll[0]));
	}
	
	return $content;
}

/**
 * Helper function to grab and filter out incomplete polls
 * @return array
 */
function polls_list_incomplete($options) {
	// Going to do some hacking.. 
	$offset = get_input('offset');

	// Store limit, and set to 0
	$limit = $options['limit'];
	$options['limit'] = 0;
	
	// Grab entities
	$entities = elgg_get_entities($options);
	
	// Remove completed
	foreach($entities as $key => $entity) {
		if (has_user_completed_poll(get_loggedin_user(), $entity)) {
			unset($entities[$key]);
		}
	}
	
	// Hack it all back together
	$return = elgg_view_entity_list(
		array_slice($entities, $offset, $limit), // Note to self, array_slice is awesome
		count($entities), 
		$offset,
		$limit, 
		$options['full_view'], 
		$options['view_type_toggle'], 
		$options['pagination']
	);	
	return $return;
}

/**
 * Helper function to determine if user has completed a poll
 */
function has_user_completed_poll($user, $poll) {
	return check_entity_relationship($user->getGUID(), HAS_VOTED_RELATIONSHIP, $poll->getGUID());
}

?>