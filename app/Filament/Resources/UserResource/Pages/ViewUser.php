<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('المعلومات الأساسية')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('الاسم')
                            ->icon('heroicon-m-user')
                            ->iconColor('primary'),
                        
                        Infolists\Components\TextEntry::make('email')
                            ->label('البريد الإلكتروني')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),
                        
                        Infolists\Components\TextEntry::make('phone')
                            ->label('رقم الهاتف')
                            ->icon('heroicon-m-phone')
                            ->default('غير محدد'),
                        
                        Infolists\Components\TextEntry::make('address')
                            ->label('العنوان')
                            ->default('غير محدد')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('الأدوار والصلاحيات')
                    ->schema([
                        Infolists\Components\TextEntry::make('roles.name')
                            ->label('الأدوار')
                            ->badge()
                            ->default('لا يوجد'),
                        
                        Infolists\Components\TextEntry::make('permissions_count')
                            ->label('إجمالي الصلاحيات')
                            ->badge()
                            ->color('success')
                            ->getStateUsing(fn ($record) => $record->getAllPermissions()->count()),
                        
                        Infolists\Components\TextEntry::make('direct_permissions')
                            ->label('الصلاحيات المباشرة')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->getStateUsing(fn ($record) => $record->getDirectPermissions()->pluck('name'))
                            ->default('لا يوجد')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('معلومات الحساب')
                    ->schema([
                        Infolists\Components\IconEntry::make('is_active')
                            ->label('الحالة')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        
                        Infolists\Components\TextEntry::make('expiry_status.text')
                            ->label('حالة الاشتراك')
                            ->badge()
                            ->color(fn ($record) => $record->expiry_status['color']),
                        
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('تاريخ الانتهاء')
                            ->dateTime('Y-m-d')
                            ->default('مدى الحياة'),
                        
                        Infolists\Components\TextEntry::make('last_login_at')
                            ->label('آخر تسجيل دخول')
                            ->dateTime('Y-m-d H:i')
                            ->default('لم يسجل دخول بعد'),
                    ])->columns(2),

                Infolists\Components\Section::make('معلومات إضافية')
                    ->schema([
                        Infolists\Components\TextEntry::make('shippingCompany.name')
                            ->label('شركة الشحن')
                            ->default('غير محدد'),
                        
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y-m-d H:i'),
                        
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخر تحديث')
                            ->dateTime('Y-m-d H:i'),
                    ])->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
