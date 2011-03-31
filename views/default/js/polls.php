<?php
/**
 * Polls js
 */
?>
//<script>
elgg.provide('elgg.polls');

elgg.polls.init = function() {
	// add option button
	$('a.elgg-polls-add-option').live('click', elgg.polls.addOption);

	// remove option button
	$('ul.elgg-polls-options').delegate('a.elgg-polls-remove-option', 'click', elgg.polls.removeOption);

	// vote button
	$('input[type=submit].elgg-polls-vote').live('click', elgg.polls.submitVote);
};

/**
 * Adds an option to the polls input
 */
elgg.polls.addOption = function(e) {
	e.preventDefault();
	var li = '<li><input type="text" class="elgg-poll-option" name="poll_options[]">'
		+ '<a class="elgg-polls-remove-option"><span class="elgg-icon elgg-icon-delete"></span></a></li>';
	var $ul = $(this).parent().find('ul.elgg-polls-options');
	$ul.append(li);
}

/**
 * Removes an option from the poll input
 */
elgg.polls.removeOption = function(e) {
	e.preventDefault();
	$li = $(this).closest('li');
	$ul = $li.parent();
	$li.remove();
}

/**
 * Submits a vote througha ajax and updates the display
 */
elgg.polls.submitVote = function(e) {
	e.preventDefault();
	var $form = $(this).closest('form');
	// save the data before clearing the html for the ajax loader
	var formData = $form.serialize();
	var $table = $form.find('table');
	var $container = $form.closest('.elgg-polls-container');
	var oldHTML = $container.html();

	// maintain the width and height
	var w = $table.outerWidth();
	var h = $table.outerHeight();
	var html = '<tr><td class="elgg-polls-ajax-loader"><span class="elgg-ajax-loader"></span></td></tr>';
	
	$table.html(html).width(w).height(h);

	elgg.action($form.attr('action'), {
		data: formData,
		success: function(json) {
			// failed
			if (json.status < 0) {
				$container.html(oldHTML);
			} else {
				$container.html($(json.output)).prepend('<h3 class="center hidden">' + elgg.echo('polls:success:vote') + '</h3>');
				$container.find('h3:first').slideDown();
			}
		}
	});
}


elgg.register_hook_handler('init', 'system', elgg.polls.init);
//</script>