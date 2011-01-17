<?php
/**
 * Polls Ajax Related JS
 * 
 * @package Polls
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2011
 * @link http://www.thinkglobalschool.com/
 * 
 */


$submit_vote_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/polls/vote');
$result_end_url = elgg_get_site_url() . 'pg/polls/ajax_result/';
$success_message = elgg_echo('polls:success:vote');
$spinner = "<div class='poll-results-loading'><img src='" . elgg_get_site_url() . "_graphics/ajax_loader_bw.gif' /></div>";

?>
<script type="text/javascript">
	

		function submitPollVote(form_data, guid) {
			var url = stripJunk("<?php echo $submit_vote_url; ?>");
			$.ajax({
				url: url,
				type: "POST",
				data: form_data,
				cache: false, 
				dataType: "html", 
				error: function() {
					console.log('Error');	
				},
				success: function(data) {
					if (data) {
						loadPollResults(data, guid);
					} else {
						console.log('Error with vote')
					}
				}
			});
		}
		
		function loadPollResults(guid) {
			var height = $("#poll-ajax-container-" + guid + " .poll-vote").height();
			var width = $("#poll-ajax-container-" + guid + "  .poll-vote").width();
			$("#poll-ajax-container-" + guid).html('<table class="poll-spinner" style="width:' + width + 'px; height: ' + height + 'px;"><tr><td style="width: 100%; height: 100%; vertical-align: middle; text-align: center;"><img src="<?php echo elgg_get_site_url() . "_graphics/ajax_loader_bw.gif"; ?>" /></td></tr></table>');
			$("#poll-ajax-container-" + guid).load("<?php echo $result_end_url; ?>" + guid, '', function() {
				$("#poll-ajax-container-" + guid).prepend('<p class="success_message"><?php echo $success_message; ?></center>');
			});
		}
	
		
		function stripJunk(text) {
			return text.replace("amp;", '');
		}
		
</script>