
<?php

return [

'paths' => ['api/*', 'sanctum/csrf-cookie'], 

'allowed_methods' => ['*'], 

'allowed_origins' => ['http://localhost:4200', 'https://your-frontend-domain.com'], 

'allowed_origins_patterns' => [],

'allowed_headers' => ['*'], 

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => false, 
];
