## wangfeng, 2007.08.20


PERL="/usr/bin/perl"
NGINX_CGI_FILE="/usr/local/nagios/bin/perl-cgi.pl"


#bg_num=`jobs -l |grep "NGINX_CGI_FILE"`
#PID=`ps aux|grep "perl-cgi"|cut -c10-14|xargs kill -9`
PID=`ps aux|grep 'perl-cgi'|cut -c10-14|sed -n "1P"`
echo $PID
sockfiles="/var/run/nagios.sock"
kill -9 $PID

$PERL $NGINX_CGI_FILE &

sleep 3
chmod  777   $sockfiles
#`chown www-data.www-data $sockfiles`

# EOF: start_nginx_cgi.sh
