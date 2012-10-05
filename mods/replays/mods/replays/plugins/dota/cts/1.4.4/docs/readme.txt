---------------------------------------
| CTS Dota Parser 1.4.4 (CDP) - Readme
---------------------------------------
Author: Luka Zabkar / Seven
Mail: zabkar@gmail.com
Site: http://luka.zabkar.net/cdp

---------------------------------------
| 0.0 Index
---------------------------------------
 1.0 About
 1.1 Quick Feature List
 2.0 Installation
 3.0 Usage
 4.0 Known Bugs
 4.1 Todo
 5.0 Credits

---------------------------------------
| 1.0 About
---------------------------------------
CDP is an open source Dota Replay parser written in PHP, 
meant to be used with WC3 DoTA replays of version 6.59 and higher.

---------------------------------------
| 1.1 Quick Feature List
---------------------------------------
 > Supports CM mode and properly lists bans and picks
 > Supports swapping
 > Supports switching
 > Supports shuffle player mode
 > Displays end game statistics that include
	> Hero Kills / Deaths / Assists
	> Creep Kills / Denies
	> Neutrals killed
	> End game gold
	> End game Inventory
	> POTM Arrow Accuracy
	> Pudge Hook Accuracy
 > Tries to automatically determine the winning side
 > Categorizes player's actions and calculates his APM excluding picking time
 > Generates time ordered lists of player's obtained items and learned skills
 > Displays colored chat
 > XML database of Items, Skills and Heroes.
 > Easy storing and restoring of parsed replay data with php serialization.

---------------------------------------
| 2.0 Installation
---------------------------------------
 1. Extract the files.
 2. Ensure the structure is as follows:
	./*.php *.css   	- All the php / css files
	./maps/*.xml	- All the XML data files
	./images	- All the icons
	./replays	- Folder for storing replays and replay info
 3. Make sure the ./replays folder is writable.
---------------------------------------
| 3.0 Usage
---------------------------------------
Use the ./upload_reply.php file to upload replays or
write your own interface, using the included 
upload_replay.php file as a guide.

All the replays are parsed upon the first view and a
serialized representation of the replay is stored as
a REPLAY_ID.txt file. Upon further viewing the replay
data is only obtained from the serialized object.

All the replays are saved as unix_timestamp.w3g files
while conserving their original name in the $replay->extra
variable, making it possible to let the user download
the replay with the uploader's original filename. 

---------------------------------------
| 4.0 Known Bugs
---------------------------------------
 > Repeated skill leveling actions in short periods of 
 time can cause the parser to level a skill more than once.
 > When a hero is picked randomly by the game in CM
   mode the pick is not saved in the $picks array.
   
---------------------------------------
| 4.1 Todo
---------------------------------------
 > More action delay tweaking
---------------------------------------
| Credits
---------------------------------------
 > Julas - Original wc3 php parser
 > rush4hire - Dota port of Jula's parser
 > esby - XML Structure / Data
 > Tedi Rachmadi - Reshine