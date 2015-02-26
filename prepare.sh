#!/bin/bash

# called by Travis CI

export WP_CORE_DIR=/tmp/wordpress
export WP_TESTS_DIR=/tmp/wordpress-tests

# Init database
mysql -e 'CREATE DATABASE wordpress_test;' -uroot

# Grab specified version of WordPress from github
wget -nv -O /tmp/wordpress.tar.gz https://github.com/WordPress/WordPress/tarball/$WP_VERSION
mkdir -p $WP_CORE_DIR
tar --strip-components=1 -zxmf /tmp/wordpress.tar.gz -C $WP_CORE_DIR

# Grab testing framework and config file
svn co --quiet --ignore-externals http://unit-tests.svn.wordpress.org/trunk/ $WP_TESTS_DIR

wget -nv -O $WP_TESTS_DIR/wp-tests-config.php https://raw.github.com/tierra/wordpress-plugin-tests/setup/wp-tests-config.php