<?php

/********************************************************************************************************************************
*
*  Servers board (/inc/languages/polish/admin/config_serversboard.lang.php)
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

$l['serversboard'] = "Tabela serwerów";
$l['serversboard_desc'] = "Pokazuje informacje o serwerach gier sieciowych na stronie glownej w tabeli oraz szczegóły na podstrnie.";

$l['serversboard_upload_all_files'] = "Prosze wrzucić wszystkie pliki na serwer.";

$l['setting_group_serversboard'] = "Tabela serwerów";
$l['setting_group_serversboard_desc'] = "Ustawienia pluginu: Tabela serwerów.";

$l['setting_serversboard_onoff'] = "Włącz lub wyłacz liste serwerow";
$l['setting_serversboard_onoff_desc'] = "Włącz lub wyłącz tabelkę z serwerami ze strony głównej.";

$l['setting_serversboard_index_onoff'] = "Włącz lub wyłacz liste serwerowna stronie glownej";
$l['setting_serversboard_index_onoff_desc'] = "Włącz lub wyłącz tabelkę z serwerami ze strony głównej.";

$l['setting_serversboard_portal_onoff'] = "Włącz lub wyłacz liste serwerowna stronie portalu";
$l['setting_serversboard_portal_onoff_desc'] = "Włącz lub wyłącz tabelkę z serwerami ze strony portalu.";

$l['setting_serversboard_show_barsplayersnum_onoff'] = "Wykres aktualnej liczby graczy";
$l['setting_serversboard_show_barsplayersnum_onoff_desc'] = "Jeśli zaznaczysz opcje na \"Tak\" w tle kolumny \"Gracze/Sloty\" bedzie pokazany pasek którego kolor i szerokość będzie zależna od ilości graczy na serwerze.";

$l['setting_serversboard_remove_host'] = "Wpisz dopiski hostingoe, które mają być usuwane";
$l['setting_serversboard_remove_host_desc'] = "Wpisz dopiski hostingowe,  które mają być usuwane np: @Pukawka.pl pamietaj jeśli wiecej,  niż jedna odziel przecinkiem.";

$l['setting_serversboard_summation_onoff'] = "Pokazać podsumowanie ?";
$l['setting_serversboard_summation_onoff_desc'] = "Włącz lub wyłącz podsumowanie serwerów, graczy slotów pod listą serwerów.";

$l['setting_serversboard_cache_time'] = "Czas przechowywania informacji w pamieci podręcznej - Cache";
$l['setting_serversboard_cache_time_desc'] = "Czas przechowywania informacji o serwerach w pamięci podręcznej, co ten czas serwery będą odpytywane i tabela będzie aktualizowane o nowe informacje.<br />Podany w minutach.";

$l['serversboard_templates'] = "Tabela serwerów -";

$l['serversboard_uninstall'] = "Odinstalowywanie pluginu tabela serwerów";
$l['serversboard_uninstall_message'] = "Czy chcesz usunąć wszystkie dane z bazy danych?";

$l['servers_list'] = "Tabela serwerów";
$l['servers_list_desc'] = "Zarządzaj tabelą serwerów.";
$l['server_add'] = "Dodaj serwer";
$l['server_add_desc'] = "Dodaj nowy serwer do tabeli serwerów.";
$l['server_add_success'] = "Serwer dodany poprawnie.";
$l['server_edit'] = "Edytuj serwer";
$l['server_edit_desc'] = "Edytuj serwer który znajduje się w tabeli serwerów.";
$l['server_edit_error'] = "Wystąpil bląd podczas edycji serwera.";
$l['server_edit_success'] = "Serwer zedytowany poprawnie zedytowany.";
$l['server_delete_error'] = "Serwer nie zostal usunięty.";
$l['server_delete_success'] = "Serwer zostal usunięty.";
$l['server_confirm_deletion'] = "Czy napewo chcesz usunąć serwer?";
$l['server_popup_confirm_deletion'] = "Czy napewo chcesz usunąć serwer?";
$l['servers_orders_updated_success'] = "Kolejność zostala zmieniona.";

$l['server_ip'] = "IP";
$l['server_ip_desc'] = "Podaj IP serwera (np.: 178.217.190.1:6350 ).";
$l['server_type'] = "Typ";
$l['server_type_desc'] = "Wybierz typ serwera.";
$l['server_type_select'] = "Wybierz typ serwera";
$l['server_arma2qport'] = "Query port dla Arma 2";
$l['server_arma2qport_desc'] = "Query port dla Arma 2, domyślnie wystarczy dodać 1 do portu serwer, np.: jeśli port serwera to 2302 to qury port będzie wyglądal tak 2303";
$l['server_arma3qport'] = "Query port dla Arma 3";
$l['server_arma3qport_desc'] = "Query port dla Arma 3, domyślnie wystarczy dodać 1 do portu serwer, np.: jeśli port serwera to 2302 to qury port będzie wyglądal tak 2303";
$l['server_bf3qport'] = "Query port dla battlefield 3";
$l['server_bf3qport_desc'] = "Query port dla battlefield 3, domyślnie wystarczy dodać 22000 do portu serwer, np.: jeśli port serwera to 25200 to qury port będzie wyglądal tak 47200";
$l['server_bf4qport'] = "Query port dla battlefield 4";
$l['server_bf4qport_desc'] = "Query port dla battlefield 4, domyślnie wystarczy dodać 22000 do portu serwer, np.: jeśli port serwera to 25200 to qury port będzie wyglądal tak 47200";
$l['server_dayzqport'] = "Query port dla dayz";
$l['server_dayzqport_desc'] = "Query port dla multi theftauto, domyślnie 27016, opcja wymagana dla dayz";
$l['server_dayzmodqport'] = "Query port dla dayz mod";
$l['server_dayzmodqport_desc'] = "Query port dla multi theftauto, domyślnie 27017/2301 lub +1 do portu serwera, jesli port serwera to 2302 to query port bedzie wyglądal tak 2303 opcja wymagana dla dayz";
$l['server_minecraftqport'] = "Query port/TCP dla minecraft";
$l['server_minecraftqport_desc'] = "Query port/TCP dla team speak 3, domyślnie 25565.";
$l['server_mtaqport'] = "Query port dla multi theftauto";
$l['server_mtaqport_desc'] = "Query port dla multi theftauto, domyślnie wystarczy dodać 123 do portu serwer, np.: jeśli port serwera to 22003 to qury port będzie wyglądal tak 22126";
$l['server_mumbleqport'] = "Query port dla mumble";
$l['server_mumbleqport_desc'] = "Query port dla mumble, wymagana wtyczka gametrackera na serwerze http://www.gametracker.com/downloads/gtmurmurplugin.php";
$l['server_rustqport'] = "Query port dla rust";
$l['server_rustqport_desc'] = "Query port dla rust, domyślnie wystarczy dodać 1 do portu serwer, np.: jeśli port serwera to 28016 to qury port będzie wyglądal tak 28017";
$l['server_terrariaqport'] = "Query port dla terraria";
$l['server_terrariaqport_desc'] = "Query port dla terraria, domyślnie wystarczy dodać 101 do portu serwer, np.: jeśli port serwera to 7777 to qury port będzie wyglądal tak 7878";
$l['server_ts3qport'] = "Query port/TCP dla team speak 3";
$l['server_ts3qport_desc'] = "Query port/TCP dla team speak 3, domyślnie 10011.";
$l['server_offlinehostname'] = "Nazwa serwera, gdy jest OFFLINE";
$l['server_offlinehostname_desc'] = "Wpisz nazwe serwera która bedzie wyświetlana gdy serwer bedzie OFFline np.; \"Polish-Zone.pl [TS3]\".";
$l['server_cuthostname'] = "Skróć nazwe serwera po (wpisz ilość) znakach.";
$l['server_cuthostname_desc'] = "Ile znaków ma zostać w wyswietlanej nazwie serwera";
$l['server_disporder'] = "Kolejność/Nr.";
$l['server_disporder_desc'] = "Wpisz numer serwera na liście.";
$l['server_field'] = "Dodatkowe pole";
$l['server_field_desc'] = "Czy pokazac dodatkowe pole - opcje okresla sie nizej.";
$l['server_field_link'] = "Link dodatkowego pola";
$l['server_field_link_desc'] = "Podaj link do dodatkowej opcji w polu więcej np.: link do hltv serwera CS16.";
$l['server_field_icon'] = "Ikona dodatkowego pola";
$l['server_field_icon_desc'] = "Podaj link do ikony dodatkowego pola.";
$l['server_forumid'] = "Wybierz dzial serwera";
$l['server_forumid_desc'] = "Wybierz dzial w którym wyjaśnia się sprawy serwera.";
$l['server_forumid_none'] = "Brak";
$l['server_owner'] = "Wpisz nazwe użytkownika właściciela serwera.";
$l['server_owner_desc'] = "Wpisz nazwe użytkownika właściciela serwera, informacje o nim zostaną wyświetlone na dodatkowej podstronie serwera.";
$l['server_visible'] = "Serwer widoczny w tabeli";
$l['server_visible_desc'] = "Jeśli zaznaczysz opcje na nie, serwer bedzie widoczny tylko w panelu administratora.";
$l['server_new'] = "\"Nowy\" Serwer jest nowy lub posiada nowe IP";
$l['server_new_desc'] = "Jeśli zaznaczysz opcje na tak i wypelnisz pole poniżej tekstem, przy serwerze pokaże się prefiks z dopisanym przez Ciebie tekstem.";
$l['server_new_color'] = "Kolor tla dla prefiksu/opcji \"Nowy\"";
$l['server_new_color_desc'] = "Wpisz kolor dla prefiksu/opcji \"Nowy\" np: #0f0f0f.";
$l['server_new_text'] = "Tekst dla opcji \"Nowy\"";
$l['server_new_text_desc'] = "Wpisz tekst który ma być pokazany kiedy opcja wyżej jest na \"Tak\".";
$l['server_buddylist'] = "Przyjaciele serwera";
$l['server_buddylist_desc'] = "Zarządzaj listą przyjaciół serwera. Wpisz uid\'y użytkowników oddzielone przecinkami. Nie zostawiaja przecinka na końcu.";
$l['server_error_missing_ip'] = "Uzupelnij wymagane pole IP. Nie wpisałeś IP lub podałeś złe.";
$l['server_error_missing_type'] = "Uzupelnij wymagane pole Typ.";
$l['server_error_missing_arma2qport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_arma3qport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_bf3qport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_bf4zqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_dayzqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_dayzmodqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_minecraftqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_mtaqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_mumbleqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_rustqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_terrariaqport'] = "Uzupelnij wymagane pole Query port.";
$l['server_error_missing_ts3qport'] = "Uzupelnij wymagane pole Query port/TCP.";
$l['server_error_missing_offlinehostname'] = "Uzupelnij wymagane pole Nazwa serwera, gdy jest OFFLINE.";
$l['server_error_missing_disporder'] = "Uzupelnij wymagane pole Kolejność. Nie wpisaleś numeru serwera lub podałeś złą wartość";
$l['server_add_save'] = "Dodaj serwer";
$l['server_edit_save'] = "Zapisz zmiany";
$l['servers_name'] = "Nazwa serwera";
$l['servers_status'] = "Status";
$l['servers_online'] = "Włączony";
$l['servers_offline'] = "Wyłączony";
$l['servers_ip'] = "IP serwera";
$l['servers_type'] = "Typ serwera";
$l['servers_order'] = "Kolejność/Nr.";
$l['servers_options'] = "Opcje";
$l['server_option_edit'] = "Edytuj";
$l['server_option_delete'] = "Usuń";
$l['no_servers'] = "Nie dodano jeszcze żadnego serwera.";
$l['save_servers_order'] = "Zapisz kolejność serwerów";

$l['serversboard_admin_permissions'] = "Może zarządzać tabelą serwerów?";
$l['admin_log_config_serversboard_edit'] = "Edytowano serwer w tabeli serwerów #{1} ({2})";
$l['admin_log_config_serversboard_add'] = "Dodano serwer do tabeli serwerów #{1} ({2})";
$l['admin_log_config_serversboard_delete'] = "Usunięto serwer z tabeli serwerów #{1} ({2})";
$l['admin_log_config_serversboard_update_order'] = "Zaktualizowano kolejność wyświetlania serwerów w tabeli serwerów";