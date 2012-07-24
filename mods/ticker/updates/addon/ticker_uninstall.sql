ALTER TABLE `{pre}_access` DROP `access_ticker`;

DROP TABLE `{pre}_ticker`;

DELETE FROM {pre}_options WHERE options_mod = 'ticker';
