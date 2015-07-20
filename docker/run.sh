#!/bin/bash

/usr/sbin/rsyslogd &
/usr/sbin/apache2ctl -D FOREGROUND -k start
