-- addon: coins for comments

ALTER TABLE {pre}_coins ADD coins_comments_received float NOT NULL default '0' AFTER coins_bets_used;
ALTER TABLE {pre}_coins ADD coins_comments_used float NOT NULL default '0' AFTER coins_comments_received;

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('comments', 'coins_receive', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('comments', 'coins_min_length', '0');

UPDATE {pre}_options SET options_value = CONCAT(options_value,',comments') WHERE options_mod = 'coins' AND options_name = 'coin_mods';
