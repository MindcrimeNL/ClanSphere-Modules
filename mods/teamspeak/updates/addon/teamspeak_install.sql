ALTER TABLE {pre}_access ADD access_teamspeak int(2) NOT NULL default '0';

CREATE TABLE {pre}_teamspeak (
  teamspeak_id {serial},
  teamspeak_version int(2) NOT NULL default '0',
  teamspeak_ip varchar(80) NOT NULL default '',
  teamspeak_udp varchar(5) NOT NULL default '',
  teamspeak_tcp varchar(5) NOT NULL default '',
  teamspeak_admin varchar(25) NOT NULL default '',
  teamspeak_adminpw varchar(80) NOT NULL default '',
  teamspeak_sadmin varchar(25) NOT NULL default '',
  teamspeak_sadminpw varchar(80) default NULL,
  teamspeak_active int(1) NOT NULL default '0',
  teamspeak_register int(2) NOT NULL default '0',
	teamspeak_access int(2) NOT NULL default 1,
  teamspeak_charset varchar(16) NOT NULL default 'ISO-8859-1',
  PRIMARY KEY  (`teamspeak_id`)
){engine};

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('teamspeak', 'channel_flags', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('teamspeak', 'player_flags', '0');

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('teamspeak', 'show_empty', '1');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('teamspeak', 'show_empty_navlist', '0');

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('teamspeak', 'timeout', '2');
