ALTER TABLE {pre}_access ADD access_bets int(2) NOT NULL default '0';

CREATE TABLE {pre}_bets (
  bets_id {serial},
  categories_id int(8) default '0',
  bets_status tinyint(1) default '0' COMMENT 'Status 0: Open, 1: Closed, 2: Finished',
  bets_title varchar(100) default NULL COMMENT 'Title of the bet.',
  bets_auto_title int(1) NOT NULL default '0',
  bets_starts_at int(11) default NULL COMMENT 'Date/Time the bet shows up.',
  bets_closed_at int(11) default NULL COMMENT 'Date/Time the bet ends.',
  bets_description text COMMENT 'Description for the bet to motivate users to place their bet.',
  bets_quote_type int(2) NOT NULL default '0',
  bets_enable_draw int(1) NOT NULL default '1',
  bets_com_close int(2) NOT NULL default '0',
  PRIMARY KEY  (`bets_id`)
){engine};

CREATE TABLE {pre}_bets_contestants (
  contestants_id {serial},
  bets_id int(8) NOT NULL default '0',
  clans_id int(8) default '0',
  bets_name varchar(100) NOT NULL default '',
  bets_quote float NOT NULL default '150',
  bets_draw int(1) NOT NULL default '0',
  bets_winner int(4) NOT NULL default '0',
  PRIMARY KEY  (`contestants_id`)
){engine};

CREATE TABLE {pre}_bets_users (
  bets_users_id {serial},
  bets_id int(9) NOT NULL default '0',
  contestants_id int(8) NOT NULL default '0',
  users_id int(8) NOT NULL default '0',
  bets_amount float NOT NULL default '0',
  bets_pay_amount float NOT NULL default '0',
  bets_pay_time int(11) NOT NULL default '0',
  bets_users_time int(11) NOT NULL default '0',
  PRIMARY KEY  (`bets_users_id`)
){engine};

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'pointsname', 'Coins');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'auto_title', '1');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'auto_title_separator', 'vs.');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'max_navlist', '4');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'max_navlist_title', '25');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'remove_quote', '10');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'min_quote', '1.2');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'max_quote', '100');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'win_quote', '0.00');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'quote_type', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'base_fee', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'coins_receive', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'coins_min_length', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('bets', 'date_format', 'Y.m.d @H:i');

UPDATE {pre}_access SET access_bets = '0' WHERE access_id = '1' LIMIT 1 ;
UPDATE {pre}_access SET access_bets = '2' WHERE access_id = '2' LIMIT 1 ;
UPDATE {pre}_access SET access_bets = '3' WHERE access_id = '3' LIMIT 1 ;
UPDATE {pre}_access SET access_bets = '4' WHERE access_id = '4' LIMIT 1 ;
UPDATE {pre}_access SET access_bets = '5' WHERE access_id = '5' LIMIT 1 ;
