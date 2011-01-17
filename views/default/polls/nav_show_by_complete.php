<div class="">
	<div class="elgg_horizontal_tabbed_nav margin_top">
		<center>
		<ul>
		<?php 
			$user = page_owner_entity();
			$status = get_input('status', 'incomplete');
			
			if (!in_array($status, array('complete', 'incomplete'))) {
				$status = 'incomplete';
			}
			
			echo "<li class='" . ($status == "incomplete" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "/{$user->username}?status=incomplete", 
													'text' => elgg_echo("polls:label:incomplete"), 
													'class' => 'poll')) . 
				 "</li>"; 
				
			echo "<li class='" . ($status == "complete" ? 'selected ' : '') . " edt_tab_nav'>" 
					. elgg_view('output/url', array('href' => $vars['url'] . $vars['return_url'] . "/{$user->username}?status=complete", 
													'text' => elgg_echo("polls:label:complete"), 
													'class' => 'poll')) . 
				 "</li>";
		?>
		</ul>
		</center>
	</div>
</div>
