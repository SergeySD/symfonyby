#!/usr/bin/env php
<?php

$dir = opendir(__DIR__);

while ($file = readdir($dir)) {
    if (is_dir($file . '/.git')) {
        exec("cd $file && git pull origin master");
    }
}

closedir($dir);
