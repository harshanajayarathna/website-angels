<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use App\Models\Post;
use App\Models\User;
use Filament\Notifications\Notification;
use App\Services\APIService;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Post')
                ->icon('heroicon-o-plus'),
            
            Action::make('FetchPosts')
                    ->label('Fetch Posts')
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
            
            $posts = $apiService->fetch('posts');

            foreach ($posts as $postData) {
                                
                $externalId = $postData['id'];     

                $user = User::where('external_id', $postData['userId'])->first();

                Post::updateOrCreate(
                    ['external_id' => $externalId],
                    [
                        'title' => $postData['title'],
                        'content' => $postData['body'],
                        'user_id' => $user->id, 
                    ]
                );
            }
            // Success notification
            Notification::make()
                ->title('Posts fetched successfully!')
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Error notification    
            Notification::make()
                ->title('Failed to fetch posts.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
