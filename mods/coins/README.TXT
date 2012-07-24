
This module was made for ClanSphere 2011.3.
For information about ClanSphere 2009.0.x, see below.

Date: 2012-04-11

IMPORTANT
=========

If you want to use coins for comments/board, you need to copy the following files:

mods/board/com_create.php
mods/board/com_edit.php
mods/board/com_remove.php
mods/board/options.php
mods/board/thread_add.php
mods/board/thread_edit.php
mods/board/thread_remove.php
mods/comments/functions.php
mods/comments/options.php

These files are compatible with ClanSphere 2011.3 (as of 2012-03-29)

INSTALL
=======

1) Upload all files (except in updates/ and diff.clansphere-trunk.txt and this README.TXT) to your webserver:

a) If you WANT coins for comments and coins for board/forum upload all files
b) If you DO NOT WANT coins for comments:
   - Do NOT upload: mods/comments/*
   - Do NOT upload: themes/base/comments/*
c) If you DO NOT WANT coins for board/forum:
   - Do NOT upload: mods/board/*
   - Do NOT upload: themes/base/board/*

2) System -> Database -> Import the following files (in that order):

coins_install.sql
comments_install.sql (ONLY IF YOU WANT comments for coins)
board_install.sql (ONLY IF YOU WANT comments for board/forum)

3) Clear the cache

ClanSphere 2009.0.x
===================

If you use a ClanSphere version 2009.0.x:

- Use the files in updates/2009.0.x/mods/board if you want to use coins for board
- Be sure to add/upload the system/runstartup/coins.php
- Clear the cache

