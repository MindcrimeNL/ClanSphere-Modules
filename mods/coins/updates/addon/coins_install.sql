ALTER TABLE {pre}_access ADD access_coins int(2) NOT NULL default '0';

CREATE TABLE {pre}_coins (
  coins_id {serial},
  users_id int(8) NOT NULL default '0',
  coins_total float NOT NULL default '0',
  coins_bets_received float NOT NULL default '0',
  coins_bets_used float NOT NULL default '0',
  PRIMARY KEY  (`coins_id`),
	UNIQUE (users_id)
){engine};

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('coins', 'startcoins', '50');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('coins', 'coin_mods', 'bets');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('coins', 'coin_decimals', '2');

UPDATE {pre}_access SET access_coins = '0' WHERE access_id = '1' LIMIT 1 ;
UPDATE {pre}_access SET access_coins = '2' WHERE access_id = '2' LIMIT 1 ;
UPDATE {pre}_access SET access_coins = '3' WHERE access_id = '3' LIMIT 1 ;
UPDATE {pre}_access SET access_coins = '4' WHERE access_id = '4' LIMIT 1 ;
UPDATE {pre}_access SET access_coins = '5' WHERE access_id = '5' LIMIT 1 ;
