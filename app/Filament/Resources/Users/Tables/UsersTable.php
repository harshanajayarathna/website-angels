<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use App\Models\User;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $cities = User::distinct()->pluck('city')->filter()->sort()->toArray();
        
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('user_name')
                    ->label('User Name'),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),                
                TextColumn::make('company_name')
                    ->label('Company Name')
                    ->searchable(),
                TextColumn::make('city')
                    ->label('City')
                    ->searchable(),
                TextColumn::make('posts_count')
                    ->label('Posts Count')
                    ->counts('posts'),                    
            ])
            ->filters([
                SelectFilter::make('city')
                    ->label('City')
                    ->options(array_combine($cities, $cities)) 
                    ->default(null)
                    ->preload()
                    ->multiple()
                    ->placeholder('All Cities')
                    ->indicator('Filtered by City'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
