ALTER TABLE {pre}_access ADD `access_ticker` int(2) NOT NULL default '0' ;
UPDATE {pre}_access SET access_ticker = '1' WHERE access_id = 1 LIMIT 1 ;
UPDATE {pre}_access SET access_ticker = '2' WHERE access_id = 2 LIMIT 1 ;
UPDATE {pre}_access SET access_ticker = '3' WHERE access_id = 3 LIMIT 1 ;
UPDATE {pre}_access SET access_ticker = '4' WHERE access_id = 4 LIMIT 1 ;
UPDATE {pre}_access SET access_ticker = '5' WHERE access_id = 5 LIMIT 1 ;

CREATE TABLE {pre}_ticker (
ticker_id int(8) unsigned NOT NULL auto_increment,
ticker_direction varchar(5) NOT NULL default '',
ticker_amount int(2) NOT NULL default '0',
ticker_delay int(2) NOT NULL default '0',
ticker_content text NOT NULL,
PRIMARY KEY  (ticker_id)
) {engine};

INSERT INTO `{pre}_options` (`options_mod`, `options_name`, `options_value`) VALUES 
('ticker', 'active_id', '1'),
('ticker', 'separator', '+++'),
('ticker', 'stop_mo', '1'),
('ticker', 'max_news', '3'),
('ticker', 'max_user', '3'),
('ticker', 'max_dls', '3'),
('ticker', 'max_online', '3'),
('ticker', 'max_threads', '3'),
('ticker', 'max_wars', '3');
