<?php
/**
 * Polls Container
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */

$poll_form = elgg_view('polls/forms/poll', $vars);
$poll = $vars['entity'];
?>
<div id='poll-ajax-container-<?php echo $poll->getGUID(); ?>'>
	<?php echo $poll_form; ?>
</div>