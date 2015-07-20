FROM ubuntu:latest
MAINTAINER Shawn Warren <shawn.warren@rackspace.com>

# Apache
RUN apt-get -yqq install apache2
RUN a2enmod rewrite

# PHP
RUN apt-get -qq update
RUN apt-get -yqq install php5 php5-mysql php5-mcrypt php5-sqlite php5-curl
RUN /usr/sbin/php5enmod mcrypt
RUN /usr/sbin/php5enmod curl

# Install rsyslog for Logentries
RUN apt-get -yqq install rsyslog
ADD ./z99-logentries.conf /etc/rsyslog.d/z99-logentries.conf

# Configure CAAS domain in Apache
ADD ./caas.conf /etc/apache2/sites-enabled/caas.conf
ADD ./caas-web.tar.gz /var/www/

# Services script
ADD run.sh /usr/local/bin/run

EXPOSE 80
CMD [ "/usr/local/bin/run" ]
