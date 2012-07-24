ALTER TABLE `{pre}_coins` DROP `coins_board_received`;
ALTER TABLE `{pre}_coins` DROP `coins_board_used`;

DELETE FROM {pre}_options WHERE options_mod = 'board' AND options_name = 'coins_receive';
DELETE FROM {pre}_options WHERE options_mod = 'board' AND options_name = 'coins_receive_thread';
DELETE FROM {pre}_options WHERE options_mod = 'board' AND options_name = 'coins_min_length';

