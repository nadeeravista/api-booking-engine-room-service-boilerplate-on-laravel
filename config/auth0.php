<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Auth0 Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Auth0 JWT validation
    |
    */

  'enabled' => env('AUTH0_ENABLED', false),
  'domain' => env('AUTH0_DOMAIN'),
  'audience' => env('AUTH0_AUDIENCE'),
  'algorithm' => env('AUTH0_ALGORITHM', 'RS256'),
  'client_id' => env('AUTH0_CLIENT_ID'),
  'client_secret' => env('AUTH0_CLIENT_SECRET'),
];
