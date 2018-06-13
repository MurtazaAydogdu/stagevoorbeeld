#!/bin/sh

rm -rf /var/run/apache2/apache2.pid

echo /usr/sbin/apache2ctl -D FOREGROUND
/usr/sbin/apache2ctl -D FOREGROUND