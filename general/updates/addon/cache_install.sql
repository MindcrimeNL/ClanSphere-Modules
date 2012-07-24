CREATE TABLE {pre}_cache (
  cache_id {serial},
  cache_key varchar(255) NOT NULL default '',
  cache_md5 varchar(32) NOT NULL default '',
  cache_time int(14) NOT NULL default 0,
  cache_timeout int(14) NOT NULL default 0,
  cache_content longblob,
  PRIMARY KEY  (`cache_id`)
){engine};

CREATE INDEX {pre}_cache_md5_index ON {pre}_cache (cache_md5);

