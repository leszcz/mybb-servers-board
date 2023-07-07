<?php

/********************************************************************************************************************************
*
*  Servers board (/admin/modules/config/serversboard.php)
*  Author: Krzysztof "Supryk" Supryczyński
*  Copyright: © 2013 - 2015 @ Krzysztof "Supryk" Supryczyński @ All rights reserved
*  
*  Website: 
*  Description: Show information about games online servers on index page and details about servers on subpage.
*
********************************************************************************************************************************/
/********************************************************************************************************************************
*
* This file is part of "Servers board" plugin for MyBB.
* Copyright © 2013 - 2015 @ Krzysztof "Supryk" Supryczyński @ All rights reserved
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Lesser General Public License for more details.
*
* You should have received a copy of the GNU Lesser General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
********************************************************************************************************************************/

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define("SERVERSBOARD_CORE_PATH", MYBB_ROOT."inc/serversboard/");

$page->add_breadcrumb_item($lang->servers_list, "index.php?module=config-serversboard"); 

if($mybb->input['action'] == "add" || $mybb->input['action'] == "edit" || !$mybb->input['action'])
{
    $sub_tabs['serversboard'] = [
        'title' => $lang->servers_list,
        'link' => "index.php?module=config-serversboard",
        'description' => $lang->servers_list_desc,
	];
	
    $sub_tabs['serversboard_add'] = [
        'title' => $lang->server_add,
        'link' => "index.php?module=config-serversboard&amp;action=add", 
        'description' => $lang->server_add_desc,
	];
	
	$query = $db->simple_select("settinggroups", "gid,name", "name = 'serversboard'");
	$settinggroups = $db->fetch_array($query);
	$lang->setting_group_serversboard_desc = str_replace(".", "", $lang->setting_group_serversboard_desc);
	$sub_tabs['serversboard_settinggroups'] = [
        'title' => $lang->setting_group_serversboard_desc,
        'link' => "index.php?module=config-settings&action=change&gid=".$settinggroups['gid'], 
        'description' => $lang->setting_group_serversboard_desc.".",
	];
}

$plugins->run_hooks("admin_config_serversboard_begin");

