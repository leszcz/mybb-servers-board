<?php

/********************************************************************************************************************************
*
*  Servers board (/serversboard.php)
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
 
define('IN_MYBB', '1'); 
define('THIS_SCRIPT', 'serversboard.php');
require "./global.php"; 

if(!$db->table_exists("serversboard") && $mybb->settings['serversboard_onoff'] != '1')
{
	error_no_permission();
}

$plugins->run_hooks("serversboard_start");

$plugins->run_hooks("serversboard_banners");