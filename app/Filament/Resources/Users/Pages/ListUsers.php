<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\User;
use App\Services\APIService;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create User')
                ->icon('heroicon-o-plus'),

            Action::make('FetchUsers')
                ->label('Fetch Users')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {                    
                    $this->fetchDataFromApi();
                })
                ->color('info'),                                        
        ];
    }

    protected function fetchDataFromApi(): void
    {       
        try {
            $apiService = new APIService();
            $users = $apiService->fetch('users');

            foreach ($users as $userData) {
                // when record exists, update it
                // when record not exists, create it
                User::updateOrCreate(
                    ['external_id' => $userData['id']], 
                    [
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'user_name' => $userData['username'],
                        'phone' => $userData['phone'],
                        'company_name' => $userData['company']['name'] ?? null,
                        'city' => $userData['address']['city'] ?? null,
                        'password' => bcrypt('password'), // Set a default 'password' 
                    ]
                );
            }
            // Success notification
            Notification::make()
                ->title('Users fetched successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Error notification
            Notification::make()
                ->title('Failed to fetch users.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