if($mybb->input['action'] == "add")
{
	$plugins->run_hooks("admin_config_serversboard_add");
	
	if($mybb->request_method == "post")
	{
		$plugins->run_hooks("admin_config_serversboard_add_commit");
		//  || !preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,6}/', $mybb->input['ip'], $match)
		if(!trim($mybb->input['ip']))
		{
			$errors[] = $lang->server_error_missing_ip;
		}

		if(!trim($mybb->input['type']))
		{
			$errors[] = $lang->server_error_missing_type;
		}
		
		if($mybb->input['type'] == "arma2" && !trim($mybb->input['arma2qport']))
		{
			$errors[] = $lang->server_error_missing_arma2qport;
		}	

		if($mybb->input['type'] == "arma3" && !trim($mybb->input['arma3qport']))
		{
			$errors[] = $lang->server_error_missing_arma3qport;
		}
		
		if($mybb->input['type'] == "bf3" && !trim($mybb->input['bf3qport']))
		{
			$errors[] = $lang->server_error_missing_bf3qport;
		}	

		if($mybb->input['type'] == "bf4" && !trim($mybb->input['bf4qport']))
		{
			$errors[] = $lang->server_error_missing_bf4qport;
		}
	
		if($mybb->input['type'] == "dayz" && !trim($mybb->input['dayzqport']))
		{
			$errors[] = $lang->server_error_missing_dayzqport;
		}
		
		if($mybb->input['type'] == "dayzmod" && !trim($mybb->input['dayzmodqport']))
		{
			$errors[] = $lang->server_error_missing_dayzmodqport;
		}
		
		if($mybb->input['type'] == "minecraft" && !trim($mybb->input['minecraftqport']))
		{
			$errors[] = $lang->server_error_missing_minecraftqport;
		}

		if($mybb->input['type'] == "mta" && !trim($mybb->input['mtaqport']))
		{
			$errors[] = $lang->server_error_missing_mtaqport;
		}
		
		if($mybb->input['type'] == "mumble" && !trim($mybb->input['mumbleqport']))
		{
			$errors[] = $lang->server_error_missing_mumbleqport;
		}
		
		if($mybb->input['type'] == "rust" && !trim($mybb->input['rustqport']))
		{
			$errors[] = $lang->server_error_missing_rustqport;
		}
		
		if($mybb->input['type'] == "terraria" && !trim($mybb->input['terrariaqport']))
		{
			$errors[] = $lang->server_error_missing_terrariaqport;
		}
		
		if($mybb->input['type'] == "teamspeak3" && !trim($mybb->input['ts3qport']))
		{
			$errors[] = $lang->server_error_missing_ts3qport;
		}
		
		if(!trim($mybb->input['offlinehostname']))
		{
			$errors[] = $lang->server_error_missing_offlinehostname;
		}
		
		if(!trim($mybb->input['disporder']) || !preg_match('/[0-9]{1,3}/', $mybb->input['disporder'], $match))
		{
			$errors[] = $lang->server_error_missing_disporder;
		}
		
		if(!trim($mybb->input['owner']))
		{
			$errors[] = $lang->server_error_missing_owner;
		}

		if(!$errors)
		{
			$server = [
			//	"sid" => "",
				"type" => $db->escape_string($mybb->input['type']),
				"ip" => $db->escape_string(trim($mybb->input['ip'])),
				"arma2qport" => $db->escape_string(trim($mybb->input['arma2qport'])),
				"arma3qport" => $db->escape_string(trim($mybb->input['arma3qport'])),
				"bf3qport" => $db->escape_string(trim($mybb->input['bf3qport'])),
				"bf4qport" => $db->escape_string(trim($mybb->input['bf4qport'])),
				"dayzqport" => $db->escape_string(trim($mybb->input['dayzqport'])),
		//		"dayzmodqport" => $db->escape_string(trim($mybb->input['dayzmodqport'])),
				"minecraftqport" => $db->escape_string(trim($mybb->input['minecraftqport'])),
				"mtaqport" => $db->escape_string(trim($mybb->input['mtaqport'])),
				"mumbleqport" => $db->escape_string(trim($mybb->input['mumbleqport'])),
				"rustqport" => $db->escape_string(trim($mybb->input['rustqport'])),
				"terrariaqport" => $db->escape_string(trim($mybb->input['terrariaqport'])),
				"ts3qport" => $db->escape_string(trim($mybb->input['ts3qport'])),
				"offlinehostname" => $db->escape_string($mybb->input['offlinehostname']),
				"cuthostname" => intval($mybb->input['cuthostname']),
				"disporder" => intval($mybb->input['disporder']),
				"field" => intval($mybb->input['field']),
				"field_link" => $db->escape_string($mybb->input['field_link']),
				"field_icon" => $db->escape_string($mybb->input['field_icon']),
				"forumid" => $db->escape_string($mybb->input['forumid']),
				"visible" => intval($mybb->input['visible']),
				"owner" => $db->escape_string($mybb->input['owner']),
				"new" => intval($mybb->input['new']),
				"new_color" => $db->escape_string($mybb->input['new_color']),
				"new_text" => $db->escape_string($mybb->input['new_text']),
				"lastupdate" => intval('0'),
			];
			
			$plugins->run_hooks("admin_config_serversboard_add_commit_start");
			
			$sid = $db->insert_query("serversboard", $server);
			
			$plugins->run_hooks("admin_config_serversboard_add_commit_end");

			log_admin_action($sid, $mybb->input['offlinehostname']);

			flash_message($lang->server_add_success, 'success');
			admin_redirect("index.php?module=config-serversboard");
		}
	}
	else
	{
		$query = $db->simple_select("serversboard", "MAX(disporder) AS max_disporder");
		$max_disporder = $db->fetch_field($query, "max_disporder");
		
		$mybb->input = array(
			"field" => "0",
			"visible" => "1",
			"owner" => $mybb->user['username'],
			"disporder" => $max_disporder + 1,
			"new" => "0",
			"cuthostname" => "0",
		);
	}
	
	$page->add_breadcrumb_item($lang->server_add);
	$page->output_header($lang->servers_list." - ".$lang->server_add);
	
    $sub_tabs['serversboard_add'] = [
        'title' => $lang->server_add,
        'link' => "index.php?module=config-serversboard&amp;action=add", 
        'description' => $lang->server_add_desc,
	];
	
	$page->output_nav_tabs($sub_tabs, 'serversboard_add');
	
	$query = $db->simple_select("serversboard"); 
	$server = $db->fetch_array($query);
	
	$form = new Form("index.php?module=config-serversboard&amp;action=add", "post");
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	
	$select_list = [
		"" => $lang->server_type_select,
		"arma3" => "Arma 3", 
		"bf3" => "Battlefield 3", 
		"bf4" => "Battlefield 4", 
		"cod4" => "Call of Duty 4",
		"cs16" => "Counter-Strike 1.6", 
		"cscz" => "Counter-Strike Condition Zero", 
		"css" => "Counter-Strike Source", 
		"csgo" => "Counter-Strike Global Offensive", 
		"dayz" => "DayZ", 
	//	"dayzmod" => "DayZ Mod", 
		"gmod" => "Garry's Mod", 
		"l4d" => "Left 4 Dead", 
		"l4d2" => "Left 4 Dead 2", 
		"minecraft" => "MineCraft",
		"mta" => "Multi Theft Auto", 
		"mumble" => "Mumble",
		"samp" => "San Andreas Multi Player", 
		"rust" => "Rust", 
		"terraria" => "Terraria", 
		"tf2" => "Team Fortress 2", 
		"tfc" => "Team Fortress Classic", 
		"teamspeak3" => "TeamSpeak 3", 
		"ventrilo" => "Ventrilo",
		"wet" => "Wolfenstein Enemy Territory",
	];

	$form_container = new FormContainer($lang->server_add);
	$form_container->output_row($lang->server_type."<em> *</em>",  $lang->server_type_desc, $form->generate_select_box('type', $select_list, $mybb->input['type'], array('id' => 'type')), 'type');
	$form_container->output_row($lang->server_ip."<em> *</em>", $lang->server_ip_desc, $form->generate_text_box('ip', $mybb->input['ip'], array('id' => 'ip')), 'ip');
	$form_container->output_row($lang->server_arma2qport."<em> *</em>",  $lang->server_arma2qport_desc, $form->generate_text_box('arma2qport', $mybb->input['arma2qport'], array('id' => 'arma2qport')), 'arma2qport', [], array('id' => 'row_serversboard_arma2qport'));
	$form_container->output_row($lang->server_arma3qport."<em> *</em>",  $lang->server_arma3qport_desc, $form->generate_text_box('arma3qport', $mybb->input['arma3qport'], array('id' => 'arma3qport')), 'arma3qport', [], array('id' => 'row_serversboard_arma3qport'));
	$form_container->output_row($lang->server_bf3qport."<em> *</em>",  $lang->server_bf3qport_desc, $form->generate_text_box('bf3qport', $mybb->input['bf3qport'], array('id' => 'bf3qport')), 'bf3qport', [], array('id' => 'row_serversboard_bf3qport'));
	$form_container->output_row($lang->server_bf4qport."<em> *</em>",  $lang->server_bf4qport_desc, $form->generate_text_box('bf4qport', $mybb->input['bf4qport'], array('id' => 'bf4qport')), 'bf4qport', [], array('id' => 'row_serversboard_bf4qport'));
	$form_container->output_row($lang->server_dayzqport."<em> *</em>",  $lang->server_dayzqport_desc, $form->generate_text_box('dayzqport', $mybb->input['dayzqport'], array('id' => 'dayzqport')), 'dayzqport', [], array('id' => 'row_serversboard_dayzqport'));
//	$form_container->output_row($lang->server_dayzmodqport."<em> *</em>",  $lang->server_dayzmodqport_desc, $form->generate_text_box('dayzmodqport', $mybb->input['dayzmodqport'], array('id' => 'dayzmodqport')), 'dayzmodqport', [], array('id' => 'row_serversboard_dayzmodqport'));
	$form_container->output_row($lang->server_minecraftqport."<em> *</em>",  $lang->server_minecraftqport_desc, $form->generate_text_box('minecraftqport', $mybb->input['minecraftqport'], array('id' => 'minecraftqport')), 'minecraftqport', [], array('id' => 'row_serversboard_minecraftqport'));
	$form_container->output_row($lang->server_mtaqport."<em> *</em>",  $lang->server_mtaqport_desc, $form->generate_text_box('mtaqport', $mybb->input['mtaqport'], array('id' => 'mtaqport')), 'mtaqport', [], array('id' => 'row_serversboard_mtaqport'));
	$form_container->output_row($lang->server_mumbleqport."<em> *</em>",  $lang->server_mumbleqport_desc, $form->generate_text_box('mumbleqport', $mybb->input['mumbleqport'], array('id' => 'mumbleqport')), 'mumbleqport', [], array('id' => 'row_serversboard_mumbleqport'));
	$form_container->output_row($lang->server_rustqport."<em> *</em>",  $lang->server_rustqport_desc, $form->generate_text_box('rustqport', $mybb->input['rustqport'], array('id' => 'rustqport')), 'rustqport', [], array('id' => 'row_serversboard_rustqport'));
	$form_container->output_row($lang->server_terrariaqport."<em> *</em>",  $lang->server_terrariaqport_desc, $form->generate_text_box('terrariaqport', $mybb->input['terrariaqport'], array('id' => 'terrariaqport')), 'terrariaqport', [], array('id' => 'row_serversboard_terrariaqport'));
	$form_container->output_row($lang->server_ts3qport."<em> *</em>",  $lang->server_ts3qport_desc, $form->generate_text_box('ts3qport', $mybb->input['ts3qport'], array('id' => 'ts3qport')), 'ts3qport', [], array('id' => 'row_serversboard_ts3qport'));
	$form_container->output_row($lang->server_offlinehostname."<em> *</em>", $lang->server_offlinehostname_desc, $form->generate_text_box('offlinehostname', $mybb->input['offlinehostname'], array('id' => 'offlinehostname')), 'offlinehostname');
	$form_container->output_row($lang->server_cuthostname, $lang->server_cuthostname_desc, $form->generate_numeric_field('cuthostname', $mybb->input['cuthostname'], array('id' => 'cuthostname')), 'cuthostname');
	$form_container->output_row($lang->server_disporder."<em> *</em>", $lang->server_disporder_desc, $form->generate_numeric_field('disporder', $mybb->input['disporder'], array('id' => 'disporder')), 'disporder');
	$form_container->output_row($lang->server_owner."<em> *</em>", $lang->server_owner_desc, $form->generate_text_box('owner', $mybb->input['owner'], array('id' => 'owner')), 'owner');
	$form_container->output_row($lang->server_visible, $lang->server_visible_desc, $form->generate_yes_no_radio('visible', $mybb->input['visible'], true));
	$form_container->output_row($lang->server_field, $lang->server_field_desc, $form->generate_yes_no_radio('field', $mybb->input['field'], true));
	$form_container->output_row($lang->server_field_link, $lang->server_field_link_desc, $form->generate_text_box('field_link', $mybb->input['field_link'], array('id' => 'field_link')), 'field_link');
	$form_container->output_row($lang->server_field_icon, $lang->server_field_icon_desc, $form->generate_text_box('field_icon', $mybb->input['field_icon'], array('id' => 'field_icon')), 'field_icon');
	$form_container->output_row($lang->server_new, $lang->server_new_desc, $form->generate_yes_no_radio('new', $mybb->input['new'], true));
	$form_container->output_row($lang->server_new_color, $lang->server_new_color_desc, $form->generate_text_box('new_color', $mybb->input['new_color'], array('id' => 'new_color')), 'new_color', [], array('id' => 'row_serversboard_new_color'));
	$form_container->output_row($lang->server_new_text, $lang->server_new_text_desc, $form->generate_text_box('new_text', $mybb->input['new_text'], array('id' => 'new_text')), 'new_text', [], array('id' => 'row_serversboard_new_text'));
	$form_container->output_row($lang->server_forumid, $lang->server_forumid_desc, $form->generate_forum_select('forumid', $mybb->input['forumid'], array( 'id' => 'forumid', 'main_option' => $lang->server_forumid_none), 1), 'forumid');
	//$form_container->output_row($lang->server_buddylist, $lang->server_buddylist_desc, $form->generate_text_area('buddylist', $mybb->input['buddylist'], array('id' => 'buddylist')), 'buddylist');
	$form_container->end();
	
	echo '
	<link rel="stylesheet" href="../jscripts/select2/select2.css">
	<script type="text/javascript" src="../jscripts/select2/select2.min.js?ver=1804"></script>
	<script type="text/javascript">
	<!--
	$("#owner").select2({
		placeholder: "'.$lang->search_user.'",
		minimumInputLength: 3,
		maximumSelectionSize: 3,
		multiple: false,
		ajax: { // instead of writing the function to execute the request we use Select2\'s convenient helper
			url: "../xmlhttp.php?action=get_users",
			dataType: \'json\',
			data: function (term, page) {
				return {
					query: term, // search term
				};
			},
			results: function (data, page) { // parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return {results: data};
			}
		},
		initSelection: function(element, callback) {
			var query = $(element).val();
			if (query !== "") {
				$.ajax("../xmlhttp.php?action=get_users&getone=1", {
					data: {
						query: query
					},
					dataType: "json"
				}).done(function(data) { callback(data); });
			}
		},
	});

  	$(\'[for=owner]\').click(function(){
		$("#owner").select2(\'open\');
		return false;
	});
	// -->
	</script>';

	$buttons[] = $form->generate_submit_button($lang->server_add_save);

	$form->output_submit_wrapper($buttons);
	$form->end();
	// var dayzmodqport_peeker = new Peeker($("#type"), $("#row_serversboard_dayzmodqport"), /dayzmod/, false);
	echo '<script type="text/javascript" src="./jscripts/peeker.js"></script>
<script type="text/javascript">
$(document).ready(function() {
var arma2qport_peeker = new Peeker($("#type"), $("#row_serversboard_arma2qport"), /arma2/, false);
var arma3qport_peeker = new Peeker($("#type"), $("#row_serversboard_arma3qport"), /arma3/, false);
var bf3qport_peeker = new Peeker($("#type"), $("#row_serversboard_bf3qport"), /bf3/, false);
var bf4qport_peeker = new Peeker($("#type"), $("#row_serversboard_bf4qport"), /bf4/, false);
var dayzqport_peeker = new Peeker($("#type"), $("#row_serversboard_dayzqport"), /dayz/, false);
var minecraftqport_peeker = new Peeker($("#type"), $("#row_serversboard_minecraftqport"), /minecraft/, false);
var mtaqport_peeker = new Peeker($("#type"), $("#row_serversboard_mtaqport"), /mta/, false);
var mumbleqport_peeker = new Peeker($("#type"), $("#row_serversboard_mumbleqport"), /mumble/, false);
var rustqport_peeker = new Peeker($("#type"), $("#row_serversboard_rustqport"), /rust/, false);
var terrariaqport_peeker = new Peeker($("#type"), $("#row_serversboard_terrariaqport"), /terraria/, false);
var ts3qport_peeker = new Peeker($("#type"), $("#row_serversboard_ts3qport"), /teamspeak3/, false);
});
</script>';

	$page->output_footer();
}


