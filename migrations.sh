#!/bin/bash
composer install
cd app
Console/cake Migrations.migration run all -p Rbac && \
Console/cake Rbac.rbac sync all && \
Console/cake Migrations.migration run all
Console/cake CakeResque.CakeResque load
