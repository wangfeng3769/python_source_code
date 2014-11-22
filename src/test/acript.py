__author__ = 'Administrator'
#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
export PATH

# Check if user is root
if [ $(id -u) != "0" ]; then
printf "Error: You must be root to run this script!\n"
exit 1
fi

if [ "$1" = "start" ]; then
psid=`ps aux|grep "uwsgi"|grep -v "grep"|wc -l`
if [ $psid -gt 2 ];then
echo "uwsgi is running!"
exit 0
else
/usr/sbin/uwsgi -x /home/uwsgi/uwsgi.xml
/usr/localinx/sbininx -s reload
fi
echo "Start uwsgi service [OK]"
elif [ "$1" = "stop" ];then
killall -9 uwsgi
echo "Stop uwsgi service [OK]"
elif [ "$1" = "restart" ];then
killall -9 uwsgi
/usr/sbin/uwsgi -x /home/uwsgi/uwsgi.xml
/usr/localinx/sbininx -s reload
echo "Restart uwsgi service [OK]"
else
echo "Usages: uwsgiserver [start|stop|restart]"
fi
