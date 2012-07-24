ALTER TABLE {pre}_access ADD access_xaseco int(2) NOT NULL default '0';

UPDATE {pre}_access SET access_xaseco = '1' WHERE access_id = 1 LIMIT 1 ;
UPDATE {pre}_access SET access_xaseco = '2' WHERE access_id = 2 LIMIT 1 ;
UPDATE {pre}_access SET access_xaseco = '3' WHERE access_id = 3 LIMIT 1 ;
UPDATE {pre}_access SET access_xaseco = '4' WHERE access_id = 4 LIMIT 1 ;
UPDATE {pre}_access SET access_xaseco = '5' WHERE access_id = 5 LIMIT 1 ;

INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('xaseco', 'bgcolor', '#ffffff');

--
-- Tablestructure for Table `{pre}_xaseco_challenges`
--

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_challenges` (
  `Id` {serial},
  `Uid` varchar(27) NOT NULL default '',
  `Name` varchar(100) NOT NULL default '',
  `Author` varchar(30) NOT NULL default '',
  `Environment` varchar(15) NOT NULL default '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Uid` (`Uid`)
){engine};

-- --------------------------------------------------------

--
-- Tablestructure for Table `{pre}_xaseco_players`
--

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_players` (
  `Id` {serial},
  `Login` varchar(50) NOT NULL default '',
  `Game` varchar(3) NOT NULL default '',
  `NickName` varchar(100) NOT NULL default '',
  `Nation` varchar(3) NOT NULL default '',
  `UpdatedAt` datetime NOT NULL default '0000-00-00 00:00:00',
  `Wins` mediumint(9) NOT NULL default 0,
  `TimePlayed` mediumint(9) NOT NULL default 0,
  `TeamName` char(60) NOT NULL default '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Login` (`Login`),
  KEY `Game` (`Game`)
){engine};

-- --------------------------------------------------------

--
-- Tablestructure for Table `{pre}_xaseco_records`
--

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_records` (
  `Id` {serial},
  `ChallengeId` mediumint(9) NOT NULL default 0,
  `PlayerId` mediumint(9) NOT NULL default 0,
  `Score` mediumint(9) NOT NULL default 0,
  `Date` datetime NOT NULL default '0000-00-00 00:00:00',
  `Checkpoints` text NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `ChallengeId` (`ChallengeId`,`PlayerId`)
){engine};

-- --------------------------------------------------------

--
-- Tablestructure for Table `{pre}_xaseco_votes`
--

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_votes` (
  `Id` {serial},
  `Score` smallint(6) NOT NULL default 0,
  `PlayerId` mediumint(9) NOT NULL default 0,
  `ChallengeId` mediumint(9) NOT NULL default 0,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `PlayerId` (`PlayerId`,`ChallengeId`),
  KEY `ChallengeId` (`ChallengeId`)
){engine};

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_players_extra` (
  `playerID` int(11) NOT NULL default 0,
  `cps` smallint(3) NOT NULL default -1,
  `dedicps` smallint(3) NOT NULL default -1,
  `donations` mediumint(9) NOT NULL default 0,
  `style` varchar(20) NOT NULL default '',
  `panels` varchar(255) NOT NULL default '',
  KEY `playerID` (`playerID`)
){engine};

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_rs_karma` (
  `Id` {serial},
  `Score` smallint(6) NOT NULL default 0,
  `PlayerId` mediumint(9) NOT NULL default 0,
  `ChallengeId` mediumint(9) NOT NULL default 0,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `PlayerId` (`PlayerId`,`ChallengeId`),
  KEY `ChallengeId` (`ChallengeId`)
){engine};

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_rs_rank` (
  `playerID` int(11) NOT NULL default 0,
  `avg` float NOT NULL default 0,
  KEY `playerID` (`playerID`)
){engine};

CREATE TABLE IF NOT EXISTS `{pre}_xaseco_rs_times` (
  `Id` {serial},
  `challengeID` mediumint(9) NOT NULL default 0,
  `playerID` mediumint(9) NOT NULL default 0,
  `score` mediumint(9) NOT NULL default 0,
  `date` int(10) unsigned NOT NULL default 0,
  `checkpoints` text NOT NULL,
  PRIMARY KEY (`ID`)
){engine};

