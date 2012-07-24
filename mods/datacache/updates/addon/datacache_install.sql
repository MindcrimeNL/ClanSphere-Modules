ALTER TABLE {pre}_access ADD access_datacache int(2) NOT NULL default '0';

CREATE TABLE {pre}_datacache (
  datacache_id {serial},
  datacache_mod varchar(80) NOT NULL default '',
  datacache_action varchar(80) NOT NULL default '',
  datacache_key varchar(80) NOT NULL default '',
  datacache_time int(14) NOT NULL default 0,
  datacache_timeout int(14) NOT NULL default 900,
  datacache_data longblob,
  PRIMARY KEY  (`datacache_id`)
){engine};

CREATE INDEX {pre}_datacache_speedup_index ON {pre}_datacache (datacache_mod, datacache_action, datacache_key);
CREATE INDEX {pre}_datacache_mod_index ON {pre}_datacache (datacache_mod);

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('datacache', 'timeout', '900');


