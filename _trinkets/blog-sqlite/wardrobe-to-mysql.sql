
-- Table: migrations
CREATE TABLE wardrobe_migrations ( 
    migration VARCHAR(255) NOT NULL,
    batch     INTEGER NOT NULL 
);

INSERT INTO wardrobe_migrations (migration, batch) VALUES ('2013_05_31_140553_create_posts_table', 1);
INSERT INTO wardrobe_migrations (migration, batch) VALUES ('2013_05_31_141423_create_tags_table', 1);
INSERT INTO wardrobe_migrations (migration, batch) VALUES ('2013_06_02_233005_create_users_table', 1);
INSERT INTO wardrobe_migrations (migration, batch) VALUES ('2013_06_02_233121_create_password_reminders_table', 1);
INSERT INTO wardrobe_migrations (migration, batch) VALUES ('2013_07_10_021355_add_user_to_posts', 1);

-- Table: posts
CREATE TABLE wardrobe_posts ( 
    id           INTEGER  NOT NULL
                          PRIMARY KEY AUTO_INCREMENT,
    title        VARCHAR(255)  NOT NULL,
    slug         VARCHAR(255)  NOT NULL,
    content      TEXT     NOT NULL,
    image        VARCHAR(255)  NOT NULL
                          DEFAULT '',
    type         VARCHAR(255)  NOT NULL
                          DEFAULT 'post',
    publish_date DATETIME NOT NULL,
    active       INTEGER  NOT NULL
                          DEFAULT '0',
    created_at   DATETIME NOT NULL,
    updated_at   DATETIME NOT NULL,
    user_id      INTEGER  NULL
                          DEFAULT '1' 
);

INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (4, 'Thank You''s', 'thank-you''s', 'With this being the first post, I wanted to take this first moment to thank several groups of people.

First, to those who have donated, thank you so much!

* Phillip C. 
* Andrew S.
* Jamie R.
* Michael H.
* Hogan L.
* Aron H.
* Sara K.
* Brian H.

Second, to everyone who has sent me an email, made a github issue or contacted me in game: Thank you.  It''s great to hear from people.

A great example was this past Saturday.  The site went down and I received an in game tell as well as two emails.  That was amazing, and thankfully it was an easy fix.  Never be afraid to reach out to me :)

And finally, to the 28,000 unique visitors in the past month: Thanks for using my site.  It''s quite the thrill to see everyone use it.', '', 'post', '2013-10-11 12:30:00', 1, '2013-10-08 15:11:49', '2013-10-11 16:30:57', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (5, 'Site Status, Recent Updates', 'site-status-recent-updates', '**This is being released at the same time as another post, [Thank You''s](/blog/post/thank-you''s), so please read that one as well**

Obviously this news/blog section is new, and there is still a lot of work to be done.

I''d like to start with a little history.

The site was launched on Friday, September 6th, 2013 after less than a week of development.  It was only the Equipment page, as well as the Stats, Food and Materia pages at that point.  The game was only released a week prior, and I was suffering from 1017 errors (as was everyone else).  I decided to make some use of that down time and do some research.

