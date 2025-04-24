FROM webdevops/php-apache:8.2
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update
RUN apt install -y nodejs
RUN apt-get install -y autoconf pkg-config libssl-dev
RUN docker-php-ext-install bcmath
RUN echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/mongodb.ini
RUN apt-get install -y \
        git libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev
RUN apt-get update -qq ; apt-get upgrade ; \
    apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
WORKDIR /var/www/html
COPY . /var/www/
RUN mv /var/www/nextprocurement/* /var/www/html
RUN rm /var/www/html/index.html
RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf
EXPOSE 80 443


