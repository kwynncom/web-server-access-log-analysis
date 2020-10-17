# web-server-access-log-analysis
parse and analyze web server access logs

Soon this will filter out robots, show referrals, etc.  I've already written most of the code but have not posted it here yet.

At the moment this does a binary search / filter to filter by date, and then it parses lines to output an associative array 
for each line, with all the typical data in a line plus an integer UNIX Epoch timestamp and some extra processing on user agent.

NOTES / CHANGES GOING BACK IN TIME

2020/10/16 9:55pm EDT (GMT -4) - I will probably get rid of the comments in index.php.  The comments are in the first version.  

DISCUSSION / MORE

I had roughly 271,000 lines before the filter and 29,000 afterward. That's ~271k lines from June 20, 2020 until most of October 14, 2020, and 
29,000 lines since October 1.

With the binary filter, it takes around 2 seconds to process.  Even if I filter dates--return from the function--when I create the timestamp in the parser, it 
takes around 14 seconds to run.  I may do a multi-core version at some point.

HISTORY

For my own record, I wrote part of this in early February, 2019.  I wrote the parser and more code that is yet to come.
