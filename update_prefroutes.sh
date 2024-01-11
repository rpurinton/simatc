#!/bin/bash
wget -O /var/www/simatc/prefroutes.csv https://www.fly.faa.gov/rmt/data_file/prefroutes_db.csv
mysql simatc_db -e "TRUNCATE TABLE prefroutes"
mysqlimport --ignore-lines=1 --fields-terminated-by=, --fields-enclosed-by=\\"" --fields-escaped-by=\\\\ simatc_db /var/www/simatc/prefroutes.csv
