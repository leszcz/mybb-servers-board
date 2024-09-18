<?php

/********************************************************************************************************************************
*
*  Servers board (/inc/plugins/serversboard.php)
*  Author: Krzysztof "Supryk" Supryczyński
*  Copyright: © 2013 - 2016 @ Krzysztof "Supryk" Supryczyński @ All rights reserved
*  
*  Website: 
*  Description: Show information about games online servers on index page and details about servers on subpage.
*
********************************************************************************************************************************/
/********************************************************************************************************************************
*
* This file is part of "Servers board" plugin for MyBB.
* Copyright © 2013 - 2016 @ Krzysztof "Supryk" Supryczyński @ All rights reserved
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
define("SERVERSBOARD_IMAGES_PATH", "./images/serversboard/");
define("PLUGIN_WEBSITE", "");
define("PLUGIN_AUTHOR", "Krzysztof \"Supryk\" Supryczyński, Tomasz Leszczyński");
define("PLUGIN_AUTHORNAME", "Supryk & l3szcz");
define("PLUGIN_AUTHORSITE", "");
define("PLUGIN_VERSION", "3.6.1");
define("COMPATIBILITY", "18*");
define("CODENAME", "servers_board");

$plugins->add_hook("admin_config_settings_manage", "serversboard_admin_lang_load");
$plugins->add_hook("admin_config_settings_change", "serversboard_admin_lang_load");
$plugins->add_hook("admin_config_settings_start", "serversboard_admin_lang_load");
$plugins->add_hook("admin_style_templates_set", "serversboard_admin_lang_load");
$plugins->add_hook("admin_config_menu", "serversboard_admin_config_menu");
$plugins->add_hook("admin_config_action_handler", "serversboard_admin_config_action_handler");
$plugins->add_hook("admin_home_menu_quick_access", "serversboard_admin_home_menu_quick_access");
$plugins->add_hook("admin_config_permissions", "serversboard_admin_config_permissions");
$plugins->add_hook("index_start", "serversboard");
$plugins->add_hook("portal_start", "serversboard");
$plugins->add_hook("serversboard_start", "serversboard_subpage");
$plugins->add_hook("serversboard_start", "serversboard");
$plugins->add_hook("build_friendly_wol_location_end", "serversboard_build_friendly_wol_location_end");
$plugins->add_hook("fetch_wol_activity_end", "serversboard_fetch_wol_activity_end");
$plugins->add_hook("global_start", "serversboard_global_start");
$plugins->add_hook("pre_output_page", "serversboard_thanks");

function serversboard_info()
{
    global $lang;
    $lang->load("config_serversboard");
	
	return [
		"name"			=> $lang->serversboard,
		"description"	=> $lang->serversboard_desc,
		"website"		=> PLUGIN_WEBSITE,
		"author"		=> PLUGIN_AUTHOR,
		"authorsite"	=> PLUGIN_AUTHORSITE,
		"version"		=> PLUGIN_VERSION,
		"compatibility" => COMPATIBILITY,
		"codename"  	=> CODENAME,
	];
}

function serversboard_is_installed()
{
	global $db;
	
	return $db->num_rows($db->simple_select("settinggroups", "*", "name=\"serversboard\""));
}

function serversboard_install()
{
	global $db, $lang, $mybb;
	$lang->load("config_serversboard");
	if(!file_exists(MYBB_ROOT."serversboard.php")) 
	{
		flash_message($lang->serversboard_upload_all_files, 'error');
		admin_redirect("index.php?module=config-plugins");
	}
	
	if(!file_exists(SERVERSBOARD_CORE_PATH."GameQ.php")) 
	{
		flash_message($lang->serversboard_upload_all_files, 'error');
		admin_redirect("index.php?module=config-plugins");
	}
	
	if(!$db->table_exists("serversboard"))
	{
		$db->write_query("CREATE TABLE ".TABLE_PREFIX."serversboard (
			`sid` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`ip` varchar(35) NOT NULL DEFAULT '0',
			`arma2qport` varchar(30) NOT NULL DEFAULT '',
			`arma3qport` varchar(30) NOT NULL DEFAULT '',
			`bf3qport` varchar(30) NOT NULL DEFAULT '',
			`bf4qport` varchar(30) NOT NULL DEFAULT '',
			`dayzqport` varchar(30) NOT NULL DEFAULT '',
			`dayzmodqport` varchar(30) NOT NULL DEFAULT '',
			`minecraftqport` varchar(30) NOT NULL DEFAULT '',
			`mtaqport` varchar(30) NOT NULL DEFAULT '',
			`mumbleqport` varchar(30) NOT NULL DEFAULT '',
			`rustqport` varchar(30) NOT NULL DEFAULT '',
			`terrariaqport` varchar(30) NOT NULL DEFAULT '',
			`ts3qport` varchar(30) NOT NULL DEFAULT '10011',
			`type` varchar(20) NOT NULL DEFAULT '0',
			`offlinehostname` varchar(100) NOT NULL DEFAULT '',
			`cuthostname` varchar(3) NOT NULL DEFAULT '',
			`disporder` int(10) NOT NULL DEFAULT '0',
			`field` int(1) NOT NULL default '0',
			`field_link` varchar(100) NOT NULL DEFAULT '',
			`field_icon` varchar(100) NOT NULL DEFAULT '',
			`visible` int(1) NOT NULL default '1',
			`new` int(1) NOT NULL default '0',
			`new_color` varchar(20) NOT NULL DEFAULT '',
			`new_text` varchar(20) NOT NULL DEFAULT '',
			`forumid` varchar(20) NOT NULL DEFAULT '',
			`buddylist` TEXT NULL,
			`cache` LONGTEXT NULL,
			`lastupdate` bigint(30) NOT NULL DEFAULT '0',
			`recordplayers` int(10) NOT NULL DEFAULT '0',
			`owner` varchar(100) NOT NULL DEFAULT '',
			`administrators` varchar(100) NOT NULL DEFAULT '',
			PRIMARY KEY (`sid`)
			) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		// ) ENGINE=MyISAM ".$db->build_create_table_collation().";");
	}

	$max_disporder = $db->fetch_field($db->simple_select("settinggroups", "MAX(disporder) AS max_disporder"), "max_disporder");
	
	$settinggroup = [
	//	"gid"					=> "",
		"name" 				=> "serversboard", 
		"title" 			=> $db->escape_string($lang->setting_group_serversboard),
		"description"		=> $db->escape_string($lang->setting_group_serversboard_desc),
		"disporder" 		=> $max_disporder + 1,
		"isdefault" 		=> "0",
	];
	
	$gid = $db->insert_query("settinggroups", $settinggroup);
	
	$settings = [];
	
	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_onoff",
		"title"			=> $db->escape_string($lang->setting_serversboard_onoff),
		"description"	=> $db->escape_string($lang->setting_serversboard_onoff_desc),
		"optionscode"	=> "onoff",
		"value"			=> "1",
		"disporder"		=> "1",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_index_onoff",
		"title"			=> $db->escape_string($lang->setting_serversboard_index_onoff),
		"description"	=> $db->escape_string($lang->setting_serversboard_index_onoff_desc),
		"optionscode"	=> "onoff",
		"value"			=> "1",
		"disporder"		=> "2",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_portal_onoff",
		"title"			=> $db->escape_string($lang->setting_serversboard_portal_onoff),
		"description"	=> $db->escape_string($lang->setting_serversboard_portal_onoff_desc),
		"optionscode"	=> "onoff",
		"value"			=> "1",
		"disporder"		=> "3",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_show_barsplayersnum_onoff",
		"title"			=> $db->escape_string($lang->setting_serversboard_show_barsplayersnum_onoff),
		"description"	=> $db->escape_string($lang->setting_serversboard_show_barsplayersnum_onoff_desc),
		"optionscode"	=> "onoff",
		"value"			=> "0",
		"disporder"		=> "4",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_remove_host",
		"title"			=> $db->escape_string($lang->setting_serversboard_remove_host),
		"description"	=> $db->escape_string($lang->setting_serversboard_remove_hostdesc),
		"optionscode"	=> "text",
		"value"			=> "@ gameslot.pl, @ pukawka.pl, ^ 1shot1kill.pl, @ multigamers.pl, @ liveserver.pl, @ hostplay.pl",
		"disporder"		=> "5",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$settings[] = [
	//	"sid"	 				=> "",
		"name"			=> "serversboard_summation_onoff",
		"title"			=> $db->escape_string($lang->setting_serversboard_summation_onoff),
		"description"	=> $db->escape_string($lang->setting_serversboard_summation_onoff_desc),
		"optionscode"	=> "onoff",
		"value"			=> "1",
		"disporder"		=> "6",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];

	$settings[] = [
	//	"sid"					=> "",
		"name"			=> "serversboard_cache_time",
		"title"			=> $db->escape_string($lang->setting_serversboard_cache_time),
		"description"	=> $db->escape_string($lang->setting_serversboard_cache_time_desc),
		"optionscode"	=> "numeric",
		"value"			=> "5",
		"disporder"		=> "7",
		"gid"			=> $gid,
		"isdefault" 	=> "0",
	];
	
	$db->insert_query_multiple("settings", $settings);
	
	rebuild_settings();  
	
	$templategroup = [
	//	"gid"			 	 =>  "",
        "prefix"	=> "serversboard",
        "title"		=> $db->escape_string("<lang:serversboard_templates>"),
	];
	
    $db->insert_query("templategroups", $templategroup);
	
	$templates = [];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard",
		"template"		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<thead>
<tr>
<td class="thead{$collapsedthead[\'serversboard\']}" colspan="8">
<div><strong>{$lang->serversboard} - {$mybb->settings[\'bbname\']}</strong></div>
</td>
</tr>
</thead>
<tbody style="{$collapsed[\'serversboard_e\']}" id="serversboard_e">
<td class="tcat" align="center"><span class="smalltext"><strong>{$lang->number}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><strong>{$lang->status}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><strong>{$lang->type}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><strong>{$lang->hostname}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><center><strong>{$lang->ip}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><center><strong>{$lang->players_slots}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><center><strong>{$lang->map}</strong></span></td>
<td class="tcat" align="center"><span class="smalltext"><center><strong>{$lang->more}</strong></span></td>
{$serversboard_row}
{$serversboard_summation}
</tbody>
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_row",
		"template" 		=> $db->escape_string('<tr>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_number\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_status\']}{$data[\'gq_new\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext" title="{$data[\'gq_desc\']}">{$data[\'gq_icon\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_hostname\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_ip\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_playersslots\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_mapname\']}</span></td>
<td class="{$altbg}" align="center"><span class="smalltext">{$data[\'gq_gt\']} {$data[\'gq_join\']} {$data[\'gq_field\']}{$data[\'gq_general\']} {$data[\'gq_page\']}</span></td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	 
	$templates[] = array(
	//	"tid" 					=> "",
		"title" 		=> "serversboard_row_empty",
		"template" 		=> $db->escape_string('<tr>
<td class="{$altbg}" align="center" colspan="8">
<span class="smalltext">{$lang->no_servers}</span>
</td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	);
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_summation",
		"template" 		=> $db->escape_string('<tr>
<td class="trow1" align="center" colspan="8">
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->together_servers}<span class="server serverssummation">{$servers}</span>
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->together_players}<span class="server serverssummation">{$gamers}</span>  
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->together_slots}<span class="server serverssummation">{$slots}</span> 
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->together_empty_slots}<span class="server serverssummation">{$emptyslots}</span>
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->procent_players}<span class="server serverssummation">{$procentgamers}</span>
&nbsp;&nbsp;&nbsp;&nbsp;{$lang->record_players}<span class="server serverssummation">{$recordgamers}</span>
</td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_subpage",
		"template" 		=> $db->escape_string('<html>
<head>
{$headerinclude}
<title>{$mybb->settings[\'bbname\']} - {$lang->serversboard}</title>
</head>
<body>
{$header}
{$serversboard}
{$footer}
</body>
</html>	'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more",
		"template" 		=> $db->escape_string('<html>
<head>
{$headerinclude}
<title>{$mybb->settings[\'bbname\']} - {$data[\'gq_hostname\']}</title>
</head>
<body>
{$header}
{$serversboard}
<div class="side" style="float: right;width: 24%;">
{$field}
{$map}
{$owner}
{$buddylist}
</div>
<div class="general" style="float: left;width: 75%;">
{$general}
{$players}
</div>
<br class="clear" />
{$footer}
</body>
</html>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_general",
		"template" 		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->general_informations}</strong></td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->hostname}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">	{$data[\'gq_hostname\']}	</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->status}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">	{$data[\'gq_status\']}{$data[\'gq_new\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->ip}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">	{$data[\'gq_ip\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->type}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_desc\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->players}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_bots\']} {$data[\'gq_numplayers\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->slots}</strong>	</td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_maxplayers\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->procent_players}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_procents\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->map}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_mapname\']}</td>
</tr>
<tr>
<td class="trow1" align="left" width="30%" valign="top"><strong>{$lang->record_players}</strong></td>
<td class="trow1" align="left" width="70%" valign="top">{$data[\'gq_recordplayers\']}</td>
</tr>
<tr>
<td class="trow1" align="center" width="100%" valign="top" colspan="8"><strong>{$lang->lastupdate} {$data[\'gq_lastupdate\']}. {$lang->nextupdate} {$data[\'gq_nextupdate\']}.</strong></td>
</tr>
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_field",
		"template" 		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->more}</strong></td>
</tr>
<tr>
<td class="trow2 post_content "><center>{$data[\'gq_gt\']} {$data[\'gq_join\']} {$data[\'gq_field\']}</center></td>
</tr>
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_map",
		"template" 		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->map} {$data[\'gq_mapname\']}</strong></td>
</tr>
<tr>
<td class="trow2 post_content "><center>{$data[\'gq_mapscreen\']}</center></td>
</tr>
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"			=> TIME_NOW,
	];
	
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_players",
		"template"		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->players_online}</strong></td>
</tr>
<td class="tcat" colspan="0"><span class="smalltext"><strong>{$lang->player_name}</strong></span></td>
<td class="tcat" colspan="0"><span class="smalltext"><strong>{$lang->player_time_online}</strong></span></td>
{$players_row}
{$serversboard_more_players_multipage}
</table><br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_players_row",
		"template" 		=> $db->escape_string('<tr><td class="trow2" align="left" width="30%" valign="top"><span class="smalltext">{$gracz}</span></td>
<td class="trow2" align="left" width="70%" valign="top"><span class="smalltext" title="">{$czas}</span></td></tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_players_row_empty",
		"template" 		=> $db->escape_string('<tr><td class="trow2" align="left" colspan="8"><span class="smalltext">{$lang->players_empty}</span></td></tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_players_multipage",
		"template" 		=> $db->escape_string('<tr>
<td class="trow1" align="left" colspan="2"><span class="smalltext">{$multipage}</span></td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_owner",
		"template" 		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead" colspan="2"><strong>{$lang->owner}</strong></td>
</tr>
<tr><td class="trow1">
<img src="{$owneravatar[\'image\']}" alt="" style="float: left;margin-right: 5px;" width="32 height="32" />
{$owner[\'profilelink\']}<br /><span>{$ownergroup[\'title\'] }</span>
</td></tr>
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_buddylist",
		"template" 		=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>{$lang->buddylist}</strong></td>
</tr>
<tr>
<td class="trow1">
<span class="smalltext">
<center>{$buddylist_row}</center>
</span>
</td>
</tr>
{$buddylist_button}
</table>
<br />'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_buddylist_row",
		"template" 		=> $db->escape_string('<span>{$buddy[\'profilelink\']}</span>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = array(
	//	"tid" 					=> "",
		"title" 				=> "serversboard_more_buddylist_row_empty",
		"template" 		=> $db->escape_string('<span>{$lang->buddylist_empty}</span>'),
		"sid" 				=> "-2",
		"version" 			=> "1800",
		"status"			=> "0",
		"dateline"			=> TIME_NOW,
	);
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_buddylist_button_joinbuddylist",
		"template" 		=> $db->escape_string('<form action="serversboard.php" method="post">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<input type="hidden" name="sid" value="{$server[\'sid\']}" />
<tr>
<td class="trow1">
<div align="center">
<input type="hidden" name="action" value="joinbuddylist" />
<input type="submit" class="server serverbutton" name="submit" value="{$lang->joinbuddylist}" />
</div>
</form>
</td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$templates[] = [
	//	"tid" 					=> "",
		"title" 		=> "serversboard_more_buddylist_button_lowerbuddylist",
		"template" 		=> $db->escape_string('<form action="serversboard.php" method="post">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<input type="hidden" name="sid" value="{$server[\'sid\']}" />
<tr>
<td class="trow1">
<div align="center">
<input type="hidden" name="action" value="lowerbuddylist" />
<input type="submit" class="server serverbutton" name="submit" value="{$lang->lowerbuddylist}" />
</div>
</form>
</td>
</tr>'),
		"sid" 			=> "-2",
		"version" 		=> "1800",
		"status"		=> "0",
		"dateline"		=> TIME_NOW,
	];
	
	$db->insert_query_multiple("templates", $templates);
	
	$style = ".server {
	display: inline-block;
	height: 16px;
	line-height: 16px;
	padding: 0 5px;
	font-size: 9px;
	font-weight: bold;
	text-transform: uppercase;
	color: white;
	text-shadow: rgba(0, 0, 0, 0.2) 0px -1px 0px;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	background-image: url(images/serversboard/highlight.png);
	background-repeat: repeat-x;
	background-position: 0 -1px;
}

.serveronline {
	background:green;
}

.serveroffline {
	background:red;
}

.servernumber {
	background:#0f0f0f;
}

.serverssummation {
	background:#0f0f0f;
}

.serverbutton {
	background:#0f0f0f;
}

.progress {
	height: 16px;
	overflow: hidden;
	background-color: #f7f7f7;
	background-image: -moz-linear-gradient(top,#f5f5f5,#f9f9f9);
	background-image: -webkit-gradient(linear,0 0,0 100%,from(#f5f5f5),to(#f9f9f9));
	background-image: -webkit-linear-gradient(top,#f5f5f5,#f9f9f9);
	background-image: -o-linear-gradient(top,#f5f5f5,#f9f9f9);
	background-image: linear-gradient(to bottom,#f5f5f5,#f9f9f9);
	background-repeat: repeat-x;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#fff5f5f5\',endColorstr=\'#fff9f9f9\',GradientType=0);
	-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
	-moz-box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
	box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
}

.progress .bar {
	float: left;
	width: 0;
	height: 100%;
	font-size: 12px;
	color: #fff;
	text-align: center; 
	text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
	background-color: #0e90d2;
	background-image: -moz-linear-gradient(top,#149bdf,#0480be);
	background-image: -webkit-gradient(linear,0 0,0 100%,from(#149bdf),to(#0480be));
	background-image: -webkit-linear-gradient(top,#149bdf,#0480be);
	background-image: -o-linear-gradient(top,#149bdf,#0480be);
	background-image: linear-gradient(to bottom,#149bdf,#0480be);
	background-repeat: repeat-x;
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ff149bdf\',endColorstr=\'#ff0480be\',GradientType=0); 
	-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15);
	-moz-box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15);
	box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15);
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	-webkit-transition: width .6s ease;
	-moz-transition: width .6s ease;
	-o-transition: width .6s ease;
	transition: width .6s ease
}

.progress .bar+.bar {
	-webkit-box-shadow: inset 1px 0 0 rgba(0,0,0,0.15),inset 0 -1px 0 rgba(0,0,0,0.15);
	-moz-box-shadow: inset 1px 0 0 rgba(0,0,0,0.15),inset 0 -1px 0 rgba(0,0,0,0.15);
	box-shadow: inset 1px 0 0 rgba(0,0,0,0.15),inset 0 -1px 0 rgba(0,0,0,0.15)
}

.progress-striped .bar {
	background-color: #149bdf;
	background-image: -webkit-gradient(linear,0 100%,100% 0,color-stop(0.25,rgba(255,255,255,0.15)),color-stop(0.25,transparent),color-stop(0.5,transparent),color-stop(0.5,rgba(255,255,255,0.15)),color-stop(0.75,rgba(255,255,255,0.15)),color-stop(0.75,transparent),to(transparent));
	background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image:-moz-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent); 
	background-image: -o-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);-webkit-background-size: 40px 40px;
	-moz-background-size: 40px 40px;
	-o-background-size: 40px 40px;
	background-size: 40px 40px
}

.progress-danger .bar,.progress .bar-danger {
	background-color: #dd514c;
	background-image: -moz-linear-gradient(top,#ee5f5b,#c43c35);
	background-image: -webkit-gradient(linear,0 0,0 100%,from(#ee5f5b),to(#c43c35));
	background-image: -webkit-linear-gradient(top,#ee5f5b,#c43c35);
	background-image: -o-linear-gradient(top,#ee5f5b,#c43c35);
	background-image: linear-gradient(to bottom,#ee5f5b,#c43c35);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ffee5f5b\',endColorstr=\'#ffc43c35\',GradientType=0)
}

.progress-danger.progress-striped .bar,.progress-striped .bar-danger {
	background-color: #ee5f5b;
	background-image: -webkit-gradient(linear,0 100%,100% 0,color-stop(0.25,rgba(255,255,255,0.15)),color-stop(0.25,transparent),color-stop(0.5,transparent),color-stop(0.5,rgba(255,255,255,0.15)),color-stop(0.75,rgba(255,255,255,0.15)),color-stop(0.75,transparent),to(transparent));
	background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -moz-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -o-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent)
}

.progress-success .bar,.progress .bar-success {
	background-color: #5eb95e;
	background-image: -moz-linear-gradient(top,#62c462,#57a957);
	background-image: -webkit-gradient(linear,0 0,0 100%,from(#62c462),to(#57a957));
	background-image: -webkit-linear-gradient(top,#62c462,#57a957);
	background-image: -o-linear-gradient(top,#62c462,#57a957);
	background-image: linear-gradient(to bottom,#62c462,#57a957);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#ff62c462\',endColorstr=\'#ff57a957\',GradientType=0)
}

.progress-success.progress-striped .bar,.progress-striped .bar-success {
	background-color: #62c462;
	background-image: -webkit-gradient(linear,0 100%,100% 0,color-stop(0.25,rgba(255,255,255,0.15)),color-stop(0.25,transparent),color-stop(0.5,transparent),color-stop(0.5,rgba(255,255,255,0.15)),color-stop(0.75,rgba(255,255,255,0.15)),color-stop(0.75,transparent),to(transparent));
	background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -moz-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -o-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent)
}


.progress-warning .bar,.progress .bar-warning {
	background-color: #faa732;
	background-image: -moz-linear-gradient(top,#fbb450,#f89406);
	background-image: -webkit-gradient(linear,0 0,0 100%,from(#fbb450),to(#f89406));
	background-image: -webkit-linear-gradient(top,#fbb450,#f89406);
	background-image: -o-linear-gradient(top,#fbb450,#f89406);
	background-image: linear-gradient(to bottom,#fbb450,#f89406);
	background-repeat: repeat-x;
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#fffbb450\',endColorstr=\'#fff89406\',GradientType=0)
}

.progress-warning.progress-striped .bar,.progress-striped .bar-warning {
	background-color: #fbb450;
	background-image: -webkit-gradient(linear,0 100%,100% 0,color-stop(0.25,rgba(255,255,255,0.15)),color-stop(0.25,transparent),color-stop(0.5,transparent),color-stop(0.5,rgba(255,255,255,0.15)),color-stop(0.75,rgba(255,255,255,0.15)),color-stop(0.75,transparent),to(transparent));
	background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -moz-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: -o-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
	background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent)
}";

	$stylesheet = [
	//	"sid"         		=> "",
		"name"         	=> "serversboard.css",
		"cachefile"		=> "serversboard.css",
		"tid"         	=> "1",
		"attachedto"   	=> "index.php|portal.php|serversboard.php",
		"stylesheet"   	=> $db->escape_string($style),
		'lastmodified' 	=> TIME_NOW
	];

	$db->insert_query("themestylesheets", $stylesheet);
	
	require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

	cache_stylesheet(1, "serversboard.css", $style);
	update_theme_stylesheet_list(1, false, true);
}

function serversboard_uninstall()
{
    global $db, $mybb, $cache, $page, $lang;
	$lang->load("config_serversboard");
	
	if($mybb->request_method != 'post')
	{
		$page->output_confirm_action("index.php?module=config-plugins&action=deactivate&uninstall=1&plugin=serversboard", $lang->serversboard_uninstall_message, $lang->serversboard_uninstall);
	}
	
	if($db->table_exists("serversboard") && !isset($mybb->input['no']))
	{
		$db->drop_table("serversboard");
	}
	
	$db->delete_query("settinggroups", "name = \"serversboard\"");
	$db->delete_query("settings", "name LIKE \"serversboard%\"");
	rebuild_settings();
	$db->delete_query("templategroups", "prefix = \"serversboard\"");
	$db->delete_query("templates", "title LIKE \"serversboard%\"");
	$db->delete_query("themestylesheets", "name= \"serversboard.css\"");
		
	require_once MYBB_ADMIN_DIR."inc/functions_themes.php";
    
    $query = $db->simple_select("themes", "tid");
    while($theme = $db->fetch_array($query))
    {
        @unlink(MYBB_ROOT."cache/themes/theme{$theme['tid']}/serversboard.css");
        @unlink(MYBB_ROOT."cache/themes/theme{$theme['tid']}/serversboard.min.css");
        update_theme_stylesheet_list($theme['tid'], false, true); 
    }
}

function serversboard_activate()
{	
	serversboard_deactivate();
	
	find_replace_templatesets("index", '#'.preg_quote('{$header}').'#', '{$header}'."\n".'{$serversboard}');
	find_replace_templatesets("portal", '#'.preg_quote('{$header}').'#', '{$header}'."\n".'{$serversboard}');
	change_admin_permission('config', 'serversboard', 1);
}

function serversboard_deactivate()
{	
	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	
	find_replace_templatesets('index', '#' . preg_quote("\n".'{$serversboard}') . '#', '', 0);
	find_replace_templatesets('portal', '#' . preg_quote("\n".'{$serversboard}') . '#', '', 0);
	change_admin_permission('config', 'serversboard', 0);
}

function serversboard_admin_lang_load()
{
    global $lang;
    $lang->load("config_serversboard");
} 
 
function serversboard_admin_config_menu(&$sub_menu)
{
    global $lang;
    $lang->load("config_serversboard");
	
    $sub_menu[] = array("id" => "serversboard", "title" => "$lang->serversboard", "link" => "index.php?module=config-serversboard");
} 

function serversboard_admin_config_action_handler(&$actions)
{	
    $actions['serversboard'] = array("active" => "serversboard", "file" => "serversboard.php");
}

function serversboard_admin_home_menu_quick_access(&$actions)
{
    global $lang;
    $lang->load("config_serversboard");
	
    $actions['serversboard'] = array("id" => "serversboard", "title" => $lang->serversboard, "link" => "index.php?module=config-serversboard");
}

function serversboard_admin_config_permissions(&$admin_permissions)
{
    global $lang;
    $lang->load("config_serversboard");
	
    $admin_permissions['serversboard'] = $lang->serversboard_admin_permissions;
}

function serversboard()
{
    global $db, $lang, $mybb, $templates, $theme, $serversboard;
	$lang->load("serversboard");
	
	if($mybb->settings['serversboard_onoff'] != "1")
	{
		return;
	}
	
	if($mybb->settings['serversboard_index_onoff'] != "1" && THIS_SCRIPT == "index.php")
	{
		return;
	}

	if($mybb->settings['serversboard_portal_onoff'] != "1" && THIS_SCRIPT == "portal.php")
	{
		return;
	}
	
	$altbg = alt_trow();
	$serversboard_index_row = "";
	
	$query = $db->simple_select("serversboard", "*", "visible=1", array('order_by' => 'disporder'));
	if(!$db->num_rows($query))
	{
		$servers = "0";
		$slots = "0";
		$gamers = "0";
		$emptyslots = "0";
		$procentgamers = "0 %";
		$recordgamers = "0";
		eval('$serversboard_row .= "'.$templates->get("serversboard_row_empty").'";');
	}
	else
	{
		require_once SERVERSBOARD_CORE_PATH."Autoloader.php";
		
		while($server 	= $db->fetch_array($query)) 
		{
			$servernumber++;
				
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
						"id"		=> $server['sid'],
						"type"		=> $server['type'],
						"host"		=> $server['ip'],
						"options"	=> [
							"master_server_port" => $server['ts3qport'],
							"query_port" => $server['ts3qport']
						],
					],
				];	
			}
			else
			{
				$servers = [
					[
						"id"	=> $server['sid'],
						"type"	=> $server['type'],
						"host"	=> $server['ip'],
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
				$gq = new GameQ\GameQ;
				$gq->addServers($servers);
				$gq->setOption('timeout', 30);
				$gq->addFilter('normalise');
				$gq->addFilter('stripcolor');
				$results = $gq->process();
				
				$update_query = [
				//	"cache" 	  => $db->escape_string(serialize($results)),
					"cache" 	  	=> $db->escape_string(base64_encode(serialize($results))),
					"lastupdate"	=> TIME_NOW,
				];
				$db->update_query("serversboard", $update_query, "sid='".$server['sid']."'");			
			} 
				
			foreach((array)$results as $data)
			{
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
					
				$data['gq_sid'] = $server['sid'];
				$data['gq_number'] = "<span class=\"server servernumber\">".$server['disporder']."</span>";
				$data['gq_icon'] = "<img src=\"".SERVERSBOARD_IMAGES_PATH."icons/".$data['gq_type'].".png\" style=\"vertical-align: middle;\"/>";
				$data['gq_ip'] = $server['ip'];

				if($data['gq_hostname'] == "")
				{
					$data['gq_status'] = "<span class=\"server serveroffline\">".$lang->server_offline."</span>";
					$data['gq_hostname'] = $server['offlinehostname'];
					$data['gq_recordplayers'] = $server['recordplayers'];
					$data['gq_numplayers'] = 0;
					$data['gq_maxplayers'] = 0;
					$data['gq_procents'] = "0 %";
				}
				elseif($data['gq_hostname'] != "") 
				{
					$data['gq_status'] = "<span class=\"server serveronline\">".$lang->server_online."</span>";
					$data['gq_online'] = '1';
					$data['gq_hostname'] = $data['gq_hostname'];
					
					if(($data['gq_numplayers'] > $server['recordplayers']) || ($server['recordplayers'] > $data['gq_maxplayers']))
					{
						$updated_server = array(
							"recordplayers" => $db->escape_string($data['gq_numplayers']),
						);
						
						$upid = $db->update_query("serversboard", $updated_server, "sid = '".$server['sid']."'");
					}

					$data['gq_numplayers'] = $data['gq_numplayers'];
					$data['gq_maxplayers'] = $data['gq_maxplayers'];
					$data['gq_procents'] = round($data['gq_numplayers'] / $data['gq_maxplayers'] * 100)." %";
					
					if($data['gq_type'] == 'teamspeak3')
					{
						for($d=0;$d<$data['gq_numplayers']; $d++)
						{
							if($data['players'][$d]['gq_name'] == "")
							{	
								$data['gq_numplayers'] = '0';
								$data['gq_maxplayers'] = $data['gq_maxplayers'];
								$data['gq_procents'] = "0 %";
							}
						}
					}
						
					if($data['gq_numplayers'] > $data['gq_maxplayers']) 
					{
						$data['gq_numplayers'] = $data['gq_maxplayers'];
						$data['gq_procents'] = "100 %";
					}
									
					if($data['num_bots'] > 0)
					{
						$data['gq_bots'] = "(".$data['num_bots'].")";
					}
				}	
					
				if($upid)
				{
					$data['gq_recordplayers'] = $updated_server['recordplayers'];
				}
				else
				{
					$data['gq_recordplayers'] = $server['recordplayers'];
				}
					
				$data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);

				if($server['cuthostname'] > "5")
				{
					if(my_strlen($data['gq_hostname']) > $server['cuthostname'])
					{
						$data['gq_hostname'] = my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "...";
					}
				}
					
				if($data['gq_mapname'] == "")
				{
					$data['gq_mapname'] = "- - -";
				}
				
				if($server['new'] == "1")
				{
					$data['gq_new'] = "&nbsp;<span class=\"server\" style=\"background:".$server['new_color'].";\">".$server['new_text']."</span>";
				}
					
				if($mybb->settings['serversboard_show_barsplayersnum_onoff'] == '1')
				{
					if($data['gq_maxplayers'] > 0) 
					{
						$procentpasek = round(($data['gq_numplayers'] / $data['gq_maxplayers'])*100);
					} 
					else 
					{
						$procentpasek = 0;
					}
						
					switch($procentpasek)
					{
						case $procentpasek <= 40:
							$full_off_color = 'success';
						break;
						case $procentpasek <= 80:
							$full_off_color = 'warning';
						break;
						case $procentpasek <= 100:
							$full_off_color = 'danger';
						break;
						case $procentpasek > 100:
							$full_off_color = 'danger';
						break;
						default:
							$full_off_color = 'success';
						break;
					}
				
					$data['gq_playersslots'] = "<div style=\"position: relative;\"><div class=\"progress progress-".$full_off_color." progress-striped\" style=\"margin-bottom: 0px;\"><div class=\"bar\" style=\"width: ".$procentpasek."%;\"></div><div style=\"position: absolute;width: 100%;\"><center>".$data['gq_bots']." ".$data['gq_numplayers']."/".$data['gq_maxplayers']." - ".$data['gq_procents']."</center></div></div></div>";
				}
				else
				{
					$data['gq_playersslots']  =	$data['gq_bots']." ".$data['gq_numplayers']."/".$data['gq_maxplayers']." - ".$data['gq_procents'];
				}
				
				if($server['field_link'] != '' && $server['field_icon'] != '' && $server['field'] != '0')
				{
					$data['gq_field'] = '<a href="'.$server['field_link'].'" target="_blank"><img src="'.$server['field_icon'].'" style="vertical-align: middle;"/></a>';
				}
					
				$data['gq_page'] = '<a href="serversboard.php?action=more_information&sid='.$server['sid'].'"><img src="'.SERVERSBOARD_IMAGES_PATH.'page.png" style="vertical-align: middle;"/></a>';
					
				if($server['forumid'] && $server['forumid'] != "-1")
				{
					$data['gq_hostname'] = '<a href="forumdisplay.php?fid='.$server['forumid'].'">'.$data['gq_hostname'].'</a>';
				}

					if($data['gq_type'] == 'arma2')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Arma 2';
					} 
					elseif($data['gq_type'] == 'arma3')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Arma 3';
					} 
					elseif($data['gq_type'] == 'bf3')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'Battlefield 3';
					} 
					elseif($data['gq_type'] == 'bf4')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'Battlefield 4';
					} 
					elseif($data['gq_type'] == 'cod4')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="xfire:join?game=cod4mp&server='.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Call of Duty 4';
					} 
					elseif($data['gq_type'] == 'cs16')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Counter-Strike 1.6';
					} 
					elseif($data['gq_type'] == 'cscz')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Counter-Strike Condition Zero';
					} 
					elseif($data['gq_type'] == 'css')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Counter-Strike Source';
					} 
					elseif($data['gq_type'] == 'csgo')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Counter-Strike Global Offensive';
					} 
					elseif($data['gq_type'] == 'dayz')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'DayZ';
					} 
					elseif($data['gq_type'] == 'dayzmod')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'DayZ Mod';
					} 
					elseif($data['gq_type'] == 'gmod')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = "Garry's Mod";
					}
					elseif($data['gq_type'] == 'l4d')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Left 4 Dead';
					} 
					elseif($data['gq_type'] == 'l4d2')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Left 4 Dead 2';
					} 
					elseif($data['gq_type'] == 'minecraft')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'MineCraft';
					} 
					elseif($data['gq_type'] == 'minequery')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						$data['gq_desc'] = 'MineCraft';
					} 
					elseif($data['gq_type'] == 'mta')
					{
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="mtasa://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'mta.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Multi Theft Auto';
					} 
					elseif($data['gq_type'] == 'mumble')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="mumble://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'mumble.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Mumble';
					} 
					elseif($data['gq_type'] == 'samp')
					{
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="samp://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'samp.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'San Andreas Multi Player';
					} 
					elseif($data['gq_type'] == 'rust')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Rust';
					} 
					elseif($data['gq_type'] == 'terraria')
					{
						if($data['gq_online'] == '1')
						{
							$ip = explode(":", $server['ip']);
							$data['gq_join'] = '<a href="steam://rungameid/105600// -j '.$ip[0].' -p '.$ip[1].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Terraria';
					} 
					elseif($data['gq_type'] == 'tf2')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Team Fortress 2';
					} 
					elseif($data['gq_type'] == 'tfc')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Team Fortress Classic';
					} 
					elseif($data['gq_type'] == 'teamspeak3')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="ts3server://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'teamspeak3.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'TeamSpeak 3';
					} 
					elseif($data['gq_type'] == 'ventrilo')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
							$data['gq_join'] = '<a href="ventrilo://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'ventrilo.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Ventrilo';
					} 
					elseif($data['gq_type'] == 'wet')
					{
						$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
						if($data['gq_online'] == '1')
						{
						//	$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
						}
						$data['gq_desc'] = 'Wolfenstein Enemy Territory';
					} 
					
				eval('$serversboard_row .= "'.$templates->get("serversboard_row").'";');	
				$altbg = alt_trow();
					
				$servers = $servernumber;
				$slots = $slots + $data['gq_maxplayers'];
				$gamers = $gamers + $data['gq_numplayers'];
				$emptyslots = $slots - $gamers;
				$recordgamers = $recordgamers + $data['gq_recordplayers'];
				
				if($slots == '0')
				{
					$procentgamers = "0 %";
				}
				else
				{
					$procentgamers = round(($gamers / $slots) * 100);
					$procentgamers = $procentgamers." %";
				}
			}	
		}
	}
	
	if($mybb->settings['serversboard_summation_onoff'] == '1')
	{
		eval('$serversboard_summation = "'.$templates->get('serversboard_summation').'";');
	}
		
	eval('$serversboard = "'.$templates->get('serversboard').'";');
}

function serversboard_subpage()
{
	global $db, $lang, $mybb, $theme, $templates, $header, $footer, $headerinclude, $serversboard, $server, $cache;
	$lang->load("serversboard");
	
	if(!$mybb->input['action'])
	{
		serversboard();
		add_breadcrumb($lang->serversboard, "serversboard.php"); 
		eval("\$page = \"".$templates->get("serversboard_subpage")."\";"); 
		output_page($page); 
	}
	elseif($mybb->input['action'] == "more_information")
	{
		$query = $db->simple_select("serversboard", "*", "sid='".$mybb->get_input('sid', 1)."' AND visible=1");
		if(!$db->num_rows($query))
		{
			error($lang->no_that_server);
		}
		else
		{
			serversboard();
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
							"id" 	  => $server['sid'],
							"type" => $server['type'],
							"host" => $server['ip'],
							"options" => [
								"master_server_port" => $server['ts3qport'],
								"query_port" => $server['ts3qport'],
							],
						],
					];	
				}
				else
				{
					$servers = [
						[
							"id" 	 => $server['sid'],
							"type" => $server['type'],
							"host" => $server['ip'],
						],
					];
				}
				
				$results = array();
			
				if(TIME_NOW - ($mybb->settings['serversboard_cache_time'] * 60) < $server['lastupdate'])
				{
				//	$results = unserialize($server['cache']);
					$results = unserialize(base64_decode($server['cache']));
				}
				else
				{					
					$gq = new GameQ\GameQ;
					$gq->addServers($servers);
					$gq->setOption('timeout', 30);
					$gq->addFilter('normalise');
					$gq->addFilter('stripcolor');
					$results = $gq->process();
				
					$update_query = [
					//	"cache"			=> $db->escape_string(serialize($results)),
						"cache"			=> $db->escape_string(base64_encode(serialize($results))),
						"lastupdate"	=> TIME_NOW,
					];
					
					$db->update_query("serversboard", $update_query, "sid='".$server['sid']."'");			
				} 
			
				foreach((array)$results as $data)
				{
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
				
					$data['gq_sid'] = $server['sid'];
					$data['gq_number'] = "<span class=\"server servernumber\">".$server['disporder']."</span>";
					$data['gq_icon'] = "<img src=\"".SERVERSBOARD_IMAGES_PATH."icons/".$data['gq_type'].".png\" style=\"vertical-align: middle;\"/>";
					$data['gq_ip'] = $server['ip'];
					$data['gq_nextupdate'] = nice_time(($mybb->settings['serversboard_cache_time'] * 60) - (TIME_NOW - $server['lastupdate']));
					if($data['gq_nextupdate'] == '' || $data['gq_nextupdate'] == '0')
					{
						$data['gq_nextupdate'] = nice_time(($mybb->settings['serversboard_cache_time'] * 60));
					}
					$data['gq_lastupdate'] = my_date($mybb->settings['dateformat'] . " " . $mybb->settings['timeformat'], $server['lastupdate']);
	
					if($data['gq_hostname'] == "") 
					{
						$data['gq_status'] = "<span class=\"server serveroffline\">".$lang->server_offline."</span>";
						$data['gq_hostname'] = $server['offlinehostname'];
						$data['gq_recordplayers'] = $server['recordplayers'];
						$data['gq_numplayers'] = 0;
						$data['gq_maxplayers'] = 0;
						$data['gq_procents'] = "0 %";
						$data['gq_mapname'] = "- - -";
						$data['gq_nextupdate'] = nice_time(($mybb->settings['serversboard_cache_time'] * 60) - (TIME_NOW - $server['lastupdate']));
						eval('$players_row .= "'.$templates->get("serversboard_more_players_row_empty").'";');
						eval('$players = "'.$templates->get("serversboard_more_players").'";');
					}
					elseif($data['gq_hostname'] != "") 
					{
						$data['gq_status'] = "<span class=\"server serveronline\">".$lang->server_online."</span>";
						$data['gq_online'] = '1';
						$data['gq_hostname'] = $data['gq_hostname'];
				
						if(($data['gq_numplayers'] > $server['recordplayers']) || ($server['recordplayers'] > $data['gq_maxplayers']))
						{
							$updated_server = array(
								"recordplayers" => $db->escape_string($data['gq_numplayers']),
							);
							
							$upid = $db->update_query("serversboard", $updated_server, "sid = '".$server['sid']."'");
						}

						$data['gq_numplayers'] = $data['gq_numplayers'];
						$data['gq_maxplayers'] = $data['gq_maxplayers'];
						$data['gq_procents'] = round($data['gq_numplayers'] / $data['gq_maxplayers'] * 100)." %";
				
						if($data['gq_type'] == 'teamspeak3')
						{
							for($d=0;$d<$data['gq_numplayers']; $d++)
							{
								if($data['players'][$d]['gq_name'] == "")
								{	
									$data['gq_numplayers'] = '0';
									$data['gq_maxplayers'] = $data['gq_maxplayers'];
									$data['gq_procents'] = '0 %';
								}
							}
						}
				
						if($data['gq_numplayers'] > $data['gq_maxplayers']) 
						{
							$data['gq_numplayers'] = $data['gq_maxplayers'];
							$data['gq_procents'] = '100 %';
						}
				
						if($data['gq_numplayers'] > '0')
						{	
							$statuscount = $data['gq_numplayers'];
							$perpage = 10;
							$page = $mybb->get_input('page', 1);
							if($page > 0)
							{
								$start = ($page-1) * $perpage;
								$pages = $statuscount / $perpage;
								$pages = ceil($pages);
								if($page > $pages || $page <= 0)
								{
									$start = 0;
									$page = 1;
								}
							}
							else
							{
								$start = 0;
								$page = 1;
							}
							$end = $start + $perpage;
							$lower = $start+1;
							$upper = $end;
							if($upper > $statuscount)
							{
								$upper = $statuscount;
							}
							$multipage = multipage($statuscount, $perpage, $page, "serversboard.php?action=more_information&sid={$data['gq_sid']}");
							$data['players'] = array_slice($data['players'], $start, $perpage); 
							
							if($data['gq_numplayers'] > $perpage)
							{
									eval('$serversboard_more_players_multipage = "'.$templates->get("serversboard_more_players_multipage").'";');
							}
								
							for ($d=0;$d<count($data['players']); $d++)
							{
								$gracz = htmlspecialchars_uni($data['players'][$d]['gq_name']);
								$czas = $data['players'][$d]['time'];
								$czas = nice_time($czas);
								eval('$players_row .= "'.$templates->get("serversboard_more_players_row").'";');
							}
						}
						else
						{
							eval('$players_row .= "'.$templates->get("serversboard_more_players_row_empty").'";');
						}
				
						eval('$players = "'.$templates->get("serversboard_more_players").'";');
			
						if($data['num_bots'] > 0)
						{
							$data['gq_bots'] = "(".$data['num_bots'].")";
						}
					}
			
					if($upid)
					{
						$data['gq_recordplayers'] = $updated_server['recordplayers'];
					}
					else
					{
						$data['gq_recordplayers'] = $server['recordplayers'];
					}
						
					$data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);

					if($server['cuthostname'] > "5")
					{
						if(my_strlen($data['gq_hostname']) > $server['cuthostname'])
						{
							$data['gq_hostname'] = my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "...";
						}
					}
						
					if($data['gq_mapname'] == "")
					{
						$data['gq_mapname'] = "- - -";
					}
					
					if($server['new'] == "1")
					{
						$data['gq_new'] = "&nbsp;<span class=\"server\" style=\"background:".$server['new_color'].";\">".$server['new_text']."</span>";
					}
			
				if($data['gq_type'] == 'arma2')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/arma2/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Arma 2';
				} 
				elseif($data['gq_type'] == 'arma3')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/arma3/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Arma 3';
				} 
				elseif($data['gq_type'] == 'bf3')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/bf3/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Battlefield 3';
				} 
				elseif($data['gq_type'] == 'bf4')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/bf4/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Battlefield 4';
				} 
				elseif($data['gq_type'] == 'cod4')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="xfire:join?game=cod4mp&server='.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
						$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/cod4/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
						$data['gq_desc'] = 'Call of Duty 4';
				} 
				elseif($data['gq_type'] == 'cs16')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
						$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/cs/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
						$data['gq_desc'] = 'Counter-Strike 1.6';
				} 
				elseif($data['gq_type'] == 'cscz')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/czero/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Counter-Strike Condition Zero';
				} 
				elseif($data['gq_type'] == 'css')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/css/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Counter-Strike Source';
				} 
				elseif($data['gq_type'] == 'csgo')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/csgo/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Counter-Strike Global Offensive';
				} 
				elseif($data['gq_type'] == 'dayz')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/dayz/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'DayZ';
				} 
				elseif($data['gq_type'] == 'dayzmod')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/dayzmod/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'DayZ Mod';
				} 
				elseif($data['gq_type'] == 'gmod')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/garrysmod/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = "Garry's Mod";
				} 
				elseif($data['gq_type'] == 'l4d')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/l4d/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Left 4 Dead';
				} 
				elseif($data['gq_type'] == 'l4d2')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/left4dead2/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Left 4 Dead 2';
				} 
				elseif($data['gq_type'] == 'minecraft')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/minecraft/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'MineCraft';
				} 
				elseif($data['gq_type'] == 'minequery')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/minecraft/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'MineCraft';
				} 
				elseif($data['gq_type'] == 'mta')
				{
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="mtasa://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'mta.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Multi Theft Auto';
				} 
				elseif($data['gq_type'] == 'mumble')
				{	
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="mumble://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'mumble.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Mumble';
				} 
				elseif($data['gq_type'] == 'samp')
				{
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="samp://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'samp.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'San Andreas Multi Player';
				} 
				elseif($data['gq_type'] == 'rust')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/rust/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					$data['gq_desc'] = 'Rust';
				} 
				elseif($data['gq_type'] == 'terraria')
				{
					if($data['gq_online'] == '1')
					{
						$ip= explode(":", $server['ip']);
						$data['gq_join'] = '<a href="steam://rungameid/105600// -j '.$ip[0].' -p '.$ip[1].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Terraria';
				} 
				elseif($data['gq_type'] == 'tf2')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/tf2/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Team Fortress 2';
				} 
				elseif($data['gq_type'] == 'tfc')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/tfc/".$data['gq_mapname'].".jpg' border='0' alt='".$data['gq_mapname']."'>";
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Team Fortress Classic';
				} 
				elseif($data['gq_type'] == 'teamspeak3')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="ts3server://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'teamspeak3.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'TeamSpeak 3';
				} 
				elseif($data['gq_type'] == 'ventrilo')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
						$data['gq_join'] = '<a href="ventrilo://'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'ventrilo.png" style="vertical-align: middle;"/></a>';
					}
					$data['gq_desc'] = 'Ventrilo';
				} 
				elseif($data['gq_type'] == 'wet')
				{
					$data['gq_gt'] = '<a href="http://www.gametracker.com/server_info/'.$data['gq_ip'].'/" target="_blank"><img src="'.SERVERSBOARD_IMAGES_PATH.'gt.png" style="vertical-align: middle;"/></a>';
					if($data['gq_online'] == '1')
					{
					//	$data['gq_join'] = '<a href="steam://connect/'.$data['gq_ip'].'"/><img src="'.SERVERSBOARD_IMAGES_PATH.'steam.png" style="vertical-align: middle;"/></a>';
					}
						$data['gq_mapscreen'] = "<img src='http://image.www.gametracker.com/images/maps/160x120/et/".strtolower($data['gq_mapname']).".jpg' border='0' alt='".$data['gq_mapname']."'>";
						$data['gq_desc'] = 'Wolfenstein Enemy Territory';
				} 
	
					if($server['field_link'] != '' && $server['field_icon'] != '' && $server['field'] != '0')
					{
						$data['gq_field'] = '<a href="'.$server['field_link'].'" target="_blank"><img src="'.$server['field_icon'].'" style="vertical-align: middle;"/></a>';
					}
	
					if($data['gq_mapname'] != '- - -')
					{
						if($data['gq_mapscreen'] && $data['gq_online'] == '1')
						{
							$data['gq_mapscreen'] = $data['gq_mapscreen'];
							eval("\$map = \"".$templates->get("serversboard_more_map")."\";");
						}
					}
				
					if($data['gq_gt'] || $data['gq_join'] || $data['gq_field'])
					{
						eval("\$field = \"".$templates->get("serversboard_more_field")."\";");
					}
				
					if(!in_array($mybb->user['uid'], explode("," ,$server['buddylist'])) && $mybb->user['uid'] > "0")
					{
						eval("\$buddylist_button = \"".$templates->get("serversboard_more_buddylist_button_joinbuddylist")."\";");
					}
					elseif(in_array($mybb->user['uid'], explode("," ,$server['buddylist'])) && $mybb->user['uid'] > "0")
					{
						eval("\$buddylist_button = \"".$templates->get("serversboard_more_buddylist_button_lowerbuddylist")."\";");
					}
					
					if($server['owner'] != "")
					{
						if(!is_array($groupscache))
						{
							$groupscache = $cache->read("usergroups");
						}
					
						$ownerbyname = get_user_by_username(trim($server['owner']));
						$owner = get_user($ownerbyname['uid']);
						$owneravatar = format_avatar(htmlspecialchars_uni($owner['avatar']), $owner['avatardimensions']);
						$ownergroup['title'] = $groupscache[$owner['usergroup']]['title'];

						$owner['username'] = format_name($owner['username'], $owner['usergroup'], $owner['displaygroup']);
						$owner['profilelink'] = build_profile_link($owner['username'], $owner['uid']);
						
						eval("\$owner = \"".$templates->get("serversboard_more_owner")."\";");
					}
				
					if($server['buddylist'] != '')
					{
						$query = $db->simple_select('users','uid,username,avatar,usergroup,displaygroup','uid IN('.$server['buddylist'].')');
						while($buddy = $db->fetch_array($query))
						{
							if($buddy['avatar'] == "") $buddy['avatar'] = "images/default_avatar.png";
							$buddy['avatar'] = '<a href="member.php?action=profile&uid=' . $buddy['uid'] . '"><img src="' . $buddy['avatar'] . '" alt="' . $buddy['username'] . '" title="' . $buddy['username'] . '" style="width: 40px;height:40px; border: 2px solid #a1a1a1;" /></a>';
							$buddy['username'] = format_name($buddy['username'], $buddy['usergroup'], $buddy['displaygroup']);
							$buddy['profilelink'] = build_profile_link($buddy['username'], $buddy['uid']);
							$buddy['profilelink'] = $buddy['avatar'];
							eval("\$buddylist_row .= \"" . $templates->get("serversboard_more_buddylist_row") . "\";");
						}
					}
					else
					{
						eval("\$buddylist_row .= \"" . $templates->get("serversboard_more_buddylist_row_empty") . "\";");
					}
					
					eval("\$buddylist = \"".$templates->get("serversboard_more_buddylist")."\";");
				}
			}
		}
		
		add_breadcrumb($lang->serversboard, "serversboard.php"); 
		add_breadcrumb($data['gq_hostname'], "serversboard.php?action=more_information&sid=".$server['sid']);
		eval("\$general = \"".$templates->get("serversboard_more_general")."\";");
		eval("\$page = \"".$templates->get("serversboard_more")."\";");
		output_page($page);
		exit;
	}
	elseif($mybb->input['action'] == "joinbuddylist")
	{
		$query = $db->simple_select("serversboard", "*", "sid='".$mybb->get_input('sid', 1)."' AND visible=1");
		if(!$db->num_rows($query))
		{
			error($lang->no_that_server);
		}

		$server = $db->fetch_array($query);
		$buddylist = explode(",",	$server['buddylist']);
		array_push($buddylist, $mybb->user['uid']);
		$buddylist = implode(",", $buddylist);
		$buddylist = trim($buddylist, ",");
		$update_query = array(
			"buddylist"    => $db->escape_string(''.$buddylist.''),
		);
		$db->update_query("serversboard", $update_query, "sid='".$mybb->input['sid']."'");	
		redirect("serversboard.php?action=more_information&sid=".$mybb->input['sid'], $lang->joinbuddylist_success);
	}
	elseif($mybb->input['action'] == "lowerbuddylist")
	{
		$query = $db->simple_select("serversboard", "*", "sid='".$mybb->get_input('sid', 1)."' AND visible=1");
		if(!$db->num_rows($query))
		{
			error($lang->no_that_server);
		}

		$server = $db->fetch_array($query);
		$buddylist = explode(",",	$server['buddylist']);
		$key = array_search($mybb->user['uid'], $buddylist);
		unset($buddylist[$key]);
		$buddylist = implode(",", $buddylist);
		$buddylist = trim($buddylist, ",");
		$update_query = array(
			"buddylist"    => $db->escape_string(''.$buddylist.''),
		);
		$db->update_query("serversboard", $update_query, "sid='".$mybb->input['sid']."'");	
		redirect("serversboard.php?action=more_information&sid=".$mybb->input['sid'], $lang->lowerbuddylist_success);
	}
}

function serversboard_fetch_wol_activity_end($user_activity)
{
    global $db, $mybb, $parameters, $filename, $user, $location, $lang;
	$lang->load("serversboard");
	
    if(strpos($user_activity['location'], "serversboard.php"))
    {
        if(is_numeric($parameters['sid']))
        {
			$user_activity['activity'] = $lang->serversboard_online_location;
            $user_activity['sid'] = $parameters['sid'];
        }
		else
		{
			$user_activity['activity'] = $lang->serversboard_online_location;
		}
    }
    return $user_activity;
}

function serversboard_build_friendly_wol_location_end(&$plugin_array)
{
    global $db, $mybb, $user_activity, $parameters, $gid_list, $location_name, $conid_list, $lang, $data;
	$lang->load("serversboard");
	
	require_once SERVERSBOARD_CORE_PATH."Autoloader.php";

    if($plugin_array['user_activity']['activity'] == $lang->serversboard_online_location)
    {
        if($plugin_array['user_activity']['sid'])
        {
			$sid = $plugin_array['user_activity']['sid'];
			$query = $db->simple_select("serversboard", "*", "sid='$sid'");
			while($server = $db->fetch_array($query)) 
			{
			//	$results = unserialize($server['cache']);
				$results = unserialize(base64_decode($server['cache']));
				
				foreach((array)$results as $data)
				{
				
					if($data['gq_hostname'] == "") 
					{
						$data['gq_hostname'] = $server['offlinehostname'];
						if($mybb->settings['serversboard_remove_host_onoff'] == '1')
						{
							$data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);
						}
						if($server['cuthostname'] != "" &&  $server['cuthostname'] > 1)
						{
							$data['gq_hostname'] = (my_strlen($data['gq_hostname']) > $server['cuthostname']) ? my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "..." : $data['gq_hostname'];
						}
					}
					elseif($data['gq_hostname'] != "") 
					{
							$data['gq_hostname'] = $data['gq_hostname'];
							if($mybb->settings['serversboard_remove_host_onoff'] == '1')
							{
								$data['gq_hostname'] = str_replace(explode(",",$mybb->settings['serversboard_remove_host']), "", $data['gq_hostname']);
							}
							if($server['cuthostname'] != "" &&  $server['cuthostname'] > 1)
							{
								$data['gq_hostname'] = (my_strlen($data['gq_hostname']) > $server['cuthostname']) ? my_substr($data['gq_hostname'], 0, $server['cuthostname']) . "..." : $data['gq_hostname'];
							}
					}
				}
			}
			$plugin_array['location_name'] = $lang->serversboard_online_location_viewing_serversboard_more." <a href=\"serversboard.php?action=more_information&sid=" . $plugin_array['user_activity']['sid'] . "\">".$data['gq_hostname']."</a>";
        }
        else
        {
            $plugin_array['location_name'] = $lang->serversboard_online_location_viewing_serversboard." <a href=\"serversboard.php\">".$lang->serversboard_online_location_serversboard."</a>";
        }
    }
    return $plugin_array;
}

function serversboard_global_start()
{
	global $mybb, $templatelist;
	
	if($mybb->settings['serversboard_onoff'] != "1")
	{
		return;
	}

	if(in_array(THIS_SCRIPT, explode("," ,"index.php")))
	{
		if(isset($templatelist))
		{
			$templatelist .= ", ";
		}
		
		$templatelist .= "serversboard_row, serversboard_summation, serversboard,serversboard_index_row_empty";
	}
	
	if(in_array(THIS_SCRIPT, explode("," ,"serversboard.php")))
	{
		if(isset($templatelist))
		{
			$templatelist .= ", ";
		}
		
		$templatelist .= "serversboard_row, serversboard_summation, serversboard, serversboard_subpage,";
		$templatelist .= "multipage_page_current, multipage_page, multipage_nextpage, multipage_jump_page, multipage, serversboard_more_players_multipage, serversboard_more_players_row, serversboard_more_players, serversboard_more_map, serversboard_more_field, serversboard_more_buddylist_button_lowerbuddylist, serversboard_more_owner, serversboard_more_buddylist_row, serversboard_more_buddylist, serversboard_more_general, serversboard_more, serversboard_more_buddylist_button_joinbuddylist, serversboard_more_buddylist_row_empty";
	}
}

/********************************************************************************************************************************
*
* Say thanks to plugin author - paste link to author website.
* Please don't remove this code if you didn't make donate.
* It's the only way to say thanks without donate.
*
********************************************************************************************************************************/
function serversboard_thanks(&$content)
{
    global $session, $thanksSupryk, $lang;
	$lang->load("serversboard");
        
    if(!isset($thanksSupryk) && $session->is_spider)
    {
        $thx = '<div style="margin:auto; text-align:center;">'.$lang->serversboard_thanks.'</div></body>';
        $content = str_replace('</body>', $thx, $content);
        $thanksSupryk = true;
    }
}
