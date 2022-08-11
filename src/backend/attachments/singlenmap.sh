#!/bin/bash
nmap -sn -n -iL $1 -oX /var/www/html/mbintranet/backend/web/attachments/output.xml
