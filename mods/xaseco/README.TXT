
XAseco module for ClanSphere v2010.x.x - Release date 2010-10-01

WHAT DOES IT DO
===============

It shows the record times of all players on a challenge/track.
It shows all record times of a specific player on all maps he has played.
It shows a ranking of the number of matches won by a player.

REQUIREMENTS
============

- Mindcrime's ClanSphere tools.php:
  http://trac.csphere.eu/csp/export/400/CSP%20Module/mindcrime/general/system/runstartup/tools.php
- XAseco v1.11 (to be) installed on your TMN Server:
	http://www.gamers.org/tmn/
- ClanSphere running on MySQL, since XAseco only supports MySQL.

INSTALLATION
============

If you are installing XAseco new:
1) Upload all files except updates/ to your ClanSphere website.
2) On your website System -> Database -> Import.
   Select updates/xaseco_install.sql and press "Run".
3) Clear the cache
4) Install and configure XAseco v1.11 on your TMN server
5) Upload all files in the xaseco_111_patch.zip file to you TMNF server.
6) Add <mysql_prefix>cs_xaseco_</mysql_prefix> to your localdatabase.xml
7) Start XAseco on your TMN server

If you are already have XAseco installed on your TMNF server:
1) Upload all files except updates/ to your ClanSphere website.
2) Stop XAseco on your TMN server.
2) On your website System -> Database -> Import.
  a) If you already have the XAseco tables in your database:
     Select updates/xaseco_rename.sql and press "Run".
  b) If you do not have XAseco tables in your database:
     Select updates/xaseco_install.sql and press "Run".
3) Clear the cache
5) Upload all files in the xaseco_111_patch.zip file to you TMNF server.
6) Add <mysql_prefix>cs_xaseco_</mysql_prefix> to your localdatabase.xml
7) Start XAseco on your TMN server

OPTIONS
=======

The only option you can set is the background color of your website content.
It will adjust the colors of the name of the players and the tracks to contrast
better. By default it is set to #ffffff.

CREDITS & COPYRIGHTS
====================

This module uses the:

- TMFColorParser v1.3c by oorf|fuckfish (fish@stabb.de)
	http://fish.stabb.de/index.php5?page=downloads&subpage=127

NOTES
=====

I've send my patches to the XAseco crew to try and get them integrated
in later versions of XAseco.

CHANGELOG
=========

2010-10-01:
- Initial release

HELP & PATCHES
==============

http://www.csphere.eu/forum/board/thread/where/14238


