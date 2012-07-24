RENAME TABLE challenges TO {pre}_xaseco_challenges;
RENAME TABLE players TO {pre}_xaseco_players;
RENAME TABLE records TO {pre}_xaseco_records;
RENAME TABLE votes TO {pre}_xaseco_votes;
-- These may not exist: plugin.rasp
RENAME TABLE players_extra TO {pre}_xaseco_players_extra;
RENAME TABLE rs_rank TO {pre}_xaseco_rs_rank;
RENAME TABLE rs_times TO {pre}_xaseco_rs_times;
RENAME TABLE rs_karma TO {pre}_xaseco_rs_karma;
-- These may not exist: plugin.matchsave
RENAME TABLE match_main TO {pre}_xaseco_match_main;
RENAME TABLE match_details TO {pre}_xaseco_match_details;
