
Datacache module for ClanSphere v2010.x.x - Release date 2010-11-24

WHAT DOES IT DO
===============

In short, it's a data caching system to prevent extensive database usage
for semi-static data. Semi-static data is data in ClanSphere that does
not really change that often, but which is currently being grabbed
from the database (via multiple queries) every time.

Examples are news, wars, articles. You do not write a new news/article every
minute of the day, or you add or change a war every minute. But everytime
someone visits your page, the list of recent news is being generated again
everytime using a lot of database queries, which can become a problem on
a website which is being visited frequently.

In order to improve the website performance, this datacache module can
cache this semi-static data, so that it takes less time and queries to
generate website content.

Example:
For a test site that I am currently developing, the test homepage
takes 1500 ms to generate when the recent news is not cached and takes between
35 and 38 SQL queries to generate (inclusive the test if the data is available
in the cache).
When using the cached version the test homepage takes 500 ms to generate and
takes 21 SQL queries to generate.
Performance wise:
The time to generate the homepage is shortened by 1000 ms or 66% performance
improvement and it needs 14 to 17 or 40% to 45% less queries to generate.

When caching even more stuff (like all the navlists) I can improve the performance
even more. I also changed the news module and used caching in such a way, that
the cache is never refreshed unless I have made changes to any news message (create,
edit, remove, publish). This means, that the cache in only refreshed when it had
a reason to be refreshed.

INSTALLATION
============

1) Upload all files except updates/ to your ClanSphere website.
2) On your website System -> Database -> Import.
   Select updates/datacache_install.sql and press "Run".
3) You can now use the datacaching system for your mods.

OPTIONS
=======

The only option you can set is the default refresh time for a cached item.
By default it is set to 900 (15 minutes).

SIMPLE EXAMPLE ON USAGE
=======================

Since wars or result don't change that often, you might want to cache the wars navlist.
The following example will only generate a new war navlist every 15 minutes...

Modify mods/wars/navlist.php:

At the start of the file you see:

--- code ---
$cs_lang = cs_translate('wars');
$cs_get = cs_get('catid,squadid');
$cs_option = cs_sql_option(__FILE__,'wars');
$data = array();
--- code ---

change into:

--- code ---
$cs_lang = cs_translate('wars');
$cs_get = cs_get('catid,squadid');
$cs_option = cs_sql_option(__FILE__,'wars');
$data = array();

$key = 'lang='.$account['users_lang'].'&squad='.$cs_get['squadid'].'&size='.$cs_option['max_navlist'].'&access='.$account['access_wars'];
$cachedata = cs_datacache_load('wars', 'navlist', $key, false);
if ($cachedata !== false)
{
        echo $cachedata;
        return;
}
--- code ---

AND (at the end)

--- code ---
  echo cs_subtemplate(__FILE__,$data,'wars','navlist');
}
else
  echo $cs_lang['no_data'];
--- code ---

change into

--- code ---
  $cachedata = cs_subtemplate(__FILE__,$data,'wars','navlist');
}
else
  $cachedata = $cs_lang['no_data'];
cs_datacache_create('wars', 'navlist', $key, $cachedata, null);
echo $cachedata;
--- code ---

Short explanation:

The key is used to be able to have different cached versions for the same
action within a module.
If you were viewing a news with a specific ID, your key should at least have
the ID and the language of the user (the cached result may have translated
stuff in it). Other key values might be the access level of the user (admins
may see more than regular users). In lists, the users option of max_navlist
might/must be considered.

For example, my news - recent has the following key:
$key = 'lang='.$account['users_lang'].'&cat='.$cat_id.'&start='.$start
    .'&size='.$cs_option['max_recent'].'&access='.$account['access_news'];
which includes the users language, the selected category, the start and
page size (remember news - recent is paged) and the users access level.

CHANGELOG
=========

2010-08-19:
- Fix: mods/clansphere/cache updated to CS trunk rev. 4344

2010-11-24:
- To help people with debugging: set $cs_main['no_datacache'] = TRUE; in
  your setup.php to disable any caching
- Fix: mods/clansphere/cache updated to CS trunk rev. 4455
