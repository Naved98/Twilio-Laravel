<?php

return [
    'paths' => ['api/*',], // Specify API paths to apply CORS
    'supports_credentials' => false, // Allow credentials like cookies
    'allowed_origins' => ['*'], // Allow all origins (in production, restrict this to specific origins)
    'allowed_methods' => ['*'], // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)
    'allowed_headers' => ['*'], // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    
];
