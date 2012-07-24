-- addon: coins for board

ALTER TABLE {pre}_coins ADD coins_board_received float NOT NULL default '0' AFTER coins_bets_used;
ALTER TABLE {pre}_coins ADD coins_board_used float NOT NULL default '0' AFTER coins_board_received;

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('board', 'coins_receive', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('board', 'coins_receive_thread', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('board', 'coins_checkbox_thread', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('board', 'coins_min_length', '0');

UPDATE {pre}_options SET options_value = CONCAT(options_value,',board') WHERE options_mod = 'coins' AND options_name = 'coin_mods';
