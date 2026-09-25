## Filament SSO Server

Filament UI for `jeffersongoncalves/laravel-sso-server`: manage SSO client apps, rotate their secrets and revoke active sessions from the admin panel.

### Installation

@verbatim
<code-snippet name="Install the plugin" lang="bash">
composer require jeffersongoncalves/filament-sso-server
</code-snippet>
@endverbatim

Requires `jeffersongoncalves/laravel-sso-server` to already be installed, migrated and keyed (`php artisan sso-server:keys`) — it owns the `sso_clients` / `sso_active_sessions` tables, routes and token logic.

### Registering in the Panel

@verbatim
<code-snippet name="Register in PanelProvider" lang="php">
use JeffersonGoncalves\Filament\SsoServer\SsoServerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            SsoServerPlugin::make()
                ->navigationGroup('Authentication'),
        ]);
}
</code-snippet>
@endverbatim

### What it ships

- **SsoClientResource** — CRUD for `SsoClient`. `client_id` (UUID) and `client_secret` (64 chars) are generated server-side on create; the plain secret is shown once in a persistent notification. A **Rotate secret** action replaces it.
- **SsoActiveSessionResource** — list-only view of `SsoActiveSession` rows (they are written by the token exchange). **Revoke** deletes one row; **Log out user everywhere** calls `SsoServer::logoutUser()`, which revokes every session of the user and dispatches Single Logout webhooks.
- **SsoOverviewWidget** — active clients, live sessions and distinct connected users.

### Best Practices

- Never read or display `client_secret` outside the create/rotate notifications — it is encrypted at rest and hidden from serialization.
- Prefer **Log out user everywhere** over bulk-deleting sessions when you need client apps to drop their local sessions too: plain deletes send no webhook.
- Toggle pieces off with `->clientResource(false)`, `->sessionResource(false)` or `->overviewWidget(false)`.
