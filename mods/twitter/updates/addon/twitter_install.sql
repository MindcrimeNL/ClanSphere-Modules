ALTER TABLE {pre}_access ADD access_twitter int(2) NOT NULL default '0';

CREATE TABLE {pre}_twitter (
  twitter_id {serial},
  users_id int(8) NOT NULL default '0',
  twitter_access_token varchar(80) NOT NULL default '',
  twitter_access_secret varchar(80) NOT NULL default '',
  PRIMARY KEY  (`twitter_id`)
){engine};

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'website_consumer_key', '');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'website_consumer_secret', '');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'website_access_token', '');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'website_access_secret', '');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'website_enable', '1');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'users_enable', '1');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'timeout', '3');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'max_navlist', '4');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('twitter', 'max_headline', '0');

