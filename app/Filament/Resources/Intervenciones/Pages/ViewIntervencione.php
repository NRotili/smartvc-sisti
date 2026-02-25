<?php

namespace App\Filament\Resources\Intervenciones\Pages;

use AlizHarb\ActivityLog\RelationManagers\ActivitiesRelationManager;
use AlizHarb\ActivityLog\Widgets\ActivityChartWidget;
use AlizHarb\ActivityLog\Widgets\LatestActivityWidget;
use App\Filament\Resources\Intervenciones\IntervencioneResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as ComponentsSection;


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
            EditAction::make()
                ->authorize(
                    fn() =>
                    $this->record->canBeEditedBy(auth()->user())
                ),
        ];
    }
}