if($mybb->input['action'] == "edit")
{
	$query = $db->simple_select("serversboard", "*", "sid='".$mybb->get_input('sid', 1)."'");
	$server = $db->fetch_array($query);
	
	if(!$server['sid'])
	{
		flash_message($lang->server_edit_error, 'error');
		admin_redirect("index.php?module=config-serversboard");
	}
	
	$plugins->run_hooks("admin_config_serversboard_edit");
	//  || !preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\:[0-9]{1,6}/', $mybb->input['ip'], $match)
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['ip']))
		{
			$errors[] = $lang->server_error_missing_ip;
		}

		if(!trim($mybb->input['type']))
		{
			$errors[] = $lang->server_error_missing_type;
		}
		
		if($mybb->input['type'] == "arma2" && !trim($mybb->input['arma2qport']))
		{
			$errors[] = $lang->server_error_missing_arma2qport;
		}	

		if($mybb->input['type'] == "arma3" && !trim($mybb->input['arma3qport']))
		{
			$errors[] = $lang->server_error_missing_arma3qport;
		}
		
		if($mybb->input['type'] == "bf3" && !trim($mybb->input['bf3qport']))
		{
			$errors[] = $lang->server_error_missing_bf3qport;
		}	

		if($mybb->input['type'] == "bf4" && !trim($mybb->input['bf4qport']))
		{
			$errors[] = $lang->server_error_missing_bf4qport;
		}
	
		if($mybb->input['type'] == "dayz" && !trim($mybb->input['dayzqport']))
		{
			$errors[] = $lang->server_error_missing_dayzqport;
		}
		
		if($mybb->input['type'] == "dayzmod" && !trim($mybb->input['dayzmodqport']))
		{
			$errors[] = $lang->server_error_missing_dayzmodqport;
		}
		
		if($mybb->input['type'] == "minecraft" && !trim($mybb->input['minecraftqport']))
		{
			$errors[] = $lang->server_error_missing_minecraftqport;
		}
		
		if($mybb->input['type'] == "mta" && !trim($mybb->input['mtaqport']))
		{
			$errors[] = $lang->server_error_missing_mumbleqport;
		}

		if($mybb->input['type'] == "mumble" && !trim($mybb->input['mumbleqport']))
		{
			$errors[] = $lang->server_error_missing_mtaqport;
		}
		
		if($mybb->input['type'] == "rust" && !trim($mybb->input['rustqport']))
		{
			$errors[] = $lang->server_error_missing_rustqport;
		}
		
		if($mybb->input['type'] == "terraria" && !trim($mybb->input['terrariaqport']))
		{
			$errors[] = $lang->server_error_missing_terrariaqport;
		}
		
		if($mybb->input['type'] == "teamspeak3" && !trim($mybb->input['ts3qport']))
		{
			$errors[] = $lang->server_error_missing_ts3qport;
		}
		
		if(!trim($mybb->input['offlinehostname']))
		{
			$errors[] = $lang->server_error_missing_offlinehostname;
		}
		
		if(!trim($mybb->input['disporder']) || !preg_match('/[0-9]{1,3}/', $mybb->input['disporder'], $match))
		{
			$errors[] = $lang->server_error_missing_disporder;
		}
		
		if(!trim($mybb->input['owner']))
		{
			$errors[] = $lang->server_error_missing_owner;
		}

		if(!$errors)
		{
			$updated_server = [
				"type" => $db->escape_string($mybb->input['type']),
				"ip" => $db->escape_string(trim($mybb->input['ip'])),
				"arma2qport" => $db->escape_string(trim($mybb->input['arma2qport'])),
				"arma3qport" => $db->escape_string(trim($mybb->input['arma3qport'])),
				"bf3qport" => $db->escape_string(trim($mybb->input['bf3qport'])),
				"bf4qport" => $db->escape_string(trim($mybb->input['bf4qport'])),
				"dayzqport" => $db->escape_string(trim($mybb->input['dayzqport'])),
			//	"dayzmodqport" => $db->escape_string(trim($mybb->input['dayzmodqport'])),
				"minecraftqport" => $db->escape_string(trim($mybb->input['minecraftqport'])),
				"mtaqport" => $db->escape_string(trim($mybb->input['mtaqport'])),
				"mumbleqport" => $db->escape_string(trim($mybb->input['mumbleqport'])),
				"rustqport" => $db->escape_string(trim($mybb->input['rustqport'])),
				"terrariaqport" => $db->escape_string(trim($mybb->input['terrariaqport'])),
				"ts3qport" => $db->escape_string(trim($mybb->input['ts3qport'])),
				"offlinehostname" => $db->escape_string($mybb->input['offlinehostname']),
				"cuthostname" => intval($mybb->input['cuthostname']),
				"disporder" => intval($mybb->input['disporder']),
				"field" => intval($mybb->input['field']),
				"field_link" => $db->escape_string($mybb->input['field_link']),
				"field_icon" => $db->escape_string($mybb->input['field_icon']),
				"forumid" => $db->escape_string($mybb->input['forumid']),
				"visible" => intval($mybb->input['visible']),
				"owner" => $db->escape_string($mybb->input['owner']),
				"new" => intval($mybb->input['new']),
				"new_color" => $db->escape_string($mybb->input['new_color']),
				"new_text" => $db->escape_string($mybb->input['new_text']),
				"lastupdate" => intval('0'),
			];
			
			$plugins->run_hooks("admin_config_serversboard_edit_commit");
			
			$db->update_query("serversboard", $updated_server, "sid = '".intval($mybb->input['sid'])."'");
			
			log_admin_action($server['sid'], $mybb->input['offlinehostname']);

			flash_message($lang->server_edit_success, 'success');
			admin_redirect("index.php?module=config-serversboard");
		}
	}
	
	$page->add_breadcrumb_item($lang->server_edit);
	$page->output_header($lang->servers_list." - ".$lang->server_edit);
	
	$sub_tabs['serversboard_edit'] = [
        'title' => $lang->server_edit, 
        'link' => "index.php?module=config-serversboard&amp;action=edit", 
        'description' => $lang->server_edit_desc,
	];
	
	$page->output_nav_tabs($sub_tabs, 'serversboard_edit');
	$form = new Form("index.php?module=config-serversboard&amp;action=edit", "post");
	
	echo $form->generate_hidden_field("sid", $server['sid']);
	
	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input = $server;
	}
	
	$select_list = [
		"" => $lang->server_type_select,
		"arma3" => "Arma 3", 
		"bf3" => "Battlefield 3", 
		"bf4" => "Battlefield 4", 
		"cod4" => "Call of Duty 4",
		"cs16" => "Counter-Strike 1.6", 
		"cscz" => "Counter-Strike Condition Zero", 
		"css" => "Counter-Strike Source", 
		"csgo" => "Counter-Strike Global Offensive", 
		"dayz" => "DayZ", 
	//	"dayzmod" => "DayZ Mod", 
		"gmod" => "Garry's Mod", 
		"l4d" => "Left 4 Dead", 
		"l4d2" => "Left 4 Dead 2", 
		"minecraft" => "MineCraft", 
		"mta" => "Multi Theft Auto", 
		"mumble" => "Mumble", 
		"samp" => "San Andreas Multi Player", 
		"rust" => "Rust", 
		"terraria" => "Terraria", 
		"tf2" => "Team Fortress 2", 
		"tfc" => "Team Fortress Classic", 
		"teamspeak3" => "TeamSpeak 3", 
		"ventrilo" => "Ventrilo",
		"wet" => "Wolfenstein Enemy Territory",
	];

	$form_container = new FormContainer($lang->server_edit);
	$form_container->output_row($lang->server_type."<em> *</em>",  $lang->server_type_desc, $form->generate_select_box('type', $select_list, $mybb->input['type'], array('id' => 'type')), 'type');
	$form_container->output_row($lang->server_ip."<em> *</em>", $lang->server_ip_desc, $form->generate_text_box('ip', $mybb->input['ip'], array('id' => 'ip')), 'ip');
	$form_container->output_row($lang->server_arma2qport."<em> *</em>",  $lang->server_arma2qport_desc, $form->generate_text_box('arma2qport', $mybb->input['arma2qport'], array('id' => 'arma2qport')), 'arma2qport', [], array('id' => 'row_serversboard_arma2qport'));
	$form_container->output_row($lang->server_arma3qport."<em> *</em>",  $lang->server_arma3qport_desc, $form->generate_text_box('arma3qport', $mybb->input['arma3qport'], array('id' => 'arma3qport')), 'arma3qport', [], array('id' => 'row_serversboard_arma3qport'));
	$form_container->output_row($lang->server_bf3qport."<em> *</em>",  $lang->server_bf3qport_desc, $form->generate_text_box('bf3qport', $mybb->input['bf3qport'], array('id' => 'bf3qport')), 'bf3qport', [], array('id' => 'row_serversboard_bf3qport'));
	$form_container->output_row($lang->server_bf4qport."<em> *</em>",  $lang->server_bf4qport_desc, $form->generate_text_box('bf4qport', $mybb->input['bf4qport'], array('id' => 'bf4qport')), 'bf4qport', [], array('id' => 'row_serversboard_bf4qport'));
	$form_container->output_row($lang->server_dayzqport."<em> *</em>",  $lang->server_dayzqport_desc, $form->generate_text_box('dayzqport', $mybb->input['dayzqport'], array('id' => 'dayzqport')), 'dayzqport', [], array('id' => 'row_serversboard_dayzqport'));
