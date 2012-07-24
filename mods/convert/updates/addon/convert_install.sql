ALTER TABLE {pre}_access ADD access_convert int(2) NOT NULL default '0';

UPDATE {pre}_access SET access_convert = '5' WHERE access_id = '5' LIMIT 1 ;