On that Friday I made a [Reddit Post](http://redd.it/1luntl).  I got a ton of great feedback, and worked like mad that weekend, and made a [second post](http://redd.it/1lzowx) the following monday after I received better data and bought an actual URL.  On a personal level I didn''t like posting that close to each other and as a knee jerk reaction I haven''t posted any main threads since.  I comment inside crafting related threads when I can.  A third will be coming after my next "big" update.

Planned Updates:

There is a design coming.  That''s the "big" update.  We can get rid of the boostrap look.  I''ve withheld some of my page by page improvements because of it.  Regular updates will still happen: new pages, updating page layouts and looks, better data.  If you''re really interested in what''s coming, checkout my [GitHub issue list](https://github.com/tickthokk/ffxiv-caas/issues).

There are a lot of sites that will likely do things better than I, like specific Guides/etc.  I want to acknowledge those and link to them when I can.  If I get enough experience under my belt then I may write some myself, but that''s a long ways off.  I will plan on having a "Crafting Diary" post segment, but those are to be taken with a grain of salt.

Recent Updates:

I don''t want to list everything, so these are just the weird ones I''d like to give some exposure.  Future posts will likely be more in depth.

1. Crafting.  This page was updated to move the left list into the main body, as well as some options to increase/decrease the amount to make.  The checkbox was replaced with a counter so you can keep better track of what you''re doing.
2. The Leve page received a minor update recently that I''m sure a lot of people are missing.  On each row, the words "XP" are a link to a [more in-depth page](/leve/breakdown/142) analyzing how many turnins it takes to level using that leve.  Then at the bottom of that page you can compare it against another.  
3. I linktraded with [FFXIV Clock](http://ffxivclock.com/), as you can see in the footer of the main site.  I don''t have a gatherer that high, but when I do I know I''ll be using it!

As always, feedback is wanted and welcome.  Don''t feel like you have to sign up to GitHub to make an issue, just email me and I can take care of it.  The project started to feed my own need to be efficient, so as I start to use them I recognize things I wish it did, or acted differently.  But I won''t catch everything.

Happy Hunting!', '', 'post', '2013-10-11 12:31:00', 1, '2013-10-08 15:29:46', '2013-10-11 16:31:09', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (6, 'Dear Crafting, Part 1', 'dear-diary-part-1', 'Dear Crafting, a series detailing my leveling progress.

## Foreword

I''ve seen a lot of crafting theories.  I simply want to log my observations and then let people comment on them as needed.  In no means am I an expert, as of this time of writing my highest relevant classes are 35 PLD, 30 MIN and 25 BSM.

This first post is just to highlight my crafting progress so far.

I have everything, at minimum, to level 15.  I felt this was necessary so I could get all of the level 15 abilities.

## 1 to 10 :: All Crafting and Gathering Classes

These were pretty straight forward.  With the help of my crafting/gathering lists I didn''t need any Leve help at all.  I almost always went for high quality for the XP; I only think I quick synthed one batch of weaver supplies.

You get a lot of extra xp by having your crafts support each other.  I switched classes a lot during these levels and tried to make everything myself.  Macros helped an insane amount here, and continue to.

I read quite a bit of threads on Reddit about the end game gold scenario, so it spooked me to be as money efficient as possible.  This came at the cost of time, but it also helped me debug my website.

My only regret is not using more food.  In the very least for the 3% XP, but the Control would have been nice too for HQ.

## 11 to 15 :: Gathering

Not a lot to say here.  I followed my Gathering guide to see what I actually needed (Hint: A _lot_ of iron ore).

## 11 to 15 :: Crafting

This is where it got tricky.  I leveled GSM and WVR first, because I had heard their level 15 abilities were the best.  It''s debatable, but right now I am a big fan of Careful Synth and Manipulation.

I kept my gear up to date, and I always had 17 CP left over (and a Careful Synth costs 18).  Get some [Frumenty](/food?name=frumenty), it''ll increase your CP so you can get that extra move in.

For some classes, Leves were super important.  I used a lot for Alchemy.  It has like 7 recipes between 11 and 15, whereas carpentry has like 30.  I think I only used like 3 or 4 leves for Carpentry.

And holy crap, your first HQ Leve turnin makes you never want to turn in a non-HQ again.  I knew this already, but you get three times the xp and gil.  When I first heard that I was like "Yeah, okay, that''s cool", but then you turn one in and then you''re like "I''m never turning in a NQ item again".  

## 15 to 30 :: Mining

Somewhere in my 11-15 Crafting I decided that my focus should be Armoring and Blacksmithing to support my Paladin.

I bit the bullet and bought mining gear instead of crafting it myself.  It wasn''t terribly expensive.

Again, I followed my Gathering guide.  I hit a snag somewhere in the mid 20''s.  If you look at the page you may see the pattern I''m talking about.  From 19 to 24 there''s only like 400 items to get.  You can go after the water and silver ore, but I was missing too much for my tastes.  So I busted out a couple Leves (Quarrymill for both the level 20 and 25 Leves, which was convenient enough) to speed that up to level 25.

By the time I was done with the Waters and Ore, I was only around level 27.  The next major thing was Bomb Ash, but that was all the way in Southern Thanalan (and I''m still in the South Shroud, where the Leves are).  So I Leve''d some more up to 29 and hoped that the Bomb Ash (and other misc things not in South Shroud) would level me. I think my level came from the Wyvern Obsidian; getting 15 HQ of those for the level 30 quest leveled me up.

## 16 to 20 :: Blacksmith & Armoring

At around level 18, when I got Careful Synthesis, things really started to click.  I read this [reddit post](http://redd.it/1mh6um) and now that I had three cross-class abilities to choose from it was really nice.  The recipes with 70/80 durability were easier to manage than the 40 durability recipes, because I could use all 5 stacks of Steady Hand before using a Durability restoring move.

Also, I forgot to equip my new off-hand item throughout my Blacksmithing process.  Keep checking your gear folks!

## 21 to 25 :: Blacksmith

I''ve started entering the territory of "no longer providing for myself".  Botany is still at 15, so those random things I need to buy.  I had to buy some Yew Logs, which I could have crafted myself if my CRP was high enough.  I think this will continue to be a gold sink for a while.  The good news is that a lot of my previously HQ''d gear I made is selling and continues to support myself.  

I''m starting to recognize the Leve system a little more.  It''s not always true, but sometimes during crafting you''ll get lucky, strike an HQ item, and it will be needed for a Leve too.  I needed another item, and while making the pre-requisite materials for it I leveled to 25, rendering the need for it useless.  Thinking about it, I should have finished making it to turn in anyway, but my point is to never underestimate the power of crafting your own materials.

## Getting through it all

It can be a little boring, especially when you''re just pressing a macro every 20 seconds or so.  To get through it, having two monitors is great.  I watched a lot of [Game Grumps](http://www.youtube.com/channel/UC9CuvdOVfMPvKCiwdGKL3cQ) and [Continue](http://www.youtube.com/channel/UCWN49rf6HI1Zb5T-Mism74g).  Also some American Dad on Netflix.  Also a game like Animal Crossing on the 3DS has a good pace for doing some of that too.

I''ve heard if you plug in a controller, and set it to work even when the window doesn''t have focus, you can play like that too.  Sounds interesting and I''d like to give it a go.  I [printed some of these](http://redd.it/1nfvpo) off in preparation, but haven''t gotten around to it yet.

Happy Crafting!', '', 'post', '2013-10-11 12:32:00', 1, '2013-10-08 18:30:07', '2013-10-11 16:31:19', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (8, 'Gathering XP Increased', 'gathering-xp-increased', 'Thanks to the recent patch, gathering XP has increased.

XIVDB datamined the changes and made the post here: http://www.reddit.com/r/ffxiv/comments/1oieqi/datmined_changes/

I did the math on it, and at level 16 it''s a 4% increase, and at level 49 it''s a 58% increase.  I also graphed it out:

![Graph](http://i.imgur.com/dfc0AkN.png)

Happy Gathering!', '', 'post', '2013-10-15 19:28:00', 1, '2013-10-15 19:28:29', '2013-10-16 15:06:46', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (9, 'New Server, Updates & Thanks.  Help out with Leve Rewards!', 'new-server-and-updates-thanks-help-out-with-leve-rewards!', '*Server News:*

If you''re seeing this post, you''re seeing the new server!  I migrated to a Rackspace Cloud setup and now we''ve got more RAM.  The site had gone down a few times due to RAM issues and now that''s fixed.  We also have enough for Redis caching, which should make the site faster as well.

*Site Updates:*

* The Gathering page has been replaced with **Career**
 * An explanation of my definition of Career exists at the bottom of that page.
* Vendor Locations added to Career page.  That functionality needs extended to other pages still.
* Leve Rewards added to Leve page.
* Food and Materia pages changed.  The food page is still too disorganized for my liking, and I''ll get back to it in due time.

*Thanks to the following Donators:*

* Jason B. (Recurring donation, you''re amazing!)
* Jonathan C.
* Phillip C. (Again!  Thanks)
* Michael Z.

*Help out with Leve Rewards!*

Thanks to a reddit user (/u/Celaeris) I was given a resource for leve rewards.  It was in Japanese, so I had to rely on Google Chrome''s Translator.  Some of it didn''t come over so well (Ash of bomb?  Bomb Ash).  I cleaned it up as best I could, but the rest I didn''t want to make any assumptions.

I created a google spreadsheet to have you guys help out with it!  I don''t have access to these in game yet.  Please play nice and don''t mess the sheet up. 

[https://docs.google.com/spreadsheet/ccc?key=0AqoFdvGbCc-8dHdWcGxpYlNEOURvWVhMZUowZ040MXc#gid=3](https://docs.google.com/spreadsheet/ccc?key=0AqoFdvGbCc-8dHdWcGxpYlNEOURvWVhMZUowZ040MXc#gid=3)

You''ll notice the "Shards" are separated.  That''s how they translated, and some of them were actually listed as "Lightning" or "Water", but these just said Shard.  My personal theory is that it could be any of the six, but I have no idea.

*Can''t help with Leves, but still want to help out?*

Spread the word!  Tell your Free Company and Linkshell''s about the site.  Maybe even yell it to the zone.  Don''t be an annoying with it though ;)

If you''ve got some extra cash you could always Donate it.  You''ll find a link at the bottom of the main site.

Make a comment below or send me an email!  I love hearing from you guys, especially when you have suggestions.  I''m just one person and can''t think of everything :)

Happy Hunting!

*P.S.* If you haven''t gotten your cactuar or bomb earings yet go do that.  It won''t really help you with crafting, but they look cool.  Well, the cactuar may help a hair if you''re under levels 15/7, then Fang Earrings/Copper Earrings take over.', '', 'post', '2013-10-20 01:04:00', 1, '2013-10-20 01:04:20', '2013-10-22 00:34:05', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (10, 'Updates', 'updates', 'I closed ten tickets today, that''s pretty awesome.  Lots of small stuff, but also one kinda important one.

There were some missing Leves, that''s been addressed.  There was also a bug with multi-yield items in the crafting list.  There''s a slight change in process now.  If you need 6 Cornmeal''s (who''s recipe is a 4x yield), it''ll just make you craft 8 instead (two full synths).

You can now hotlink to Leves, Quests and the Recipe Book.  It utilizes the # at the end of the URL.  [Cornbread Entry in Recipe Book](http://craftingasaservice.com/recipes#cornbread|all|1|70|).

The "big" update is in the Crafting list, if you obtain items in the bottom list, the needed amounts above will change, eventually going to zero on everything.
', '', 'post', '2013-10-27 01:40:00', 1, '2013-10-27 01:40:56', '2013-10-27 19:46:51', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (11, 'Happy Holidays', 'happy-holidays-2013', 'Hey Crafters!

Happy Holidays from Santa Sakhr.

![](http://i.imgur.com/1syCfSQ.jpg)

There haven''t been any updates for a while.  Normal gaming got in the way!  But this holiday event definitely made me appreciate a lot of the things I like about FFXIV.  I''ll be starting development back up after the holidays.

As always, thanks to everybody for using the site.  4000 visits this past Sunday, pretty amazing for just one guy who hasn''t been advertising!

I wanted to thank the people who have donated as well:

Andrew, Anna, Karen, Robotronic Games, Keisha, Jodi, Bryan, Jason, Anthony, Alejandro, Brian, Firetako LLC and Karoly.

A very special thank you to Anna who gave a very generous donation and included a special message to my Mrs.  You get this image as my thank you: http://i.imgur.com/Ey37akv.jpg

Happy Crafting Everyone!', '', 'post', '2013-12-20 02:20:22', 1, '2013-12-20 02:20:22', '2013-12-20 02:20:22', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (12, 'Updates for Patch 2.1', 'updates-for-patch-2-1', 'Happy Heavensturn Crafters!

Those horse heads are frightening, but at least the snake and dragon ones are kinda cool.

Data has been updated for Patch 2.1!  There were also many fixes to the site.

* The crafting list should now be way more accurate when changing totals
* 2.1 reduced HQ leve turnin rewards (2 times reward instead of 3 times)
* Equipment now shows if it''s been rewarded
* Our new data lost specific coordinates for gathering, but I definitely want that back in at some point.
* Recipe Book sorting option added
* Equipment and Crafting now have a "load last setup" option
* The rest of the pages have a manual "save/load" option (so you can come back to the site and continue where you left off).  If you want multiples, you can favorite the page as the URL is a permalink, but it''s been like that for a while.
* There were some other general bug fixes that were resolved as well.

Important-ish news: I will soon be privatizing my github account.  This means the issue tracker has been removed.  Email is still a great way to report bugs.  If it becomes an issue then I''ll add a different bug tracker.

I also uncovered a new resource for information which could add a lot of functionality to the site.  It''s pretty exciting, but it will take some research and time to implement.

In player news: I finally hit level 50 on my PLD on the 1st.  A good way to bring in the new year.  I''ll be crafting again which will mean more attention paid to the site!  Huzzah!

Keep on Crafting!', '', 'post', '2014-01-08 03:41:00', 1, '2014-01-08 03:41:29', '2014-01-08 12:52:57', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (13, 'Development Continues', 'development-continues', 'It''s been two months since my last post, so I felt I needed to have a "I''m still working" post.

Development is coming along nicely, and I''m getting closer.  I basically have to re-write all of my queries to adapt to the new data structure, and it''s taking some time.  I''m also catching smaller bugs here and there, so it''s going to be even better.  After this I can focus on some of the more niceties.

As always, thanks for everybody''s feedback, comments and donations!

Happy Crafting!

', '', 'post', '2014-03-03 17:59:00', 1, '2014-03-03 17:59:10', '2014-03-03 17:59:22', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (14, '2.2 is coming, and I''m not ready >.<', '2-2-is-coming-and-i''m-not-ready', 'So, 2.2 is coming: [Lodestone Crafting Post](http://na.finalfantasyxiv.com/lodestone/topics/detail/fdcac38ca1a190627bf4e5b1362cae51e70137f7)

Looks cool.  I don''t think I will be ready in time, but I am getting very close!

I think I''m still behind on some 2.1 stuff too, but I can''t update because it would destroy the site in it''s current state.

Happy Crafting!', '', 'post', '2014-03-18 13:16:00', 1, '2014-03-18 13:16:17', '2014-03-18 13:17:06', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (15, 'Site Updated for 2.1: New Data Source', 'site-updated-for-2-1-new-data-source', 'I''m still waiting on data from 2.2!  Sorry for the delay, but this one''s out of my hands :)  The patch just went live this past week, so hopefully it will be there this next week.

I had to touch every page because my data structure changed, so with any update, there will be bugs.  Please do report them either in comments below or by emailing me!

The footer now displays what version the data is at.  I''ll be keeping that around as I envision always being a week behind, at least.  Such is the nature of the beast.

Here''s some fun things I have planned:

**Multiple Languages:**  I have item data for German, French and Japanese now.  These domains will follow this pattern: [http://fr.craftingasaservice.com/](http://fr.craftingasaservice.com/).  It''s technically already setup, but I still have some logic in place based on English names, which I''ve got to switch out.

**Routes:** Some kind of coordinate system where you add items to your basket and they will be marked on the map.  I have a *very rough* maps page setup, and if you''d like to preview it, it''s here: [http://craftingasaservice.com/map](http://craftingasaservice.com/map)  Obviously nothing else with it is setup.  I didn''t have that data before, but now I do!

**HQ Toggles:**  I now have all the HQ data, so I just need to figure out a good way of toggling that on and off.

**Equipment Page:** I''ve been considering changing this to reflect a layout similar to [Ariyala''s Gear Calculator](http://ffxiv.ariyala.com/).  If anybody feels strongly for or against that let me know.

**"Logging In":**  Thanks to the [XIVPads](http://xivpads.com/) Lodestone API, I might be able to have you set your character and the calculations on the site would changed based on that.  For example, it wouldn''t suggest self sufficient recipies to you that you can''t make.

**Food:**  The food page is atrocious.  It needs a new layout.

**Mobile Friendly:**  I want to make the site friendly for you phone and tablet users.  Not everybody has two monitors or wants to alt tab.


*Thanks again to all the donators!*

Happy Crafting!', '', 'post', '2014-03-30 15:15:00', 1, '2014-03-30 15:15:43', '2014-03-30 15:26:34', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (16, 'Patch 2.2 Data is Live!', 'patch-2-2-data-is-live!', 'That is all :)', '', 'post', '2014-04-05 15:09:24', 1, '2014-04-05 15:09:24', '2014-04-05 15:09:24', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (18, 'Site Updates, Through the May-lstrom!', 'site-updates-2-2-a', 'Hello again Crafters of Light!

With this update there were a lot of fixes to the Equipment and Crafting pages in particular.  The short version?  They work better now.

Updates have been sparse lately because of my schedule, but that''s cleared up now!  I''ve been on 8 planes in the past 3 months.  Hopefully I''m done flying for a long time.

During the past month the site had some issues, but big thanks to my server guru for handling some of that when I wasn''t able to.  The server received an upgrade in memory which helped immensely.

One new useful feature, and one "for fun" feature was added.  The useful one is on the crafting list page.  You can now share your Crafting Lists via the URL.  The for fun one is in the footer.

Thanks to these users for pointing out the bugs or features that were resolved in this release.  Thanks Sliph, Anja, Egiesel, Sefie, Daikaiopt, Bbindo, Khalid, Mdown, Holger, Oktoberfest and Icefox!  As always, thanks to those who have donated as well!

Enjoy!', '', 'post', '2014-05-18 02:19:00', 1, '2014-05-18 02:19:47', '2014-05-19 01:00:53', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (19, '2.3 Data, New Domain & Subreddit!', '2-3-data-new-domain-and-subreddit!', 'Hey everybody,

Development was slow there for a while, but I''m back on the wagon.

I got data updated to version 2.3.

I purchased [FFXIVCrafting.com](http://ffxivcrafting.com)!  I''ll be transitioning to this as my formal site, but keeping craftingasaservice around :)

I also set up a [SubReddit](http://www.reddit.com/r/ffxivcrafting).  Give it a visit!  Report bugs, ask for wish list items, or just keep emailing me, anything works.

I''ve been promising a new design for a while, and that''s happening really soon as well!

Happy Crafting!', '', 'post', '2014-07-22 22:54:01', 1, '2014-07-22 22:54:01', '2014-07-22 22:54:01', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (20, 'Did somebody say Maps?', 'did-somebody-say-maps', 'Hey Crafters, we''ve got some fun updates this time around.

Maps!

Go into a Crafting List and click the "Map It" button.  Things won''t be super accurate, and I may even be missing some stuff.  Be sure to read the Legend!  I''d like to improve it, but I want to see how you guys use it.  So feedback is pretty important here.  Let me know what changes you''d like to see to it on the [Subreddit](http://www.reddit.com/r/ffxivcrafting)! 

Also on the Crafting List is an export/download option.  This generates a CSV file which will copy/paste into Google Spreadsheet or open in Excel very easily.  This page also got a printer friendly touchup.

A few miscellaneous bugs were fixed as well, and some backend/framework upgrades.

If you find any new bugs, be sure to check out my [Report A Bug](http://ffxivcrafting.com/report) page.

Happy Crafting!', '', 'post', '2014-07-27 02:16:38', 1, '2014-07-27 02:16:38', '2014-07-27 02:16:38', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (21, 'One Year Anniversary Mega Update!', 'one-year-anniversary-mega-update!', 'Happy FFXIV Anniversary Crafters!  Not only is it the anniversary of the game, but it''s also the sites!

![](http://share.gifyoutube.com/axM0xQ.gif)

*Credit to user mercenarri on reddit for the image*

I officially launched (meaning I posted the link to Reddit) on 9/6/2013.  I suppose I only spent about two or three weeks developing before that.  I received a lot of love from the community, and I''m happy to develop and maintain this site.

## Site Traffic, a history

I check my Google Analytics account frequently and I wanted to share some of the cool numbers over the past year.

The traffic peaked in October of last year at 138,000 sessions, which ramped down to a pretty consistent 45,000 sessions per month.

There''s been almost 3 million page views, and on average most of you visit about 3 or 4 pages before going away.  Half a million of those page views belong to last October alone.

There''s also been a solid average of 19% new visitors every month.  It''s cool to think that a fifth of you reading this just discovered my site.

## What''s changed?

Obviously, the design.  I''m still using Bootstrap, but now it''s actually distinguished!  Each page got a little touch up to support it.  Design changes are still ongoing :)

The initial Crafting page was cleaned up.  You can still access the old one by using the "Advanced" option below the button.  It''s more streamlined to match what you''d expect to see in the actual crafting log.

Language Switcher:  Right now it only does game data, but I would like to also do any verbiage on the site the same way.  I believe I''ve had two French players offer their translation services, so I''ll be in touch with them soon.  If you know French, German or Japanese and wish to help drop me a line.  I don''t know the volume of work, but I doubt it''s a lot.

Account Tie-in:  You can now "log in" to your character and some choices will pre-populate for you now.  Selecting Carpenter on the Equipment starting page for instance will fill in your Level.  Thanks to XIVPads for their API :)

Leves page:  The "in depth" Leve analysis was made more apparent.  Instead of "XP" being a link, the whole XP section is a button.  This page was also enhanced by revealing the Recipe for the item to  empower the player to see what it takes.

Fishing Leves were also added.

The Food Page:  I love what I''ve done here.  It includes the HQ foods as well, and there are graphs comparing the foods.  Please check it out!

Bug Fixes:  There were numerous Missing Leves, Bad food stats, the map was broken and some other stuff that were all resolved.

## The Future

There''s still a lot of cool things to be done.  Account level stuff can be integrated further into the equipment and crafting sections to be sure.

If you''re really interested on what''s on my Todo list, you can view my open tickets on Github (https://github.com/Tickthokk/ffxivcrafting/issues).

## Coming Clean

I think it''s time to state that I haven''t actually played the game since March.  I do keep up to date on what''s happening, and I''d love to start playing again.  Life has been busy, and it''s about to get even busier.

Rest assured though.  As much as Gaming is my Hobby, Web Development is even bigger love of mine.  When you have an awesome project like this to work on it''s very rewarding on a personal level.

## Special Thanks

I couldn''t have done this alone!

Thanks to Shawn W. for server hosting and assistance.  It''s a huge relief to have such reliable servers!

Thanks to Cavin M. for the design and general support.

Thanks to everybody who''s suggested new ideas for the site.

Thanks to these amazing people who donated this past year, you guys are awesome :)

Alejandro B (multiple!), Alisa L, Andrew B, Andrew Se, Andrew Sm, Anja W, Anna D, Anthony H, Aron H, Benjamin M, Brian H, Brian S, Bryan W, Chad D, Dexter N, Elizabeth G, Erich D, Firetako LLC (multiple!), Gary M, Herbert W, Hogan L, Hunter W, Jackson J, Jamie R, Jason B (multiple!), Jason S, Jodi G, Jonathan C, Justin S, Karen O, Karoly B, Keisha H, Laura F, Lee W, Leskova M (multiple!), Luke S, Matthew K, Meintje D, Michael H, Michael Z, Miltes I, Phillip C (multiple!), Rebecca B, Robotronic G, Ryan B, Sara K, Sharleen H, Thomas F, Thomas S, Trevor D, Véronique D, William F

## Spread the word

You don''t need to donate to help.  The traffic alone really pumps me up, so tell your Free Companies about the site!

As always, Happy Crafting!', '', 'post', '2014-09-05 23:59:00', 1, '2014-09-05 23:59:24', '2014-09-06 00:15:39', 1);
INSERT INTO wardrobe_posts (id, title, slug, content, image, type, publish_date, active, created_at, updated_at, user_id) VALUES (22, 'New Leves Page', 'new-leves-page', 'I simplified the Leves page, and really thought about why you''re there.

The old page will be available at [/leve](http://ffxivcrafting.com/leve) for a while still, but I''ll be removing it eventually.

Enjoy!', '', 'post', '2014-09-12 23:20:44', 1, '2014-09-12 23:20:44', '2014-09-12 23:20:44', 1);

-- Table: tags
CREATE TABLE wardrobe_tags ( 
    post_id INTEGER NOT NULL,
    tag     VARCHAR(255) NOT NULL 
);

INSERT INTO wardrobe_tags (post_id, tag) VALUES (1, 'crafting');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (1, 'development');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (1, 'guide');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (1, 'test');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (2, 'development');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (3, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (4, 'news');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (5, 'news');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (6, 'crafting diary');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (7, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (8, 'patch notes');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (9, 'development');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (9, 'news');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (10, 'development');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (11, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (12, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (13, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (14, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (15, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (16, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (17, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (18, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (19, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (20, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (21, '');
INSERT INTO wardrobe_tags (post_id, tag) VALUES (22, '');

-- Table: users
CREATE TABLE wardrobe_users ( 
    id         INTEGER  NOT NULL
                        PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255)  NOT NULL,
    last_name  VARCHAR(255)  NOT NULL,
    email      VARCHAR(255)  NOT NULL,
    password   VARCHAR(255)  NOT NULL,
    active     INTEGER  NOT NULL
                        DEFAULT '1',
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL 
);

INSERT INTO wardrobe_users (id, first_name, last_name, email, password, active, created_at, updated_at) VALUES (1, 'Sakhr', 'Ruh''wah', 'tickthokk@gmail.com', '$2y$08$zIXZMPkZJ/xRxKOlJsuYnu3B78lRUxfxadMMbxBqh1smC9oGYsnTS', 1, '2013-10-08 13:30:25', '2013-10-08 13:30:25');

-- Table: password_reminders
CREATE TABLE wardrobe_password_reminders ( 
    email      VARCHAR(255)  NOT NULL,
    token      VARCHAR(255)  NOT NULL,
    created_at DATETIME NOT NULL 
);


-- Index: tags_post_id_tag_unique
CREATE UNIQUE INDEX tags_post_id_tag_unique ON wardrobe_tags ( 
    post_id,
    tag 
);


-- Index: users_email_unique
CREATE UNIQUE INDEX users_email_unique ON wardrobe_users ( 
    email 
);

