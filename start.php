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
	
	// Add poll sidebar
	// @todo - don't know what this is...looks theme-specific
	elgg_extend_view('group-extender/sidebar', 'polls/group_polls', 2);
	
	// Page handler
	register_page_handler('polls', 'polls_page_handler');

	// Add menus
	// site menu for main tabs
	elgg_register_menu_item('site', array(
		'name' => 'polls',
		'text' => elgg_echo('polls'),
		'href' => 'pg/polls'
	));

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

	// @todo This is used almost exclusively in the profile page now. Do you mean user_hover?
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'polls_owner_block_menu_setup');
	elgg_register_plugin_hook_handler('prepare', 'menu:entity', 'polls_remove_entity_edit_link');

	
	// add the group pages tool option     
    add_group_tool_option('polls', elgg_echo('groups:enablepolls'), true);
					
	// Register actions
	$action_path = dirname(__FILE__) . '/actions/polls';
	elgg_register_action('polls/save', "$action_path/save.php");
	elgg_register_action('polls/vote', "$action_path/vote.php");
	elgg_register_action('polls/delete', "$action_path/delete.php");
	
	// Setup url handler for polls
	register_entity_url_handler('polls_url_handler', 'object', 'poll');
	
	// Comment handler
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'poll_annotate_comments');
	
	// Register type for search
	register_entity_type('object', 'poll');		

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
	
	
	
	switch ($page_type) {
		case 'owner':
			$user = get_user_by_username($page[1]);
			$params = polls_get_page_content_list($user->guid);
			break;

		case 'friends': 
			$user = get_user_by_username($page[1]);
			$params = polls_get_page_content_friends($user->guid);
			break;
		
		case 'view': 
			set_context('polls-detailed');
			$params = polls_get_page_content_view($page[1]);
			break;

		case 'new':
			// backward compatibility
		case 'add':
			$params = polls_get_page_content_edit($page_type, $page[1]);
			break;

		case 'ajax_result':
			$poll = get_entity($page[1]);
			if ($poll) {
				echo elgg_view('polls/poll_results', array('entity' => $poll));
			}
			exit; // Ajax, don't load anything else
			break;

		case 'group':
			$params = polls_get_page_content_list($page[1]);
			break;

		case 'all':
		default:
			$params = polls_get_page_content_list();
			break;
	}

	$body = elgg_view_layout($params['layout'], $params);

	echo elgg_view_page($params['title'], $body);
}

/**
 * Populates the ->getUrl() method for a poll
 *
 * @param ElggEntity entity
 * @return string request url
 */
function polls_url_handler($entity) {
	global $CONFIG;
	return $CONFIG->url . "pg/polls/view/{$entity->guid}/";
}

/**
 * Hook into the framework and provide comments on polls
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function poll_annotate_comments($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$full = $params['full'];
	
	if (
		($entity instanceof ElggEntity) &&	// Is the right type 
		($entity->getSubtype() == 'poll') &&  // Is the right subtype
		($full) // This is the full view
	)
	{
		// Display comments
		return elgg_view_comments($entity);
	}
	
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
	if ($owner instanceof ElggUser || $user->polls_enable == 'yes') {
		$title = elgg_echo('poll');
		$url = "pg/polls/group/{$owner->getGUID()}/owner";
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
		foreach ($return['default'] as $i => $menu) {
			if ($menu->getName() == 'edit') {
				unset ($return['default'][$i]);
			}
		}
	}

	return $return;
}

register_elgg_event_handler('init', 'system', 'polls_init');