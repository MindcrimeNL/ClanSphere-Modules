-- plugins in replays, view/download access control
ALTER TABLE {pre}_replays ADD replays_plugins varchar(80) NOT NULL default '';
ALTER TABLE {pre}_replays ADD replays_access int(8) NOT NULL default '1';
ALTER TABLE {pre}_replays ADD replays_count_downloads int(8) NOT NULL default '0';

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('replays', 'plugins', 'wc3,dota,sc2');

