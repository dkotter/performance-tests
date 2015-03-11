#!/bin/sh
cd /tmp/wordpress

wp-cli/bin/wp eval-file tests/post-not-in.php