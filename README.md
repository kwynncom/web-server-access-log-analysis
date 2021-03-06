# web-server-access-log-analysis
parse and analyze web server access logs

https://kwynn.com/t/20/10/ua/  - user agent code is now running here


NOTES / CHANGES GOING BACK IN TIME


10:33pm

Starting agent analysis again


11/14 8:29pm

Partially redundant files do work.  I am probably about to delete one of the original "parse" files / functions in favor of the version that separates out 
the HTTP command and HTTP version.

I'm reworking the "new" parse to match the older to a degree.  That's working now.


2020/11/13 9:33pm

going back to minimal parse version
starting on de-duping load again


****************

1:42pm - Possibly working.  


10/24 12:40am

This is an example of dev'ing while too tired.  The good news is that the loading filter almost works.  Now I just need to account for more than 100 
rows added.  

I've got a weird algorithm going for the filter.  The dateFilter would not work because it's a moving target.  The algorithm seems sound, though.  
Now I think I can just clear my object variables and keep iterating.


10/21 12:13am

The load process may be unstable.  I am trying to abstract fork, but may have messed up the wrong function.


2020/10/20  11:08pm EDT

> db.getCollection('lines').distinct('agent')

At MongoDB prompt or Robo3T.  The ">" is just to indicate a prompt; don't include it.

That query taught me a lot.  I am going to redo "robots."  In fact, I think I'm going to delete bots.  The previous version is in fullStack1

The previous version of robot analysis was in /fullStack1/bots.php.  I will probably delete the file soon.
***************

5:17pm - parallel processing works well.  Will delete the inter-process messages and other previous attempts.

10/19 1:15am - process control seems to be working, but it will need major reworking.

It seems that xdebug messes with all aspects of forking and messaging and whatnot.  So I probably need a command line version like this:
   php loadAndParse.php /tmp/access.log startAt:50000 endAt:100000

Also, the queue needs a new ID every time; otherwise I get messages delivered minutes or perhaps hours later.

9:08pm - push

2020/10/18 8:39pm - I'm trying shared memory, but it seems that the feature is only meant for small amounts of data for coordination.  


2020/10/16 9:55pm EDT (GMT -4) - I will probably get rid of the comments in index.php.  The comments are in the first version.  

DISCUSSION / MORE

I had roughly 271,000 lines before the filter and 29,000 afterward. That's ~271k lines from June 20, 2020 until most of October 14, 2020, and 
29,000 lines since October 1.

With the binary filter, it takes around 2 seconds to process.  Even if I filter dates--return from the function--when I create the timestamp in the parser, it 
takes around 14 seconds to run.  I may do a multi-core version at some point.


PERFORMANCE / RUNTIME MEASUREMENTS

6:46pm - InsertMany is 10 - 15 times faster than insertOne!  I suppose I should have known that.

10/19 2:12am - Given that I test near the end of a file, use "tail" before head for speed.  Thus the getLine() function does need the tot.  I can do head 
    without the tot, but it's much slower if I'm looking for the end of the file.

2020/10/18 8:10pm - Off hand I see little difference between using explode() and strtok().  strtok() uses less memory, so I'll probably go with it.


HISTORY

For my own record, I wrote part of this in early February, 2019.  I wrote the parser and more code that is yet to come.

