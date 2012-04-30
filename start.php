<?php
/**
 * Polls start.php
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 *
 *
 * @todo The model for the voting is a bit strange.  This is the current model:
 *	An annotation is created on the poll object with
 *		name		=	(int) $poll_option
 *		value		=	(int) $poll_option
 *		owner_guid	=	voting user guid
 * 
 *	A relationship is created between the poll object and the user with
 *		guid_one		=	voting user guid
 *		relationship	=	HAS_VOTED_RELATIONSHIP
 *		guid_two		=	poll
 *
 * Suggested model:
 *	Annotation created on the poll object with
 *		name		=	'vote'
 *		value		=	(int) $poll_option
 *		owner_guid	=	voting user guid
 *
 *	Relationships are unncessary. You can check if a user has voted by checking if an annotation
 *	exists on the poll named 'vote' owned by that user.  This would require an upgrade to be run
 *	that found all the poll objects and updated the annotation names on them to be "vote".
 *
 * @todo Description is accepted but doesn't seem to be used anywhere.
 *
 * @todo The owner has to vote before he can see the results.
 */

function polls_init() {
	
	// Constant for voted relationship
	define('HAS_VOTED_RELATIONSHIP', 'has_voted_for');
	
	// Include helpers
	require_once 'lib/polls_lib.php';
			
	// Extend CSS
	elgg_extend_view('css/elgg','polls/css');
	
	// Add in the JS
	elgg_extend_view('js/elgg', 'js/polls');
	
	// Page handler
	elgg_register_page_handler('polls', 'polls_page_handler');

	// Add menu items for logged in users
	if (elgg_is_logged_in()) {
		// Site menu
		elgg_register_menu_item('site', array(
			'name' => 'polls',
			'text' => elgg_echo('polls'),
			'href' => 'polls'
		));
		
		// Owner block
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'polls_owner_block_menu_setup');
	}

	// secondary tab filter menu for incomplete / complete polls
	elgg_register_menu_item('polls-status', array(
		'name' => 'incomplete',
		'text' => elgg_echo('polls:label:incomplete'),
		'href' => elgg_http_add_url_query_elements(current_page_url(), array('polls_status' => 'incomplete')),
		'selected' => (get_input('polls_status', 'incomplete') == 'incomplete'),
		'priority' => 1
	));

	elgg_register_menu_item('polls-status', array(
		'name' => 'complete',
		'text' => elgg_echo('polls:label:complete'),
		'href' => elgg_http_add_url_query_elements(current_page_url(), array('polls_status' => 'complete')),
		'selected' => (get_input('polls_status', 'incomplete') == 'complete'),
		'priority' => 2
	));

	elgg_register_plugin_hook_handler('register', 'menu:entity', 'polls_remove_entity_edit_link');

	
	// add the group pages tools
    add_group_tool_option('polls', elgg_echo('groups:enablepolls'), true);
	elgg_extend_view('page/elements/sidebar', 'polls/group_sidebar');
					
	// Register actions
	$action_path = dirname(__FILE__) . '/actions/polls';
	elgg_register_action('polls/save', "$action_path/save.php");
	elgg_register_action('polls/vote', "$action_path/vote.php");
	elgg_register_action('polls/delete', "$action_path/delete.php");
	
	// Setup url handler for polls
	elgg_register_entity_url_handler('object', 'poll', 'polls_url_handler');
	
	// Register type for search
	elgg_register_entity_type('object', 'poll');		

	return true;
}

/**
 * Dispatcher for polls.
 *
 * URLs take the form of
 *  All polls:        polls/all
 *  User's polls:     polls/owner/<username>
 *  Friends' polls:   polls/friends/<username>
 *  View poll:        polls/view/<guid>/<title>
 *  New poll:         polls/add/<guid> (container: user, group, parent)
 *  Edit poll:        polls/edit/<guid>
 *  Group polls:      polls/group/<guid>/owner
 *
 * Title is ignored
 *
 * @param array $page
 */
function polls_page_handler($page) {
	gatekeeper();
	// @TODO something better
	elgg_push_breadcrumb(elgg_echo('polls'), 'polls/all');
	elgg_push_context('polls');

	$pages = dirname(__FILE__) . '/pages/polls';
	
	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	switch ($page[0]) {
		case "owner":
			include "$pages/owner.php";
			break;

		case "friends":
			include "$pages/friends.php";
			break;

		case "view":
			set_input('guid', $page[1]);
			include "$pages/view.php";
			break;

		case "add":
			include "$pages/add.php";
			break;

		// can't edit polls
//		case "edit":
//			set_input('guid', $page[1]);
//			include "$pages/edit.php";
//			break;

		case 'group':
			group_gatekeeper();
			include "$pages/owner.php";
			break;

		case 'all':
		default:
			include "$pages/all.php";
			break;
	}

	elgg_pop_context();

	return true;
}

/**
 * Populates the ->getUrl() method for a poll
 *
 * @param ElggEntity entity
 * @return string request url
 */
function polls_url_handler($entity) {
	global $CONFIG;
	return $CONFIG->url . "polls/view/{$entity->guid}/";
}

/**
 * Add a user hover menu for polls
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function polls_owner_block_menu_setup($hook, $type, $return, $params) {
	$owner = $params['entity'];
	
	// Only display todo link for users or groups with enabled todos
	if (elgg_instanceof($owner, 'user')) {
		$title = elgg_echo('poll');
		$url = "polls/owner/{$owner->username}/";
		$return[] = new ElggMenuItem('polls', $title, $url);
	} elseif (elgg_instanceof($owner, 'group') && $owner->polls_enable == 'yes') {
		$title = elgg_echo('polls:label:grouppolls');
		$url = "polls/group/{$owner->getGUID()}/owner";
		$return[] = new ElggMenuItem('polls', $title, $url);
	}

	return $return;
}

/**
 * Remove the edit menu item from polls because you can't edit them.
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $return
 * @param unknown_type $params
 */
function polls_remove_entity_edit_link($hook, $type, $return, $params) {
	$entity = $params['entity'];

	// don't display edit link for polls
	if (elgg_instanceof($entity, 'object', 'poll')) {
		foreach ($return as $idx => $menu) {
			if ($menu->getName() == 'edit') {
				unset ($return[$idx]);
			}
		}
	}

	return $return;
}

elgg_register_event_handler('init', 'system', 'polls_init');
