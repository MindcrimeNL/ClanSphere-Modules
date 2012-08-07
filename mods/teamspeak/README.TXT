INTRODUCTION
============

REMEMBER: THIS IS A BETA!

This is a general TeamSpeak module for ClanSphere v2009.0.3
or ClanSpher v2011.0.0 for TS2 and TS3 servers.

Since TS3 does not use login/password anymore, the create/delete
account is different on TS3. You get a token for entering the
"Normal" user group in TS3 when you use create account. When using
that token, the TeamSpeak module can then find the user account
on the TS3 server and can delete that account.

CURRENT ISSUES:
===============

- It is not possible to delete a TS3 account when the user is
  connected to the TS3 server. First disconnect, then delete
  your account.

INSTALL
=======

- Upload all files to your ClanSphere site
- In ClanSphere -> System -> Database -> Import:
	select updates/teamspeak_install.sql
	press/click submit
- Add in your setup.php crypt key (minimal 8 characters):
	$cs_main['crypt_key'] = 'mysecretkey'; // CHANGE THIS!

UNINSTALL
=========

- In ClanSphere -> System -> Database -> Import:
	select updates/teamspeak_uninstall.sql
	press/click submit
- Remove the following folders/directories:
	mods/teamspeak
	themes/base/teamspeak
- Remove the following files:
	lang/English/teamspeak.php
	lang/German/teamspeak.php
	symbols/crystal_project/48/ts1.png
	symbols/crystal_project/48/ts1.png
	updates/teamspeak_install.sql
	updates/teamspeak_uninstall.sql
	README_TEAMSPEAK.TXT
- Remove this file only, if you are SURE you do not need it anymore. This
	file was also used and installed by the old ts2panel module:
	system/runstartup/tools.php

CHANGES
=======

Changes in comparison to TS2Panel module for ClanSphere v2009.0.2:
- Support for TS3, based on (a modified version of) TS3Admin class v0.1.5
- General tss class which provides a general interface between
  TS2 and TS3 servers
- Registration access (level 0-5, 0 = no registration) can be set per server
- View access (level 1-5) can be set per server
- Removal of a TS account is only possible for non server admins.

COPYRIGHT
=========

Intellectual property information for this module:

- Based on my own version of the TS2Panel module for ClanSphere v2009.0.2 
License: Beerware

- Original TS2Panel module by Patrick Jaskulski
License: Unknown

- TS3 admin class

Taken from: TS3Admin class v0.1.5
Modified: Yes
License: GPL v1.3, November 2008

ts3admin.class.php written by Par0noid (Stefan Z.)

You can use the software under the terms of the GNU General Public License.

Dev-Website: http://ts3admin.de.tp
Author-Website: http://par0noid.de

- TS3 images

Taken from: TS3 WebViewer v0.6.1
Modified: No
License: Unknown

// TS3 WebViewer created by Markus Bodmann
// http://www.gilligans-island.de

