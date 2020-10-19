# web-server-access-log-analysis
parse and analyze web server access logs

Soon this will filter out robots, show referrals, etc.  I've already written most of the code but have not posted it here yet.

At the moment this does a binary search / filter to filter by date, and then it parses lines to output an associative array 
for each line, with all the typical data in a line plus an integer UNIX Epoch timestamp and some extra processing on user agent.


PERFORMANCE / RUNTIME MEASUREMENTS

10/19 2:12am - Given that I test near the end of a file, use "tail" before head for speed.  Thus the getLine() function does need the tot.  I can do head 
    without the tot, but it's much slower if I'm looking for the end of the file.

2020/10/18 8:10pm - Off hand I see little difference between using explode() and strtok().  strtok() uses less memory, so I'll probably go with it.


NOTES / CHANGES GOING BACK IN TIME

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

HISTORY

For my own record, I wrote part of this in early February, 2019.  I wrote the parser and more code that is yet to come.
