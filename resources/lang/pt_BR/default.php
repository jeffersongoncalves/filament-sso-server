<?php

return [
    'navigation' => [
        'group' => 'Servidor SSO',
    ],

    'fields' => [
        'created_at' => 'Criado em',
    ],

    'clients' => [
        'label' => 'cliente SSO',
        'plural_label' => 'clientes SSO',
        'sections' => [
            'identity' => 'Identificação do cliente',
            'endpoints' => 'Endpoints de integração',
        ],
        'fields' => [
            'name' => 'Nome',
            'client_id' => 'Client ID',
            'redirect_uri' => 'Redirect URI (callback)',
            'redirect_uri_help' => 'Deve ser exatamente igual à URL de callback do cliente.',
            'slo_webhook_url' => 'URL do webhook de Single Logout',
            'slo_webhook_url_help' => 'Endpoint do cliente notificado no Single Logout. Deixe vazio se o cliente não suportar.',
            'is_active' => 'Ativo',
            'is_active_help' => 'Clientes inativos são rejeitados no authorize e na troca de token.',
            'active_sessions' => 'Sessões ativas',
        ],
        'actions' => [
            'rotate_secret' => 'Rotacionar secret',
            'rotate_secret_description' => 'O secret atual para de funcionar imediatamente. A aplicação cliente precisa ser atualizada com o novo.',
            'delete_description' => 'Excluir o cliente também remove todas as sessões dele.',
        ],
        'notifications' => [
            'created' => 'Cliente SSO criado',
            'rotated' => 'Secret do cliente rotacionado',
            'secret_body' => 'Client secret: :secret — copie agora para a aplicação cliente (SSO_CLIENT_SECRET). Ele não será exibido novamente.',
        ],
    ],

    'sessions' => [
        'label' => 'sessão SSO',
        'plural_label' => 'sessões SSO',
        'fields' => [
            'client' => 'Cliente',
            'user_id' => 'ID do usuário (sub)',
            'token_hash' => 'Hash do token',
            'expires_at' => 'Expira em',
        ],
        'filters' => [
            'valid' => 'Válida',
        ],
        'actions' => [
            'revoke' => 'Revogar',
            'revoke_selected' => 'Revogar selecionadas',
            'revoke_description' => 'O access token para de funcionar imediatamente. O cliente não é notificado.',
            'logout_user' => 'Deslogar usuário de todos os clientes',
            'logout_user_description' => 'Revoga todas as sessões deste usuário e envia os webhooks de Single Logout aos clientes.',
        ],
        'notifications' => [
            'logged_out' => 'Usuário deslogado de todos os clientes',
        ],
    ],

    'widget' => [
        'active_clients' => 'Clientes ativos',
        'active_clients_description' => 'Aplicações cliente cadastradas e habilitadas',
        'live_sessions' => 'Sessões ativas',
        'live_sessions_description' => 'Access tokens não expirados',
        'connected_users' => 'Usuários conectados',
        'connected_users_description' => 'Usuários distintos com sessão ativa',
    ],
];
