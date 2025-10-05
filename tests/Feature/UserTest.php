<?php

use function Pest\Livewire\livewire;
use function Pest\Laravel\actingAs;
use App\Models\User;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ViewUser;
use function Pest\Laravel\assertDatabaseHas;
// use Filament\Actions\Testing\TestAction;
// use Filament\Actions\DeleteBulkAction;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $user = User::factory()->create();
    actingAs($user);
});

it('can render the create user page', function () {
    livewire(CreateUser::class)
        ->assertOk();
});

it('can create a user', function () {
    $newUser = User::factory()->create();

    $this->assertDatabaseHas('users', [
        'id' => $newUser->id,
        'email' => $newUser->email,
        'name' => $newUser->name,
        'external_id' => $newUser->external_id,
        'user_name' => $newUser->user_name,
        'phone' => $newUser->phone,
        'company_name' => $newUser->company_name,
        'city' => $newUser->city,
    ]);
});

it('can render the edit user page', function () {
    $user = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

it('can update a user', function () {
    $user = User::factory()->create();

    $newUserData = User::factory()->make();

    livewire(EditUser::class, [
        'record' => $user->id,
    ])
        ->fillForm([
            'name' => $newUserData->name,
            'email' => $newUserData->email,
            'password' => 'password', // Assuming password is required
        ])
        ->call('save')
        ->assertNotified();

    assertDatabaseHas(User::class, [
        'id' => $user->id,
        'name' => $newUserData->name,
    ]);

});

it('can render the view user page', function () {
    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->id,
    ])->assertOk()
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ]);
});

it('can render the user list', function () {
   
    $users = User::factory()->count(3)->create();

    livewire(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords($users);
});

it('can search users by name and email', function () {
    $users = User::factory()->count(3)->create();

    livewire(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords($users)
        ->searchTable($users->first()->name)
        ->assertCanSeeTableRecords($users->take(1))
        ->assertCanNotSeeTableRecords($users->skip(1))
        ->searchTable($users->last()->email)
        ->assertCanSeeTableRecords($users->take(-1))
        ->assertCanNotSeeTableRecords($users->take($users->count() - 1));
});

it('can filter users by `city`', function () {
    $users = User::factory()->count(5)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->filterTable('city', $users->first()->city)
        ->assertCanSeeTableRecords($users->where('city', $users->first()->city))
        ->assertCanNotSeeTableRecords($users->where('city', '!=', $users->first()->city));
});

it('can fetch users', function () {    
    livewire(ListUsers::class)
        ->callAction('FetchUsers')
        ->assertNotified();
});








   
