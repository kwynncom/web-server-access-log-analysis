# web-server-access-log-analysis
parse and analyze web server access logs
**********
This code processes web server access logs such as this line that is split into 2 lines:

66.249.70.62 - - [17/Mar/2022:16:33:55 -0400] 689777 "GET /t/9/02/apprentice_steps.html HTTP/1.1" 200 3646 "-" 
"Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" 

Every single object requested from a browser or other program is logged as above.  

https://kwynn.com/t/7/11/blog.html#e2022_0317_h3_01 - I discuss this at some length

Some of the practical goals are to distinguish humans from robots and figure out what humans (if any) are looking at.  The parts of this project 
go something like:

* user agent analysis / web display
* quickly loading 500M worth of log - entering the lines as separate rows / documents in a database
* verifying / validating the loading
* parsing lines into IP address, date, command (GET, POST), user agent, etc.

Those are the main parts.  "myips" comes from the fact that I am one of this biggest human users of my site.  There are a number of utilities I wrote that 
I use all the time.  Thus, I am trying to ID my own usage. 

"bots" is about identifying robots.  

v.c is "verify" / "validate"  

I'll come back to XOR and validation.

**********
USER AGENTS

One branch of this (not a "branch" in the git sense) is looking at "user agents" such as the above starting with "Mozilla/5.0"  
Here are user agents galore:

https://kwynn.com/t/21/12/ua/

https://kwynn.com/t/21/06/ua/ 

https://kwynn.com/t/20/10/ua/

***********
VALIDATION / XOR

I first tried validation with md4 (which is notably faster than md5).  The problem with that is that it's linear in that you can't parallelize the process 
because data-chunk A feeds into chunk B.

Thus, I did an XOR (logical exclusive OR).  XOR is commutative: A XOR B === B XOR A.  So, I XORed each line separately.  Then I can calculate the XOR of each 
line in any order and get the same result.  The XOR processes fork() as in create parallel processes.  

*****************
LOADING

I fork() for loading, too.  I balanced buffering between RAM and speed.  

A challenge I had with loading is that files can't be processed by line efficiently.  Otherwise put, one should not index a file in the loose sense of "index" 
by lines.  Keeping track of the begin and end byte pointer of each line is much, much cleaner.  Actually, I found it yet cleaner to keep track of the 
end pointer + 1 (fpp1 === file pointer plus one).  

fp0 is the beginning byte pointer of each line.

ftsl1 is the timestamp of the first line as encoded by Apache (file timestamp (per) line 1).  In this case, I found it a reasonable tradeoff to process that 
one line.  My data finally became clean versus previous attempts at keeping track of line numbers directly.

*********************
2021/11/25 - I created a 0.32 branch that has lots and lots of code.  I am in essence starting over on the main / master branch.

The branch command is listed at
https://kwynn.com/t/7/11/blog.html#e2022_0318_branches

2022/09/12 - I might remove the XOR stuff
