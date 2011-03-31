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
 * @todo Can use a bit of cleanup
 */
?>

.elgg-river-item form.elgg-polls-vote {
	display: inline;
	background-color: transparent;
}

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

/** Resets and tweaks for poll sidebar in groups **/
.elgg-polls-sidebar .elgg-image-block {
	border-bottom: 1px dotted #CCC;
}

.elgg-polls-sidebar .elgg-button {
	display: inline-block;
}