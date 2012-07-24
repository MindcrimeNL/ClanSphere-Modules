ALTER TABLE `{pre}_access` DROP `access_coins`;

DROP TABLE `{pre}_coins`;

DELETE FROM {pre}_options WHERE options_mod = 'coins';
