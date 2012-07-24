
Mindcrime's Twitter Module for ClanSphere Clan-CMS v2011.x.x.

Module Date: 2011-08-30

Introduction
============

Features:
- Uses the new OAuth authentication
- Users can view their own tweets, create a new tweet or view their favourite
  tweets
- Webmasters can create a new tweet (for website twitter account) and show
  their tweets on the website
- Enable/disable twitter for users
- Enable/disable website twitter
- Navlist showing last X tweets

Requires:
- datacache module installed
  ( http://www.csphere.eu/modules/module/view/id/231 )
- system/runstartup/tools.php (do NOT replace your system/core/tools.php!!!)
  ( http://svn.csphere.eu/module/mindcrime/general/system/runstartup/tools.php )
- add the following in your setup.php:
        $cs_main['runstartup'] = TRUE;
        $cs_main['crypt_key'] = 'mysecretkey'; // CHANGE THIS! (minimal 8 characters)

Uses:
- Tijs Verkoyen's Twitter OAuth PHP class v2.1.2

Bugs:
- Sometimes the "account verification" after the module has received the access
  tokens goes wrong for unknown reasons (maybe because Twitter is too busy at
  that moment?), allthough everything is correct. (Might be fixed by v2.1.2)

Installation
============

1) Install the required modules and files first
2) Upload all files, except updates/* and this file to your site.
3) Go to System -> Database -> Import and import the updates/addon/twitter_install.sql.
4) Clear the cache
5) Follow the extra installation instructions below

Extra installation instructions
===============================

OAuth Consumer Key/Secret:
- In order to use this module you need to register the application at twitter:

1) Register the application (you might need to login first):
Open http://dev.twitter.com/apps/new

Application Name: ClanSphere Module
Description: ClanSphere is a Web-CMS for clans. ClanSphere Twitter module
             written by Mindcrime.
Application Website: http://www.csphere.eu/
Organization: Mindcrime, Geh aB Clan
Application Type: Browser
Callback URL: http://www.gab-clan.org/
Default Access type: Read & Write

2) Go to http://dev.twitter.com/apps ("Your Twitter Apps") and click on
   "ClanSphere Module":

On the page you will see:

Consumer key: SOME-KEY 
Consumer secret: SOME-SECRET_KEY

3) In your ClanSphere go to System -> Options -> Twitter and enter these keys
   under "Twitter Consumer Key" and "Twitter Consumer Secret" and press
   "Create" (German: "Erstellen").

The installation is now complete.
(Note: Never distribute you SOME-SECRET_KEY to anyone)

User/Website giving the module access to create/view tweets
===========================================================

Each user (and for the website twitter too) needs to do one extra step ONLY
once, to allow the application to create/view tweets. He needs to allow you
module access to twitter.

A registered user does this by going to ClanSphere -> Settings -> Twitter and
click on "Generate Access Key" (German: "Zugriffsschluessel Erstellen").
He will be redirected to Twitter and he can allow the application access for
his Twitter account.
After allowing access the user will be redirected back to his Settings...

For your website, you must basically do the same. Go to ClanSphere -> System
-> Options -> Twitter and click on "Generate Access Key". You will be
redirected to Twitter and you can allow the application access for the Twitter
account that you would like to use as the "general website" Twitter account.

Remember, you need to do this only once! (You can do it more than once, but
currently that is not needed)

If you (or a user) does not want to use the module anymore, remove the access
via:
1-a) For Users: ClanSphere -> Settings -> Twitter and check the "delete data",
     then press "Edit".
 -b) For Website: System -> Options -> Twitter and remove the "Twitter Access
     Token" and "Twitter Access Secret" and press "Create".

2) Go to: http://twitter.com/settings/connections ("Your Twitter Connections")
   and "Revoke Acess" for the module.

