<?php
// bx clanportal 0.3.0.0
// $Id: view.php 825 2009-04-23 19:38:38Z nenTi $

//Manage
$cs_lang['mod']	= 'Wettb&uuml;ro';
$cs_lang['mod_text']	= 'Wettb&uuml;ro';
$cs_lang['mod_name']	= 'Wettb&uuml;ro';
$cs_lang['new_bet']	= 'Neue Wette hinzuf&uuml;gen';

$cs_lang['select_status']	 = 'Anzeigestatus ausw&auml;hlen';
$cs_lang['open_bets']	 = 'Offene Wetten';
$cs_lang['wait_bets'] = 'Geschlossene Wetten';
$cs_lang['closed_bets']	 = 'Beendete Wetten';

$cs_lang['title']	= 'Title';
$cs_lang['active']	= 'Aktiv';

//Options 
$cs_lang['pointsname']	= 'W&auml;hrungsbezeichnung';
$cs_lang['default_quote_type']	= 'Standart Quote System';
$cs_lang['auto_title']	= 'Automatisch einen Titel erstellen';
$cs_lang['auto_title_default']	= '"Automatisch Titel erstellen" normalerweise an';
$cs_lang['auto_title_separator']	= 'Text zwischen den Teams';
$cs_lang['max_navlist']	 = 'Datens&auml;tze in der Navlist';
$cs_lang['max_navlist_title'] = 'Maximale Titel L&auml;nge in der Navlist';
$cs_lang['body_bets']	 = 'Verwaltung der Einstellungen im Modul.';
$cs_lang['opt_bets']	 = 'Optionen';
$cs_lang['remove_quote']	 = 'Geb&uuml;hr beim L&ouml;schen';
$cs_lang['max_quote'] = 'Maximale Quote "Teil der Totalsumme"';
$cs_lang['min_quote'] = 'Minimale Quote "Teil der Totalsumme"';
$cs_lang['super_quote'] = 'Super Quote ( manipuliert ALLE Quoten +/- )';
$cs_lang['base_fee'] = 'Grundgeb&uuml;hr';
$cs_lang['date_format'] = 'Datum format';

$cs_lang['quote_type_0'] = 'Teil der Gesamtsumme';
$cs_lang['quote_type_1'] = 'Prozentsatz';
$cs_lang['quote_type_2'] = 'Festgelegt';
$cs_lang['quote_type_explain'] = '"Teil der Gesamtsumme": Gewinner bekommen ein Teil der Gesamtsumme an Wetten.<br />"Prozentsatz": F&uuml;r Quote X, der Gewinner bekommt (1 + SQ + X/100) coins f&uuml;r jeden coin.<br />"Festgelegt": F&uuml;r Quote X, der Gewinner bekommt X + SQ coins f&uuml;r jeden coin.<br />SQ = Super Quote.';

//Create
$cs_lang['body_create']	 = 'Neue Wette hier erstellen.';
$cs_lang['error_create'] = 'Es ist ein Fehler beim erstellen der Wette aufgetreten:';

$cs_lang['start_date'] = 'Startdatum';
$cs_lang['end_date'] = 'Enddatum (Eventstart)';
$cs_lang['quote_type']	= 'Quoten System';
$cs_lang['contestant'] = 'Kandidaten';
$cs_lang['description'] = 'Beschreibung';
$cs_lang['or'] = 'oder';
$cs_lang['bets_quote'] = 'Quote';
$cs_lang['min_contestants'] = 'Mindestens 2 Kandidaten festlegen:';
$cs_lang['add_contestant'] = 'Hinzuf&uuml;gen';
$cs_lang['remove_contestant'] = 'Entfernen';
$cs_lang['quote_manual'] = 'Quote An/Aus';
$cs_lang['accept_draw'] = 'Unentschieden als Tippoption anbieten';

$cs_lang['no_category']   = '- Kein Kategorie eingegeben';
$cs_lang['no_contestant'] = '- Nicht gen&uuml;gend Kandidaten eingetragen!';
$cs_lang['no_closed_at'] = '- Enddatum schon &uuml;berschritten!';
$cs_lang['no_title'] = '- Kein Titel eingegeben!';

$cs_lang['more'] = 'mehr...';
$cs_lang['com_close'] = 'Kommentare sperren';

