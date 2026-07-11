<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use AlizHarb\ActivityLog\RelationManagers\ActivitiesRelationManager;
use AlizHarb\ActivityLog\Widgets\LatestActivityWidget;
use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIntervencione extends ViewRecord
{
    protected static string $resource = IntervencioneResource::class;

    protected static ?string $title = 'Detalle de la intervención';

    public function getWidgets(): array
    {
        return [
            LatestActivityWidget::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            ActivitiesRelationManager::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('ampliar')
                ->label('Ampliar')
                ->icon('heroicon-o-plus-circle')
                ->color('warning')
                ->url(fn () => IntervencioneResource::getUrl('create', [
                    'padre' => $this->record->intervencion_padre_id ?? $this->record->id,
                ]))
                ->visible(fn () => IntervencioneResource::canCreate()),
            EditAction::make()
                ->authorize(
                    fn () => $this->record->canBeEditedBy(auth()->user())
                ),
        ];
    }
}
