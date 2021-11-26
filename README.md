# web-server-access-log-analysis
parse and analyze web server access logs

https://kwynn.com/t/20/10/ua/  - user agent code is now running here

2021/11/25 - I created a 0.32 branch that has lots and lots of code.  I am in essence starting over on the main / master branch.

******************8

Note on branching:

The key is to create the branch and THEN to change the branch.  I think you have to commit it.  Make sure it shows up in origin / on github.  

I think these were the right commands:

git branch 0.32
git checkout 0.32
git add -A .
git commit -m "trying again to create branch"
git push --set-upstream origin 0.32
git checkout main
git add -A .
git commit -m "removing all from mai[n] temporarily"
git checkout 2a7231bda956def5e205e910062b7f3f4b23c046 cli/t1.php
git checkout 2a7231bda956def5e205e910062b7f3f4b23c046 README.md
git checkout 2a7231bda956def5e205e910062b7f3f4b23c046 parse.php
git add -A .
git commit -m "new main or master branch"
git push
