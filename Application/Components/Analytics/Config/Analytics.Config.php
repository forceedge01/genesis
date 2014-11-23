<?php

Set::Config('Analytics', array(

    'TrackVisits' => true,
    'TrackUnqiueVisitsOnly' => true,
    'IgnoreIpAddress' => '::1',
    'TrackTableName' => 'Tracks',
    'ResetInterval' => 24*60*60,
    'RecordInsiteTracks' => true,
    'EnableBotTrap' => true
));

//define('ANALYTICS_TRACK_VISITS', true);
//
//define('ANALYTICS_TRACK_UNIQUE_VISITS_ONLY', false);
//
//define('ANALYTICS_IGNORE_IP_ADDRESS', '::1');
//
//define('ANALYTICS_TRACK_TABLE', 'Tracks');
//
//define('ANALYTICS_RESET_INTERVAL', 24*60*60);// Seconds
//
//define('ANALYTICS_RECORD_INSITE_TRACKS', true);
//
//define('ANALYTICS_ENABLE_BOT_TRAP', true);