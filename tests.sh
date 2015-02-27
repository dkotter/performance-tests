#!/bin/sh
cd /tmp/wordpress
chmod a+x tests/post-not-in-tests.php

php tests/post-not-in-tests.php