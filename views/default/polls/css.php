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
?>


ul.elgg-polls-options li input[type=text] {
	width: 95%;
}

.elgg-polls-bottom {
	padding-bottom: 10px;
	margin-bottom: 10px;
	border-bottom: 1px dotted #CCCCCC;
}

table.elgg-polls-vote {
	margin-top: 15px;
	margin-bottom: 15px;
	width: auto;
	margin-left: auto;
	margin-right: auto;
	border: 1px solid #666666;
	padding: 10px;
	-webkit-box-shadow: 2px 1px 5px rgba(60, 6, 10, 0.8);
 	-moz-box-shadow: 2px 1px 5px rgba(60, 6, 10, 0.8);
}

table.elgg-polls-vote td.elgg-polls-ajax-loader {
	vertical-align: middle;
}

table.elgg-polls-vote span.elgg-ajax-loader {
	display: block;
}

table.elgg-polls-vote tr:nth-child(odd) {
	background-color:#fff; 
}
table.elgg-polls-vote tr:nth-child(even) {
	background-color:#eee; 
}

table.elgg-polls-vote .elgg-polls-foot {
	background-color:#fff; 
	text-align: center;
}

table.elgg-polls-vote a {
	font-weight: normal !important;
}

table.elgg-polls-vote td {
	padding: 4px;
}

table.elgg-polls-vote td.elgg-polls-title {
	text-align: center;
	font-weight: bold;
	font-size: 120%;
	padding: 15px;
	color: #444444;
}

table.elgg-polls-vote td.elgg-polls-option-num {
	width: 25px;
	padding-left: 15px;
}

table.elgg-polls-vote td.elgg-polls-option-text {
	width: auto;
	padding-left: 10px;
	padding-right: 10px;
}

table.elgg-polls-vote td.elgg-polls-option-input {
	width: 25px;
	padding-right: 15px;
}

table.elgg-polls-vote td.elgg-polls-option-count {
	width: 70px;
	padding-right: 15px;
}

table.elgg-polls-vote div.elgg-polls-owner-content {
	font-size: 80%;
	border: 1px solid #999999;
	background: #ffffff;
	padding: 5px;
	margin-top: 2px;
	margin-bottom: 2px;
	display: none;
	width: 350px;
}

table.elgg-polls-vote a.elgg-polls-show-link {
	font-size: 80%;
	cursor: pointer;
}

form.polls-vote-form {
	width: auto;
}

p.success-message {
	font-size: 110%;
	font-weight: bold;
	margin-left: auto;
	margin-right: auto;
	width: 250px;
	text-align: center;
}

p.elgg-polls-error {
	font-size: 90%;
	width: auto;
	color: red;
	font-weight: bold;
}

table#poll {
	width: 100%;
}

table#poll .elgg-polls-input {
	width: 97%;
	margin-bottom: 3px;
}

.elgg-polls-table a.remove_over {
	opacity:1;
	filter:alpha(opacity=100);
}

.elgg-polls-table a.remove {
	opacity:0.2;
	filter:alpha(opacity=20);
}

.elgg-polls-table .remove-img {
	opacity:0.2;
	filter:alpha(opacity=20);
	width: 20px;
	height: 20px;
	background-color: #000000;
	background-image: url("<?php echo elgg_get_site_url() . "mod/polls/graphics/minus.gif"; ?>");
}

.elgg-polls-table .remove-img-over {
	opacity:1;
	filter:alpha(opacity=100);
	width: 20px;
	height: 20px;
	background-color: #000000;
	background-image: url("<?php echo elgg_get_site_url() . "mod/polls/graphics/minus.gif"; ?>");
}


/* Spinner */
.elgg-polls-spinner {
	margin-top: 15px;
	margin-bottom: 15px;
	margin-left: auto;
	margin-right: auto;
	border: 1px solid #666666;
	padding: 10px;
	background: #FFFFFF;
	-webkit-box-shadow: 2px 1px 5px rgba(60, 6, 10, 0.8);
 	-moz-box-shadow: 2px 1px 5px rgba(60, 6, 10, 0.8);
}

/** Tweaks for poll sidebar in groups **/
.elgg-polls-sidebar .entity_listing_info {
	width: auto;
}