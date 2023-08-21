<?php

namespace App\Filament\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Page as BasePage;
use Filament\Panel;

class Analytics extends BasePage
{
    protected static ?string $slug = '/analytics';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Analytics';
    protected static string $view = 'filament.pages.analytics';
    protected static string $resource = UserResource::class;
    protected ?string $heading = 'Dashboard - Analytics';
    protected ?string $subheading = '';

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return ['Dashboard', 'Analytics'];
    }
}
