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
 */

function polls_init() {
	global $CONFIG;
	
	// Constant for voted relationship
	define('HAS_VOTED_RELATIONSHIP', 'has_voted_for');
	
	// Include helpers
	require_once 'lib/polls_lib.php';
			
	// Extend CSS
	elgg_extend_view('css/screen','polls/css');
	
	// Add in the JS
	elgg_extend_view('metatags', 'polls/ajaxpoll_js');
	
	// Add poll sidebar
	elgg_extend_view('group-extender/sidebar','polls/group_polls', 2);
	
	// Page handler
	register_page_handler('polls','polls_page_handler');

	// Add to tools menu
	add_menu(elgg_echo("polls"), $CONFIG->wwwroot . 'pg/polls');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','polls_submenus');
	
	// add the group pages tool option     
    add_group_tool_option('polls',elgg_echo('groups:enablepolls'),true);
					
	// Register actions
	elgg_register_action('polls/create', $CONFIG->pluginspath . 'polls/actions/create.php');
	elgg_register_action('polls/vote', $CONFIG->pluginspath . 'polls/actions/vote.php');
	elgg_register_action('polls/delete', $CONFIG->pluginspath . 'polls/actions/delete.php');
	
	// Setup url handler for polls
	register_entity_url_handler('polls_url_handler','object', 'poll');
	
	// Comment handler
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'poll_annotate_comments');
	
	// Profile hook	
	elgg_register_plugin_hook_handler('profile_menu', 'profile', 'polls_profile_menu');
	
	// Register type
	register_entity_type('object', 'poll');		

	return true;
	
}

/* Polls page handler */
function polls_page_handler($page) {
	global $CONFIG;
	set_context('polls');
	gatekeeper();

	elgg_push_breadcrumb(elgg_echo('polls'), "pg/polls"); // @TODO something better
	
	// Following the core blogs plugin page handler
	if (!isset($page[0])) {
		$page[0] = 'all';
	}
	
	$page_type = $page[0];
	
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
			$params = polls_get_page_content_view($page[1]);
			break;
		case 'new':
			$params = polls_get_page_content_edit($page_type, $page[1]);
			break;
		case 'ajax_result':
			$poll = get_entity($page[1]);
			if ($poll) {
				echo elgg_view('polls/poll_results', array('entity' => $poll));
			}
			exit; // Ajax, don't load anything else
			break;
		//case 'edit':
		//	$params = polls_get_page_content_edit($page_type, $page[1]);
		//	break;
		case 'group':
			$params = polls_get_page_content_list($page[1]);
			break;
		case 'all':
		default:
			$params = polls_get_page_content_list();
			break;
	}

	$params['sidebar'] .= isset($params['sidebar']) ? $params['sidebar'] : '';
	$params['content'] = elgg_view('navigation/breadcrumbs') . $params['content'];

	$body = elgg_view_layout($params['layout'], $params);

	echo elgg_view_page($params['title'], $body);
}
	
/**
 * Setup polls submenus
 */
function polls_submenus() {
	global $CONFIG;

	// all/yours/friends 
	elgg_add_submenu_item(array('text' => elgg_echo('polls:menu:yourpolls'), 
								'href' => elgg_get_site_url() . 'pg/polls/' . get_loggedin_user()->username), 'polls');
								
	elgg_add_submenu_item(array('text' => elgg_echo('polls:menu:friendspolls'), 
								'href' => elgg_get_site_url() . 'pg/polls/friends' ), 'polls');

	elgg_add_submenu_item(array('text' => elgg_echo('polls:menu:allpolls'), 
								'href' => elgg_get_site_url() . 'pg/polls/' ), 'polls');
	
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
 * Plugin hook to add polls's to users profile block
 * 	
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function polls_profile_menu($hook, $entity_type, $return_value, $params) {
	// Only display todo link for users or groups with enabled todos
	if ($params['owner'] instanceof ElggUser || $params['owner']->polls_enable == 'yes') {
		$return_value[] = array(
			'text' => elgg_echo('poll'),
			'href' => elgg_get_site_url() . "pg/polls/group/{$params['owner']->getGUID()}/owner",
		);
	}

	return $return_value;
}

register_elgg_event_handler('init', 'system', 'polls_init');
?>