<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    function only_members_can_add_avatars()
    {
        $this->withExceptionHandling();

        $this->PostJson("api/users/1/avatar")
        //$this->json('POST', "api/users/1/avatar")
            ->assertStatus(401);
    }
    
    /** @test */
    function a_valid_avatar_must_be_provided()
    {
        $this->withExceptionHandling()->signIn();
        
        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])->assertStatus(422);
    }
    
    /** @test */
    function a_user_may_add_an_avatar_to_their_profile()
    { 
        $this->signIn();
        
        Storage::fake('public'); //fake the public disk
        
        $this->json('POST', 'api/users/'. auth()->id() . '/avatar', [
            //'avatar' => UploadedFile::fake()->image('avatar.jpg')
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg') //// dd($file); //Illuminate\Http\Testing\File
        ]);

        $this->assertEquals(asset('avatars/'.$file->hashName()), auth()->user()->avatar_path);
        
        //Storage::disk('publ ic')->assertExists('avatars/avatar.jpg');
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
} 
