<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('تعديل'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات الدور')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('اسم الدور')
                            ->icon('heroicon-m-shield-check')
                            ->iconColor('primary'),
                        
                        Infolists\Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->default('لا يوجد وصف'),
                        
                        Infolists\Components\TextEntry::make('users_count')
                            ->label('عدد المستخدمين')
                            ->badge()
                            ->getStateUsing(fn ($record) => $record->users()->count()),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i'),
                    ])->columns(2),
                
                Infolists\Components\Section::make('الصلاحيات')
                    ->schema([
                        Infolists\Components\TextEntry::make('permissions')
                            ->label('')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->getStateUsing(function ($record) {
                                return $record->permissions->pluck('name')->map(function ($permission) {
                                    return RoleResource::formatPermissionLabel($permission);
                                })->values();
                            }),
                    ]),
            ]);
    }
}
