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
 * Get the entities to show for based upon the filters
 *
 * @param int $owner_guid The GUID of the page owner or NULL for all polls
 * @return array
 */
function polls_get_page_content_list($container_guid = NULL) {
	$options = array(
		'type' => 'object',
		'subtype' => 'poll',
		'full_view' => FALSE,
		'limit' => 10
	);

	if ($container_guid) {
		$options['container_guid'] = $container_guid;
	}

	// Check status..
	// incomplete means "logged in user hasn't voted"
	if (get_input('polls_status', 'incomplete') == 'complete') {
		$options['relationship'] = HAS_VOTED_RELATIONSHIP;
		$options['relationship_guid'] = elgg_get_logged_in_user_guid();
		$options['inverse_relationship'] = FALSE;
		// Nice and easy here, just grab entities with the voted relationship
		$list = elgg_list_entities_from_relationship($options);
	} else {
		$options['pagination'] = TRUE;
		$db_prefix = elgg_get_config('dbprefix');

		// if the user hasn't voted, this relationship doesn't exist.
		$options['wheres'] = array(
			"(NOT EXISTS (
			SELECT 1 FROM {$db_prefix}entity_relationships polls_er
			WHERE
				polls_er.guid_one = '" . elgg_get_logged_in_user_guid() . "'
				AND polls_er.relationship = '" . HAS_VOTED_RELATIONSHIP . "'
				AND polls_er.guid_two = e.guid))"
		);
		// Little funky, registering my own function to grab incomplete polls
		// since theres not such thing as 'elgg_list_entities_WITHOUT_relationship'
		$list = elgg_list_entities($options);
	}

	if (!$list) {
		$list = elgg_view('polls/noresults');
	}
	
	return $list;
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
 *
 * @param mixed $container_guid The container_guid to get for
 * @return html
 */
function polls_get_latest_poll_content($container_guid = ELGG_ENTITIES_ANY_VALUE) {	
	$latest_poll = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'poll',
		'limit' => 1, 
		'container_guid' => $container_guid
	));
	
	if ($latest_poll[0]) {
		return elgg_view_entity($latest_poll[0], array('full_view' => true));
	} else {
		return '';
	}
}

/**
 * Helper function to determine if user has completed a poll
 */
function has_user_completed_poll($user, $poll) {
	return check_entity_relationship($user->getGUID(), HAS_VOTED_RELATIONSHIP, $poll->getGUID());
}

/**
 * Prepare the add/edit form variables
 *
 * @param ElggObject $object An object to base the values on.
 * @return array
 */
function polls_prepare_form_vars($object = null) {
	// input names => defaults
	$values = array(
		'title' => get_input('title', ''),
		'description' => '',
		'access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		// entity is added later
	);

	if ($object) {
		foreach (array_keys($values) as $field) {
			if (isset($object->$field)) {
				$values[$field] = $object->$field;
			}
		}
	}

	if (elgg_is_sticky_form('poll')) {
		$sticky_values = elgg_get_sticky_values('poll');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	if ($object) {
		$values['entity'] = $object;
	}

	elgg_clear_sticky_form('poll');

	return $values;
}