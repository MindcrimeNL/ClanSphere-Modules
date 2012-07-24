ALTER TABLE `{pre}_coins` DROP `coins_comments_received`;
ALTER TABLE `{pre}_coins` DROP `coins_comments_used`;

DELETE FROM {pre}_options WHERE options_mod = 'comments' AND options_name = 'coins_receive';
DELETE FROM {pre}_options WHERE options_mod = 'comments' AND options_name = 'coins_min_length';

