#更新代码仓库
git fetch origin master:refs/remotes/origin/master && git reset --hard origin/master && composer install && composer update