<?php

return [

    'github_profile' => env('GITHUB_PROFILE'),

    'notification' => [
        'email' => env('NOTIFICATION_EMAIL'),
        'slack_hook' => env('LOG_SLACK_WEBHOOK_URL')
    ],

    'check_sites' => env('CHECK_SITES',''),

];