/**
 * Poll editform js
 * 
 */

// Global counter  for rows
var counter = null;

/** Make sure that new and existing remove buttons have the proper click event bound at all times **/
function bindRemoveRowClickHandler(table_id) {
	$("div#remove_row").each(
		function(index) {
			$(this).unbind('click.addit').bind('click.addit', function() {
				// Get the <tr> id so we know which row this particular click will remove
				// Here's the layout <tr> -> <td> -> <textarea> Thats why I have to parent().parent()
				// Maybe a more graceful way to do this? I'm a jQuery noob. 
			    var parent_id = $(this).parent().parent().attr('id');  
				removeOptionRowById(parent_id, table_id);
			  	bindRemoveRowClickHandler(table_id);
			  });
		}
	);
}

/** Add a row to the table **/
function addOptionRow(table_id) {
	// Id for new row
	var id = "row" + counter;
	
	// TD class
	var class = "";
	
	// Zebra
	if (counter % 2 == 0)
		class = "alt";
		
	// Row HTML
	var r = "";
	r += "<tr id='" + id + "'>";
	r += 	"<td><input type='text' name='" + counter + "' class='poll_input " + class + "'></input></td>";
	r += 	"<td style='vertical-align: middle;'><div id='remove_row' class='remove_img' onmouseout='this.className=\"remove_img\"'  onmouseover='this.className=\"remove_img_over\"'></div></td>";
	r += "</tr>";

	// Incremement counter
	counter++;
	
	// Append it
	$('#' + table_id + ' > tbody:last').append(r);
	
	// Set hidden input
	$('#num_rows').attr('value', counter);
}

/** Remove a row from the table, with given id **/
function removeOptionRowById(id, table_id) {
	
	// Decrement counter
	if (counter > 0)
		counter--;
		
	// Remove it
	$('#' + id).remove();	
	
	// Set hidden input
	$('#num_rows').attr('value', counter);
	
	// Fix the table and input id's
	updateTable(table_id);
}

/**
*	This function does the magic to fix the poll table
* 	after we've removed a row. 
**/
function updateTable(table_id) {
	// Row counter
	var i = 0;
	
	// Count rows
	var row_count = $("table#" + table_id + " tbody").children().length;
	
	$("table#" + table_id + " tbody").children().each(
		function() {
			// New id for rows
        	var child = $(this);
			child.attr("id", "row" + i); 
			
			// Column counter
			var j = 0;
			
			// Loop through columns
			child.children().each(
				function () {
					var child = $(this);
					//console.log("td", child);
					// Loop through and find textarea's
					child.children().each(
						function () {
							if ($(this).is('input')) {
								$(this).attr("name", i)
								
								// Zebra stripes
								$(this).removeClass('poll_input alt');
								var class = 'poll_input';
								var alt = '';
								if (i % 2 == 0)
									alt += "alt";
									
								$(this).addClass('poll_input '+alt);
							}
						}
					);
					j++;
				}
			);
			i++;
    	}
	);
}