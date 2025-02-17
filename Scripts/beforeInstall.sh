#!/bin/bash
apt update -y 
systemctl stop apache2
systemctl disable apache2
apt install nginx php php-fpm php-xml php-cli php-curl php-mbstring php-mysqlnd composer git -y
systemctl start nginx
systemctl enable nginx
