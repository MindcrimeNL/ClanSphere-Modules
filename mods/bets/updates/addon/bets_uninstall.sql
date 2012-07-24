ALTER TABLE `{pre}_access` DROP `access_bets`;

DROP TABLE `{pre}_bets`;
DROP TABLE `{pre}_bets_contestants`;
DROP TABLE `{pre}_bets_users`;

DELETE FROM {pre}_options WHERE options_mod = 'bets';
