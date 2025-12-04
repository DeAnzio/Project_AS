<?php
// Auth0 configuration (falls back to env variables)
return [
    'domain' => getenv('AUTH0_DOMAIN') ?: 'dev-to07hfodsfp24ms2.us.auth0.com',
    'client_id' => getenv('AUTH0_CLIENT_ID') ?: 'LHF5CgzgvdK2LrCmvtuloII8ROnjVj4Y',
    'client_secret' => getenv('AUTH0_CLIENT_SECRET') ?: 'elAXlsAitkRKM5465pzM6f4Opl555-5h9B0fuOVaQJXKkyRBle9VUwepvPKzQRtW',
    'redirect_uri' => getenv('AUTH0_REDIRECT_URI') ?: 'http://localhost:8089/auth/auth0_callback.php',
    'audience' => getenv('AUTH0_AUDIENCE') ?: ''
];

?>
