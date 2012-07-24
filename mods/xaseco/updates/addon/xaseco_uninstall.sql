ALTER TABLE `{pre}_access` DROP `access_xaseco`;

DROP TABLE `{pre}_xaseco_challenges`;
DROP TABLE `{pre}_xaseco_players`;
DROP TABLE `{pre}_xaseco_records`;
DROP TABLE `{pre}_xaseco_votes`;
DROP TABLE `{pre}_xaseco_players_extra`;
DROP TABLE `{pre}_xaseco_rs_karma`;
DROP TABLE `{pre}_xaseco_rs_rank`;
DROP TABLE `{pre}_xaseco_rs_times`;

DELETE FROM {pre}_options WHERE options_mod = 'xaseco';

