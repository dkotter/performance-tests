#!/bin/sh

# WordPress test setup script for Travis CI 
#
# Author: Benjamin J. Balter ( ben@balter.com | ben.balter.com )
# License: GPL3

mkdir -p tmp

export WP_CORE_DIR=/tmp/wordpress

# Init database
mysql -e 'CREATE DATABASE wordpress_test;' -uroot

# Grab specified version of WordPress from github
wget -nv -O tmp/wordpress.tar.gz https://github.com/WordPress/WordPress/tarball/$WP_VERSION
mkdir -p $WP_CORE_DIR
tar --strip-components=1 -zxmf tmp/wordpress.tar.gz -C $WP_CORE_DIR

tests=$(basename $(pwd))/tests
mv tests $WP_CORE_DIR

composer create-project wp-cli/wp-cli $WP_CORE_DIR/wp-cli --no-dev

cd $WP_CORE_DIR

wp-cli/bin/wp core config --dbname=wordpress_test --dbuser=root --dbhost=localhost
wp-cli/bin/wp core install --url=http://example.com --title="Just another test site" --admin_user=wordpress --admin_password=password --admin_email=test@example.com
curl http://loripsum.net/api/5 | wp-cli/bin/wp post generate --post_content --count=30

cd ~/build/dkotter/performance-tests
chmod a+x tests.sh
