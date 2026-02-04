<?php
// php-app/config/routes.php
return [
    'GET' => [
        '/' => 'AuthController@redirectToTickets',
        '/login' => 'AuthController@showLogin',
        '/register' => 'AuthController@showRegister',
        '/logout' => 'AuthController@logout',
        '/tickets' => 'TicketController@index',
        '/tickets/new' => 'TicketController@createForm',
    ],
    'POST' => [
        '/login' => 'AuthController@login',
        '/register' => 'AuthController@register',
        '/tickets' => 'TicketController@create',
    ],
];
