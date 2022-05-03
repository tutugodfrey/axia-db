#!/bin/bash
# this logic should be implemented in puppet when the following occurs
# Notice: /Stage[main]/Webdeploy/Vcsrepo[/var/www/vhosts/axia-db]/ensure: ensure changed 'present' to 'latest'
cd /var/www/vhosts/axia-db
app/Console/cake Rbac.rbac sync all
app/Console/cake Migrations.migration run all
app/Console/cake AssetCompress.asset_compress build
redis-server --daemonize yes
redis-cli flushall
app/Console/cake CakeResque.CakeResque load