//	$form_container->output_row($lang->server_dayzmodqport."<em> *</em>",  $lang->server_dayzmodqport_desc, $form->generate_text_box('dayzmodqport', $mybb->input['dayzmodqport'], array('id' => 'dayzmodqport')), 'dayzmodqport', [], array('id' => 'row_serversboard_dayzmodqport'));
	$form_container->output_row($lang->server_minecraftqport."<em> *</em>",  $lang->server_minecraftqport_desc, $form->generate_text_box('minecraftqport', $mybb->input['minecraftqport'], array('id' => 'minecraftqport')), 'minecraftqport', [], array('id' => 'row_serversboard_minecraftqport'));
	$form_container->output_row($lang->server_mtaqport."<em> *</em>",  $lang->server_mtaqport_desc, $form->generate_text_box('mtaqport', $mybb->input['mtaqport'], array('id' => 'mtaqport')), 'mtaqport', [], array('id' => 'row_serversboard_mtaqport'));
	$form_container->output_row($lang->server_mumbleqport."<em> *</em>",  $lang->server_mumbleqport_desc, $form->generate_text_box('mumbleqport', $mybb->input['mumbleqport'], array('id' => 'mumbleqport')), 'mumbleqport', [], array('id' => 'row_serversboard_mumbleqport'));
	$form_container->output_row($lang->server_rustqport."<em> *</em>",  $lang->server_rustqport_desc, $form->generate_text_box('rustqport', $mybb->input['rustqport'], array('id' => 'rustqport')), 'rustqport', [], array('id' => 'row_serversboard_rustqport'));
	$form_container->output_row($lang->server_terrariaqport."<em> *</em>",  $lang->server_terrariaqport_desc, $form->generate_text_box('terrariaqport', $mybb->input['terrariaqport'], array('id' => 'terrariaqport')), 'terrariaqport', [], array('id' => 'row_serversboard_terrariaqport'));
	$form_container->output_row($lang->server_ts3qport."<em> *</em>",  $lang->server_ts3qport_desc, $form->generate_text_box('ts3qport', $mybb->input['ts3qport'], array('id' => 'ts3qport')), 'ts3qport', [], array('id' => 'row_serversboard_ts3qport'));
	$form_container->output_row($lang->server_offlinehostname."<em> *</em>", $lang->server_offlinehostname_desc, $form->generate_text_box('offlinehostname', $mybb->input['offlinehostname'], array('id' => 'offlinehostname')), 'offlinehostname');
	$form_container->output_row($lang->server_cuthostname, $lang->server_cuthostname_desc, $form->generate_numeric_field('cuthostname', $mybb->input['cuthostname'], array('id' => 'cuthostname')), 'cuthostname');
	$form_container->output_row($lang->server_disporder."<em> *</em>", $lang->server_disporder_desc, $form->generate_numeric_field('disporder', $mybb->input['disporder'], array('id' => 'disporder')), 'disporder');
	$form_container->output_row($lang->server_owner."<em> *</em>", $lang->server_owner_desc, $form->generate_text_box('owner', $mybb->input['owner'], array('id' => 'owner')), 'owner');
	$form_container->output_row($lang->server_visible, $lang->server_visible_desc, $form->generate_yes_no_radio('visible', $mybb->input['visible'], true));
	$form_container->output_row($lang->server_field, $lang->server_field_desc, $form->generate_yes_no_radio('field', $mybb->input['field'], true));
	$form_container->output_row($lang->server_field_link, $lang->server_field_link_desc, $form->generate_text_box('field_link', $mybb->input['field_link'], array('id' => 'field_link')), 'field_link');
	$form_container->output_row($lang->server_field_icon, $lang->server_field_icon_desc, $form->generate_text_box('field_icon', $mybb->input['field_icon'], array('id' => 'field_icon')), 'field_icon');
	$form_container->output_row($lang->server_new, $lang->server_new_desc, $form->generate_yes_no_radio('new', $mybb->input['new'], true));
	$form_container->output_row($lang->server_new_color, $lang->server_new_color_desc, $form->generate_text_box('new_color', $mybb->input['new_color'], array('id' => 'new_color')), 'new_color', [], array('id' => 'row_serversboard_new_color'));
	$form_container->output_row($lang->server_new_text, $lang->server_new_text_desc, $form->generate_text_box('new_text', $mybb->input['new_text'], array('id' => 'new_text')), 'new_text', [], array('id' => 'row_serversboard_new_text'));
	$form_container->output_row($lang->server_forumid, $lang->server_forumid_desc, $form->generate_forum_select('forumid', $mybb->input['forumid'], array('id' => 'forumid', 'main_option' => $lang->server_forumid_none), 1), 'forumid');
	//$form_container->output_row($lang->server_buddylist, $lang->server_buddylist_desc, $form->generate_text_area('buddylist', $mybb->input['buddylist'], array('id' => 'buddylist')), 'buddylist');
	$form_container->end();
	
	echo '
	<link rel="stylesheet" href="../jscripts/select2/select2.css">
	<script type="text/javascript" src="../jscripts/select2/select2.min.js?ver=1804"></script>
	<script type="text/javascript">
	<!--
	$("#owner").select2({
		placeholder: "'.$lang->search_user.'",
		minimumInputLength: 3,
		maximumSelectionSize: 3,
		multiple: false,
		ajax: { // instead of writing the function to execute the request we use Select2\'s convenient helper
			url: "../xmlhttp.php?action=get_users",
			dataType: \'json\',
			data: function (term, page) {
				return {
					query: term, // search term
				};
			},
			results: function (data, page) { // parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to alter remote JSON data
				return {results: data};
			}
		},
		initSelection: function(element, callback) {
			var query = $(element).val();
			if (query !== "") {
				$.ajax("../xmlhttp.php?action=get_users&getone=1", {
					data: {
						query: query
					},
					dataType: "json"
				}).done(function(data) { callback(data); });
			}
		},
	});

  	$(\'[for=owner]\').click(function(){
		$("#owner").select2(\'open\');
		return false;
	});
	// -->
	</script>';

	$buttons[] = $form->generate_submit_button($lang->server_edit_save);

	$form->output_submit_wrapper($buttons);
	$form->end();
	// var dayzmodqport_peeker = new Peeker($("#type"), $("#row_serversboard_dayzmodqport"), /dayzmod/, false);
	echo '<script type="text/javascript" src="./jscripts/peeker.js"></script>
