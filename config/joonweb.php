<?php

return [
    'api_key' => getenv('JOONWEB_CLIENT_ID') ?: '',
    'api_secret' => getenv('JOONWEB_CLIENT_SECRET') ?: '',
    'api_version' => getenv('JOONWEB_API_VERSION') ?: '26.0',
    'app_name' => getenv('JOONWEB_APP_NAME') ?: 'My Joonweb App',
    'app_url' => getenv('APP_URL') ?: 'https://yourapp.com'
];
