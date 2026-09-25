<?php

return [
    'navigation' => [
        'group' => 'SSO Server',
    ],

    'fields' => [
        'created_at' => 'Created at',
    ],

    'clients' => [
        'label' => 'SSO client',
        'plural_label' => 'SSO clients',
        'sections' => [
            'identity' => 'Client identity',
            'endpoints' => 'Integration endpoints',
        ],
        'fields' => [
            'name' => 'Name',
            'client_id' => 'Client ID',
            'redirect_uri' => 'Redirect URI (callback)',
            'redirect_uri_help' => 'Must match the client callback URL exactly.',
            'slo_webhook_url' => 'Single Logout webhook URL',
            'slo_webhook_url_help' => 'Client endpoint notified on Single Logout. Leave empty if the client does not support it.',
            'is_active' => 'Active',
            'is_active_help' => 'Inactive clients are rejected on authorize and token exchange.',
            'active_sessions' => 'Live sessions',
        ],
        'actions' => [
            'rotate_secret' => 'Rotate secret',
            'rotate_secret_description' => 'The current secret stops working immediately. The client app must be updated with the new one.',
            'delete_description' => 'Deleting the client also removes all of its sessions.',
        ],
        'notifications' => [
            'created' => 'SSO client created',
            'rotated' => 'Client secret rotated',
            'secret_body' => 'Client secret: :secret — copy it now to the client app (SSO_CLIENT_SECRET). It will not be shown again.',
        ],
    ],

    'sessions' => [
        'label' => 'SSO session',
        'plural_label' => 'SSO sessions',
        'fields' => [
            'client' => 'Client',
            'user_id' => 'User ID (sub)',
            'token_hash' => 'Token hash',
            'expires_at' => 'Expires at',
        ],
        'filters' => [
            'valid' => 'Valid',
        ],
        'actions' => [
            'revoke' => 'Revoke',
            'revoke_selected' => 'Revoke selected',
            'revoke_description' => 'The access token stops working immediately. The client is not notified.',
            'logout_user' => 'Log out user everywhere',
            'logout_user_description' => 'Revokes every session of this user and sends Single Logout webhooks to the clients.',
        ],
        'notifications' => [
            'logged_out' => 'User logged out from every client',
        ],
    ],

    'widget' => [
        'active_clients' => 'Active clients',
        'active_clients_description' => 'Registered and enabled client apps',
        'live_sessions' => 'Live sessions',
        'live_sessions_description' => 'Unexpired access tokens',
        'connected_users' => 'Connected users',
        'connected_users_description' => 'Distinct users with a live session',
    ],
];
