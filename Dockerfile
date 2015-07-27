FROM debian:8.0
MAINTAINER Shawn Warren <shawn.warren@rackspace.com>

EXPOSE 80

RUN apt-get -qq update
RUN apt-get -qq install locales

RUN sed -e '/en_US.UTF-8/s/^# \+//' -i /etc/locale.gen && locale-gen
ENV LANG en_US.UTF-8

RUN apt-get -qq install apache2
RUN a2enmod rewrite

RUN apt-get -qq install php5 php5-mysql php5-mcrypt php5-sqlite php5-curl
RUN /usr/sbin/php5enmod mcrypt
RUN /usr/sbin/php5enmod curl
RUN sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php5/apache2/php.ini

RUN apt-get -qq install wget
RUN mkdir -p /opt/newrelic
WORKDIR /opt/newrelic
RUN wget -q -r -nd -np -A linux.tar.gz http://download.newrelic.com/php_agent/release/
RUN tar -zxf newrelic-php5-*-linux.tar.gz --strip=1
ENV NR_INSTALL_SILENT true
RUN bash newrelic-install install
WORKDIR /
ADD config/newrelic.ini /etc/php5/apache2/conf.d/newrelic.ini

ADD config/000-default.conf /etc/apache2/sites-available/000-default.conf
ADD . /var/www/

RUN apt-get -qq install curl
RUN curl -sS https://getcomposer.org/installer | php
WORKDIR /var/www/
RUN /composer.phar install
RUN chown -R www-data.www-data /var/www/

ENTRYPOINT [ "/usr/sbin/apache2ctl", "-D", "FOREGROUND" ]
CMD [ "-k", "start" ]
