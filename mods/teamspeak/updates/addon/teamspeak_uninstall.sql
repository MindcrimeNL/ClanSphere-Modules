ALTER TABLE `{pre}_access` DROP `access_teamspeak`;

DROP TABLE `{pre}_teamspeak`;

DELETE FROM {pre}_options WHERE options_mod = 'teamspeak';
