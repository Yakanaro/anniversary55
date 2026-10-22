<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Celebration
    |--------------------------------------------------------------------------
    |
    | When the celebration starts. The countdown on the invitation counts down
    | to this moment in the given timezone (Petrozavodsk is on Moscow time).
    |
    */

    'starts_at' => env('INVITATION_STARTS_AT', '2026-12-04 16:30'),

    'timezone' => env('INVITATION_TIMEZONE', 'Europe/Moscow'),

    /*
    |--------------------------------------------------------------------------
    | Venue
    |--------------------------------------------------------------------------
    */

    'venue' => [
        'address' => 'пер. Репникова, д. 10',
        'district' => 'Перевалка',
        'city' => 'Петрозаводск',
        'search_query' => 'Петрозаводск, переулок Репникова, 10',
        'longitude' => 34.329906,
        'latitude' => 61.779535,
    ],

    /*
    |--------------------------------------------------------------------------
    | Guest Chat
    |--------------------------------------------------------------------------
    |
    | Invite link to the group chat guests join (Telegram, WhatsApp, etc.).
    | Leave it empty to hide the chat block on the invitation.
    |
    */

    'chat_url' => env('INVITATION_CHAT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Guest List
    |--------------------------------------------------------------------------
    |
    | The password the host enters at /host/login to see who is coming.
    | Leave it empty to keep the guest list closed.
    |
    */

    'host_password' => env('INVITATION_HOST_PASSWORD'),

];
