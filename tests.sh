#!/bin/sh
cd /tmp/wordpress

wp-cli/bin/wp eval-file tests/kses.php
wp-cli/bin/wp eval-file tests/post-not-in.php