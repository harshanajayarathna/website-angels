<?php

use function Pest\Livewire\livewire;
use function Pest\Laravel\actingAs;
use App\Models\User;
use App\Models\Post;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ViewPost;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use Filament\Actions\Testing\TestAction;

beforeEach(function () {
    $user = User::factory()->create();    
    actingAs($user);
});

it('can render the create post page', function () {
    livewire(CreatePost::class)
        ->assertOk();
});

it('can user create a post', function () {

    $user = User::factory()->create();  

    $newPost = Post::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('posts', [
            'id' => $newPost->id,
            'title' => $newPost->title,
            'content' => $newPost->content,
            'user_id' => $user->id,
            'external_id' => $newPost->external_id,
        ]);    
});

it('can render the edit post page', function () {
    $post = Post::factory()->create();

    livewire(EditPost::class, [
        'record' => $post->id,
    ])
        ->assertOk()
        ->assertSchemaStateSet([
            'title' => $post->title,
            'content' => $post->content,
        ]);
});

it('can update a post', function () {
    $post = Post::factory()->create();

    $newPostData = Post::factory()->make();

    livewire(EditPost::class, [
        'record' => $post->id,
    ])
        ->fillForm([
            'title' => $newPostData->title,
            'content' => $newPostData->content,
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'title' => $newPostData->title,
    ]);

});

it('can render the view post page', function () {
    $post = Post::factory()->create();

    livewire(ViewPost::class, [
        'record' => $post->id,
    ])->assertOk()
        ->assertSchemaStateSet([
            'title' => $post->title,
            'content' => $post->content,
        ]);
});

it('can render the post list', function () {
   
    $posts = Post::factory()->count(3)->create();

    livewire(ListPosts::class)
        ->assertOk()
        ->assertCanSeeTableRecords($posts);
});

it('can search posts by title', function () {
    $posts = Post::factory()->count(3)->create();

    livewire(ListPosts::class)
        ->assertOk()
        ->assertCanSeeTableRecords($posts)
        ->searchTable($posts->first()->title)
        ->assertCanSeeTableRecords($posts->take(1))
        ->assertCanNotSeeTableRecords($posts->skip(1));
});

it('can filter posts by `user`', function () {
    $user = User::factory()->create();
    $posts = Post::factory()->count(5)->create(['user_id' => $user->id]);

    livewire(ListPosts::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('user_id', $user->id)
        ->assertCanSeeTableRecords($posts->where('user_id', $user->id))
        ->assertCanNotSeeTableRecords($posts->where('user_id', '!=', $user->id));
});

it('can sort posts by title, user and created at', function () {
    Post::factory()->count(10)->create();

    $sortedPostsByTitleAsc = Post::query()->orderBy('title')->get();
    $sortedPostsByTitleDesc = Post::query()->orderBy('title', 'desc')->get();

    $sortedPostsByUserAsc = Post::query()
                                ->join('users', 'posts.user_id', '=', 'users.id')
                                ->orderBy('users.name', 'asc')
                                ->select('posts.*')
                                ->get();
    $sortedPostsByUserDesc = Post::query()
                                ->join('users', 'posts.user_id', '=', 'users.id')
                                ->orderBy('users.name', 'desc')
                                ->select('posts.*')
                                ->get();
    
    $sortedPostsByCreatedAtAsc = Post::query()->orderBy('created_at')->get();
    $sortedPostsByCreatedAtDesc = Post::query()->orderBy('created_at', 'desc')->get();

    livewire(ListPosts::class)
        ->sortTable('title')
        ->assertCanSeeTableRecords($sortedPostsByTitleAsc, inOrder: true)
        ->sortTable('title', 'desc')
        ->assertCanSeeTableRecords($sortedPostsByTitleDesc, inOrder: true)
        ->sortTable('user.name')
        ->assertCanSeeTableRecords($sortedPostsByUserAsc, inOrder: true)
        ->sortTable('user.name', 'desc')
        ->assertCanSeeTableRecords($sortedPostsByUserDesc, inOrder: true)
        ->sortTable('created_at')
        ->assertCanSeeTableRecords($sortedPostsByCreatedAtAsc, inOrder: true)
        ->sortTable('created_at', 'desc')
        ->assertCanSeeTableRecords($sortedPostsByCreatedAtDesc, inOrder: true)
        ;
});

it('can fetch posts', function () {    
    livewire(ListPosts::class)
        ->callAction('FetchPosts')
        ->assertNotified();
});




   
