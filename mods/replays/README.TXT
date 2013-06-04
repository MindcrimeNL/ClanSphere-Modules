
Date: 2013-06-04

Mindcrime's replays module
==========================

This module upgrades the standard replays module with the following extra's:

- Plugin extension:
  It is possible to automatically parse a replay and show extra information
  for a replay
  
	Standard there are (external) parsers included for:
	- WarCraft III (WC3)
	- Defense of the Ancients (DotA)
	- StarCraft II: Wings of Liberty (SC2)
	
	A small example plugin extension is included.
	
- Accessibility:
  - Added access field: It is possible to deny access for specific user levels

- Users from access level 3 (member) and upwards can manage, create and edit
	their own replays

- (Local) Download counter

Many thanks to the creators and coders of the individual parsers.

In this package the following external code was used:

1) DotA CDP Replay Parser v1.4.6
================================

This is a replay parser for DotA.
It is still actively developed.
The source was modified by me to integrate it into the parser. 

License: Unknown
Author: Seven
Email: zabkar@gmail.com  (Subject CDP)
URL: http://luka.zabkar.net/?page_id=107

2) SC2 PHPSC2Replay Parser v1.61
================================

This is a replay parser for SC2.
Currently it is not actively developed. The last version does not work
with newer SC2 patches or SC2 HotS.
One small modification was made to the code to fix a PHP warning.
Patch date: 2011-10-02 (SC2 1.4.1)

License: GPLv3
Author: Lauri Virkamaki
Email: lauri.virkamaki<at>gmail.com
IRC: irc.quakenet.org, nick Ascylon (usually available from 17 CET onward)
URL: http://code.google.com/p/phpsc2replay/

3) WC3 Julas Replay Parser v2.4
===============================

This is a replay parser for WC3.
Although a recent version appeared, it is not really in development anymore.
The source was modified by me to integrate it into the parser.

License: Unknown
Author: 2003-2010 Juliusz 'Julas' Gonera
Email: julas@toya.net.pl
URL: http://w3rep.sourceforge.net/

4) iZiReplay v2.1.0
===================

This is an old replay package for WC3.
This package is not being developed anymore.
Only the images were used from this package.

License: Unknown
Author: S0k4R / CoW.GuMMi
URL: http://www.mycow.de/ (not functional anymore)
