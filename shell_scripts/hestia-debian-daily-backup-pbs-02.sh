#!/bin/bash

###########   Only For hestiacp on debian 11  #############

# include backup-config file 

mydir="${0%/*}";
echo $mydir;
source "$mydir"/backup-config.sh;
# create a backup directory by date  yyyy-dd-mm format
mkdir -p /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`;


### It backs up each database into a different file
echo $(date) : "mysqldump started for each database";
databases=`mysql -u $USER -p$PASSWORD -e "SHOW DATABASES;" | tr -d "| " | grep -v Database`;
for db in $databases; do
    if [[ "$db" != *"splr_db"* ]] && [[ "$db" != *"open-track-log"* ]] && [[ "$db" != "information_schema" ]] && [[ "$db" != "performance_schema" ]] && [[ "$db" != "mysql" ]] && [[ "$db" != _* ]] && [[ "$db" != "sys" ]]; then
        echo "Dumping database: $db";
        mysqldump -u $USER -p$PASSWORD --single-transaction --databases $db | gzip > /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-mysqldump-$db-`date +%Y%m%d`.sql.gz;
    fi
	if [[ "$db" == *"splr_db"* ]]; then
        echo "Dumping Schema Only : $db";
        mysqldump -u $USER -p$PASSWORD --single-transaction --no-data --databases $db | gzip > /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-mysql-Schema-dump-$db-`date +%Y%m%d`.sql.gz;
    fi
    if [[ "$db" == *"open-track-log"* ]]; then
        echo "Dumping Schema Only : $db";
        mysqldump -u $USER -p$PASSWORD --single-transaction --no-data --databases $db | gzip > /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-mysql-Schema-dump-$db-`date +%Y%m%d`.sql.gz;
    fi
done

######### web directory zip 
#echo "web directory zip started"
#pushd /home/admin/web/;
#zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-home-admin-web-`date '+%Y-%m-%d'`.zip .;
#popd;



######### hestia web config directory zip 
echo $(date) : "hestia web config directory zip backup started";
#pushd /home/admin/conf/web/;
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-home-admin-conf-web-`date '+%Y-%m-%d'`.zip /home/admin/conf/web;

###### my.cnf and php.ini exim.conf backup
echo $(date) : "/etc/my.cnf backup started";
cp /etc/my.cnf /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-`date '+%Y-%m-%d'`-my.cnf;
echo $(date) : "/etc/php.ini backup started";
cp /etc/php.ini /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-`date '+%Y-%m-%d'`-php.ini;
echo $(date) : "/etc/exim.conf backup started";
cp /etc/exim/exim.conf /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-`date '+%Y-%m-%d'`-exim.conf;
echo $(date) : "/etc/php-fpm.conf backup started";
cp /etc/php-fpm.conf /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-`date '+%Y-%m-%d'`-php-fpm.conf;
echo $(date) : "/etc/rc.d/rc.local backup started";
cp /etc/rc.d/rc.local /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-rc-d-`date '+%Y-%m-%d'`-rc.local;

##### Mysql Config Directory Backup 
echo $(date) : "/etc/Mysql directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-mysql-`date '+%Y-%m-%d'`.zip /etc/mysql/;
##### Bind Directory Backup 
echo $(date) : "/etc/bind/ directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-bind-`date '+%Y-%m-%d'`.zip /etc/bind/;
##### NGINX Directory Backup 
echo $(date) : "/etc/nginx directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-nginx-`date '+%Y-%m-%d'`.zip /etc/nginx/;
##### PHP-FPM Directory Backup 
echo $(date) : "/etc/php/ directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-php-`date '+%Y-%m-%d'`.zip /etc/php/;
############### sysctl.d backup #########
echo $(date) : "/etc/sysctl.d/ directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-sysctl.d-`date '+%Y-%m-%d'`.zip /etc/sysctl.d/;
############### fail2ban backup #########
echo $(date) : "/etc/fail2ban directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-fail2ban-`date '+%Y-%m-%d'`.zip /etc/fail2ban/; 
############### httpd backup #########
echo $(date) : "/etc/httpd directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-httpd-`date '+%Y-%m-%d'`.zip /etc/httpd/; 
############### amplify-agent backup #########
echo $(date) : "/etc/amplify-agent directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-amplify-agent-`date '+%Y-%m-%d'`.zip /etc/amplify-agent/; 

####### pmta Backup 
echo $(date) : "/etc/pmta directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-etc-pmta-`date '+%Y-%m-%d'`.zip /etc/pmta/;

##### network script directory backup
echo $(date) : "/etc/network/ directory backup started";
zip -r /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-network-scripts-`date '+%Y-%m-%d'`.zip /etc/network/;


######  CRONTAB BACKUP 
echo $(date) : "CRONTAB backup started";
crontab -u admin -l >> /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-crontab-admin-`date '+%Y-%m-%d_%H%M%S'`.txt;
crontab -u root -l >> /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/$SERVERNAME-crontab-root-`date '+%Y-%m-%d_%H%M%S'`.txt;
################# delete  old config files in /backup/daily-backup directory older than 2 days keep last 2 days backup configs

#find /backup/daily-backup -type f -mtime +2 -delete 


########### admin backup #######
echo $(date) : "users backup started";
mkdir -p /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/hestiacp-users-backup;
sudo /usr/local/hestia/bin/v-backup-users;
echo $(date) : "users backup finished";
mv /backup/*.tar /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/hestiacp-users-backup;
echo $(date) : "move user backup tar file to daily backup directory finished";
mv /backup/*.log /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'`/hestiacp-users-backup;
echo $(date) : "move user backup log file to daily backup directory finished";



ssh root@$BACKUPSERVER_1 mkdir -p /home/hestiacp-backup/$SERVERNAME;
echo $(date) : "rsync started with bandwidth limit 20 Megabyte per seconds to $BACKUPSERVER_1";
rsync --bwlimit=20720 --remove-source-files -azv /backup/$SERVERNAME-daily-backup-`date '+%Y-%m-%d'` root@$BACKUPSERVER_1:/home/hestiacp-backup/$SERVERNAME/;
echo $(date) : "delete empty directory after successful transfer in /backup directory";
pushd /backup/;
find . -type d -empty -delete;