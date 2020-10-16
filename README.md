# web-server-access-log-analysis
parse and analyze web server access logs

Soon this will filter out robots, show referrals, etc.  I've already written most of the code but have not posted it here yet.

At the moment this does a binary search / filter to filter by date, and then it parses lines to output an associative array 
for each line, with all the typical data in a line plus an integer UNIX Epoch timestamp and some extra processing on user agent.

DISCUSSION / MORE

I had roughly 271,000 lines before the filter and 29,000 afterward. That's ~271k lines from June 20, 2020 until most of October 14, 2020, and 
29,000 lines since October 1.

Without the binary filter it takes about 14 seconds to process, and about 2 with the filter.  I may do a multi-core version at some point.

HISTORY

For my own record, I wrote part of this in early February, 2019.  I wrote the parser and more code that is yet to come.