<script type="text/javascript">
$(document).ready(function() {
var arma2qport_peeker = new Peeker($("#type"), $("#row_serversboard_arma2qport"), /arma2/, false);
var arma3qport_peeker = new Peeker($("#type"), $("#row_serversboard_arma3qport"), /arma3/, false);
var bf3qport_peeker = new Peeker($("#type"), $("#row_serversboard_bf3qport"), /bf3/, false);
var bf4qport_peeker = new Peeker($("#type"), $("#row_serversboard_bf4qport"), /bf4/, false);
var dayzqport_peeker = new Peeker($("#type"), $("#row_serversboard_dayzqport"), /dayz/, false);
var minecraftqport_peeker = new Peeker($("#type"), $("#row_serversboard_minecraftqport"), /minecraft/, false);
var mtaqport_peeker = new Peeker($("#type"), $("#row_serversboard_mtaqport"), /mta/, false);
var mumbleqport_peeker = new Peeker($("#type"), $("#row_serversboard_mumbleqport"), /mumble/, false);
var rustqport_peeker = new Peeker($("#type"), $("#row_serversboard_rustqport"), /rust/, false);
var terrariaqport_peeker = new Peeker($("#type"), $("#row_serversboard_terrariaqport"), /terraria/, false);
var ts3qport_peeker = new Peeker($("#type"), $("#row_serversboard_ts3qport"), /teamspeak3/, false);
});
</script>';

	$page->output_footer();
}

