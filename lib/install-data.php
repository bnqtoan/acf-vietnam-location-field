<?php global $wpdb;
$locations = file_get_contents(__DIR__.'/don_vi_hanh_chinh.sql');
$locations = str_replace('devvn_', $wpdb->prefix, $locations);
echo $locations;