//Remove
$cs_lang['really'] = 'Soll die \'%s\' wirklich gel&ouml;scht werden?';
$cs_lang['del_true'] = 'Wette gel&ouml;scht';
$cs_lang['del_false'] = 'L&ouml;schen abgebrochen';
$cs_lang['no_selection'] = 'Keine Wette ausgew&auml;hlt';
$cs_lang['invalid_rollback_option'] = 'L&ouml;sch Option nicht anwendbar!';
$cs_lang['rollback_option'] = 'L&ouml;sch Option';
$cs_lang['rollback_0'] = 'Einsatz zur&uuml;ck erstatten an Benutzer &amp; Gewinne zur&uuml;ckfordern von Benutzern';
$cs_lang['rollback_1'] = 'Einsatz zur&uuml;ck erstatten an Benutzer &amp; m&ouml;gliche Gewinne bleiben erhalten';
$cs_lang['rollback_2'] = 'Einfach loesschen';

// List.php
$cs_lang['overview']= '&Uuml;bersicht';
$cs_lang['no_cat_text']= 'Keine Infos hinterlegt.';
$cs_lang['start']= 'Startet am';
$cs_lang['ende']= 'Endet am';
$cs_lang['stat']= 'Status';
$cs_lang['bets']= 'Wetten';
$cs_lang['open'] = 'Offen';
$cs_lang['go_open'] = '&Uuml;bersicht Offene Wetten';
$cs_lang['closed'] = 'Beendet';
$cs_lang['on_calc'] = 'Geschlossen';
$cs_lang['ready'] = 'Ausgewertet';
$cs_lang['participants'] = 'Teilnehmer';

// edit.php
$cs_lang['head_edit']	= 'Bearbeiten';
$cs_lang['body_edit'] =  'Bitte alle Felder mit * ausf&uuml;llen.';
$cs_lang['edit_done'] =  '&Auml;nderungen erfolgreich &uuml;bernommen.';
$cs_lang['edit'] =  'Bearbeiten';

// Result.php
$cs_lang['head_result']	= 'Ergebniss eintragen';
$cs_lang['body_result']	= 'Bitte das Ergebniss ausw&auml;hlen.';
$cs_lang['winner']	= 'Gewinner';
$cs_lang['draw']	= 'Unentschieden';
$cs_lang['result_booked'] = 'Ergebniss erfolgreich eingetragen';

//View.php
$cs_lang['details'] = 'Details';
$cs_lang['body_details'] = 'Detailinfos zu einer Wette.';
$cs_lang['bet'] = 'Wette';
$cs_lang['credits'] = 'Guthaben';
$cs_lang['place_bet'] = 'Tipp abgeben';
$cs_lang['bidding'] = 'Gebote';
$cs_lang['result'] = 'Ergebnis';
$cs_lang['wins'] = 'gewinnt';
$cs_lang['bet_amount'] = 'Wetteinsatz';
$cs_lang['date'] = 'Datum'; 
$cs_lang['team'] = 'Team / Gegner';
$cs_lang['earned'] = 'Empfangen';
$cs_lang['win_quote'] = 'Super Quote';

//Place_bet.php
$cs_lang['place_failed'] = 'Wetteinsatz fehlgeschlagen:';
$cs_lang['placed_bet'] = 'Wetteinsatz erfolgreich abgegeben!';
$cs_lang['not_enough_points'] = ' - Nicht gen&uuml;gend %s';
$cs_lang['already_bet'] = 'Sie haben bereits einen Einsatz gesetzt.';
$cs_lang['no_user'] = ' - Sie sind nicht eingelogt.';
$cs_lang['no_desc'] = 'Wette jetzt!';
$cs_lang['no_contestant'] = ' - Kein Kandidat ausgew&auml;hlt';
$cs_lang['no_bets'] = 'Noch keine Wetteins&auml;tze gesetzt.';
$cs_lang['no_amount'] = 'Du kannst keine leere Wette abgeben.';
$cs_lang['no_delete'] = 'Kann die Wette nicht l&ouml;sschen!';
$cs_lang['remove_placed_bet'] = 'Wette zur&uuml;ckziehen';
$cs_lang['remove_placed_done'] = 'Wetteinsatz erfolgreich zur&uuml;ckgezogen.';
// toplist.php
$cs_lang['toplist'] = 'Bestenliste';
?>