if($mybb->input['action'] == "delete")
{
	$query = $db->simple_select("serversboard", "*", "sid='".$mybb->get_input('sid', 1)."'");
	$server = $db->fetch_array($query);
	
	if(!$server['sid'])
	{
		flash_message($lang->server_delete_error, 'error');
		admin_redirect("index.php?module=config-serversboard");
	}
	
	$plugins->run_hooks("admin_config_serversboard_delete");

	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=config-serversboard");
	}

	if($mybb->request_method == "post")
	{
		$db->delete_query("serversboard", "sid='{$server['sid']}'");
		
		$plugins->run_hooks("admin_config_serversboard_delete_commit");

		log_admin_action($server['sid'], $server['offlinehostname']);

		flash_message($lang->server_delete_success, 'success');
		admin_redirect("index.php?module=config-serversboard");
	}
	else
	{
		$page->output_confirm_action("index.php?module=config-serversboard&amp;action=delete&amp;sid={$server['sid']}", $lang->server_confirm_deletion);
	}
}

if($mybb->input['action'] == "update_order" && $mybb->request_method == "post")
{	
	if(!is_array($mybb->input['disporder']))
	{
		admin_redirect("index.php?module=config-serversboard");
	}

	$plugins->run_hooks("admin_config_serversboard_update_order");
	
	foreach($mybb->input['disporder'] as $sid => $order)
	{
		$update_query = [
			"disporder" => intval($order)
		];
		$db->update_query("serversboard", $update_query, "sid='".intval($sid)."'");
	}
	
	$plugins->run_hooks("admin_config_serversboard_update_order_commit");

	log_admin_action();

	flash_message($lang->servers_orders_updated_success, 'success');
	admin_redirect("index.php?module=config-serversboard");
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->servers_list);

	$page->output_nav_tabs($sub_tabs, 'serversboard');

	$form = new Form("index.php?module=config-serversboard&amp;action=update_order", "post");
	$table = new Table;
	$table->construct_header($lang->servers_name, ['width' => '40%']);
	$table->construct_header($lang->servers_status, ['width' => '10%']);
	$table->construct_header($lang->servers_ip, ['width' => '10%']);
	$table->construct_header($lang->servers_type, ['width' => '10%']);
	$table->construct_header($lang->servers_order, ['width' => '10%', 'class' => 'align_center']);
	$table->construct_header($lang->servers_options, ['width' => '10%', 'class' => 'align_center']);
	
	$query = $db->simple_select("serversboard", "*", "", ['order_by' => 'disporder']);
	while($server = $db->fetch_array($query))
	{
		switch($server['type'])
		{
			case "arma2":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['arma2qport'];			
			break;
			case "arma3":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['arma3qport'];			
			break;
			case "bf3":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['bf3qport'];			
			break;
			case "bf4":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['bf4qport'];			
			break;
			case "dayz":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['dayzqport'];			
			break;
			case "dayzmod":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['dayzmodqport'];			
			break;
			case "minecraft":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['minecraftqport'];			
			break;
			case "mta":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['mtaqport'];			
			break;
			case "mumble":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['mumbleqport'];			
			break;
			case "rust":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['rustqport'];				
			break;
			case "terraria":
				$ip = explode(":", $server['ip']);
				$joinport = $ip[1];
				$server['ip'] = $ip[0].":".$server['terrariaqport'];				
			break;
		}
		
		if($server['type'] == "teamspeak3")
		{
			$servers = [
				[
					'id' => $server['sid'],
					'type' => $server['type'],
					'host' => $server['ip'],
					'options' => [
						'master_server_port' => $server['ts3qport'],
					],
				],
			];	
		}
		else
		{
			$servers = [
				[
					'id' => $server['sid'],
					'type' => $server['type'],
					'host' => $server['ip'],
				],
			];
		}
		
		$results = [];
			
		if(TIME_NOW - ($mybb->settings['serversboard_cache_time'] * 60) < $server['lastupdate'])
		{
		//	$results = unserialize($server['cache']);
			$results = unserialize(base64_decode($server['cache']));
		}
		else
		{
			require_once SERVERSBOARD_CORE_PATH."Autoloader.php";
				
			$gq = new GameQ\GameQ;
			$gq->addServers($servers);
			$gq->setOption('timeout', 30);
			$gq->addFilter('normalise');
			$gq->addFilter('stripcolor');
			$results = $gq->process();
			
			$update_query = [
			//	"cache" 	  => $db->escape_string(serialize($results)),
				"cache" 	  => $db->escape_string(base64_encode(serialize($results))),
				"lastupdate"   => TIME_NOW,
			];
			$db->update_query("serversboard", $update_query, "sid='".$server['sid']."'");			
		} 
			
		foreach((array)$results as $data)
		
			if($data['gq_type'] == 'arma2')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'arma3')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'bf3')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'bf4')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'dayz')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'dayzmod')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'minecraft')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'mta')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'mumble')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'rust')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
			elseif($data['gq_type'] == 'terraria')
			{
				$ip= explode(":", $server['ip']);
				$server['ip'] = $ip[0].":".$joinport;	
			}
		
		if($data['gq_hostname'] == "") 
		{		
			$data['gq_hostname'] = $server['offlinehostname'];
			if($mybb->settings['serversboard_remove_host_onoff'] == '1') $data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);
			if($server['cuthostname'] != "" &&  $server['cuthostname'] > 1) $data['gq_hostname'] = (my_strlen($data['gq_hostname']) > $server['cuthostname']) ? my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "..." : $data['gq_hostname'];
			$data['gq_status'] = $lang->servers_offline;
		}
		elseif($data['gq_hostname'] != "") 
		{
			$data['gq_hostname'] = $data['gq_hostname'];
			if($mybb->settings['serversboard_remove_host_onoff'] == '1') $data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);
			if($server['cuthostname'] != "" &&  $server['cuthostname'] > 1) $data['gq_hostname'] = (my_strlen($data['gq_hostname']) > $server['cuthostname']) ? my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "..." : $data['gq_hostname'];
			$data['gq_status'] = $lang->servers_online;
			
			if($data['gq_numplayers'] > $server['recordplayers'] || $server['recordplayers'] > $data['gq_maxplayers'])
			{
				$updated_server = [
					"recordplayers" => $db->escape_string($data['gq_numplayers']),
				];
				$db->update_query("serversboard", $updated_server, "sid = '".$server['sid']."'");
			}
		}
	
		$table->construct_cell("<a href=\"index.php?module=config-serversboard&amp;action=edit&amp;sid={$server['sid']}\"><strong>{$data['gq_hostname']}</strong></a>");
		$table->construct_cell($data['gq_status']);
		$table->construct_cell($server['ip']);
		$table->construct_cell($server['type']);
		$table->construct_cell($form->generate_text_box("disporder[{$server['sid']}]", $server['disporder'], ['id' => 'disporder', 'style' => 'width: 80%', 'class' => 'align_center']));
		$popup = new PopupMenu("serversboard_{$server['sid']}", $lang->servers_options);
		$popup->add_item($lang->server_option_edit, "index.php?module=config-serversboard&amp;action=edit&amp;sid={$server['sid']}");
		$popup->add_item($lang->server_option_delete, "index.php?module=config-serversboard&amp;action=delete&amp;sid={$server['sid']}&amp;my_post_key={$mybb->post_code}", "return AdminCP.deleteConfirmation(this, '{$lang->server_popup_confirm_deletion}')");
		$table->construct_cell($popup->fetch(), ['class' => 'align_center']);
		$table->construct_row();
	}
	
	if($table->num_rows()  == 0)
	{
		$table->construct_cell($lang->no_servers, array('colspan' => 6));
		$table->construct_row();
		$no_results = true;
	}
	
	$table->output($lang->servers_list);

	if(!$no_results)
	{
		$buttons[] = $form->generate_submit_button($lang->save_servers_order);
		$form->output_submit_wrapper($buttons);
	}

	$form->end();

	$page->output_footer();
}

?>