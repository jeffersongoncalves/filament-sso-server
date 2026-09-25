<?php

namespace JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use JeffersonGoncalves\Filament\SsoServer\Resources\SsoClientResource;

class EditSsoClient extends EditRecord
{
    protected static string $resource = SsoClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SsoClientResource::configureRotateSecretAction(Action::make('rotateSecret')),
            DeleteAction::make(),
        ];
    }
}
