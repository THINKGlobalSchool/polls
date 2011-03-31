<?php

	/**
	 * To Do Group list
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
			
	$group = page_owner_entity();
	
	// Only display sidebar todo's if enabled
	if ($group->polls_enable == 'yes') {	
		// get the groups todo's
		$polls = elgg_get_entities(array('type' => 'object', 'subtype' => 'poll', 
											'container_guids' => page_owner(), 'limit' => 6));
										
										
		// Remove completed
		foreach ($polls as $idx => $poll) {
			if (has_user_completed_poll(get_loggedin_user(), $poll)) {
				unset($polls[$idx]);
			}
		}
?>
<div class="group_tool_widget poll poll-sidebar" style='height: auto; margin-bottom: 5px; min-height: 100%;'>
<span class="group_widget_link"><a href="<?php echo $vars['url'] . "pg/polls/group/" . page_owner_entity()->getGUID() . "/owner"; ?>"><?php echo elgg_echo('link:view:all')?></a></span>
<h3><?php echo elgg_echo('polls:label:grouppolls') ?></h3>
<?php	
		if($polls){
			foreach($polls as $poll){
			
				//get the owner
				$owner = $poll->getOwnerEntity();

				//get the time
				$friendlytime = friendly_time($poll->time_created);
		
			    $info = "<div class='entity_listing_icon'>" . elgg_view('profile/icon',array('entity' => $poll->getOwnerEntity(), 'size' => 'tiny')) . "</div>";

				//get the bookmark entries body
				$info .= "<div class='entity_listing_info'><p class='entity_title'><a href=\"{$poll->getURL()}\">{$poll->title}</a></p>";
				
				//get the user details
				$info .= "<p class='entity_subtext'><b>Created: {$friendlytime}</b></p>";
				$info .= "</div>";
				//display 
				echo "<div class='entity_listing clearfloat'>" . $info . "</div>";
			} 
		} 
		$create_poll = $vars['url'] . "pg/polls/new/" . page_owner_entity()->getGUID();
		echo "<p class='margin_top'><a class='action_button' href=\"{$create_poll}\">" . elgg_echo("poll:new") . "</a></p>";
		echo "</div>";
	
	}
