<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SettingsHub extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'الإعدادات';
    protected static ?string $title = 'الإعدادات العامة';
    protected static ?string $navigationGroup = '⚙️ الإعدادات والربط';
    protected static ?int $navigationSort = 100; // Last item

    protected static string $view = 'filament.pages.settings-hub';
}
