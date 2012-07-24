CREATE TABLE {pre}_replays_example (
  replays_example_id {serial},
  replays_id int(8) NOT NULL default '0',
  replay_example_other varchar(25) NOT NULL default '',
  PRIMARY KEY (replays_example_id)
){engine};

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('replays_example', 'games_ids', '');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('replays_example', 'option1string', 'a text option');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('replays_example', 'option2int', '10');
