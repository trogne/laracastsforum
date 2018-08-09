<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\PleaseConfirmYourEmail;
use App\User;
//use Session;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake(); //fake the Mailable... fake method is going to swap the underlying class in the laravel container with this new MailFake  -  public static fake() {static::swap(new MailFake)} // MailFake send, not fire off email, instead push that mailable to an array , $this->mailables[] = $view
        
        //event(new Registered(create('App\User')));
        //$this->post('/register', [
        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);
        
        //Mail::assertSent(PleaseConfirmYourEmail::class, function ($mailable) {
        //    //dd($mailable->to[0]['name']);
        //});
        //Mail::assertSent(PleaseConfirmYourEmail::class);
        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }
    
    /** @test */
    function user_can_fully_confirm_their_email_addresses()
    {
        Mail::fake();
        
        //Session::start();
        
        //$this->withExceptionHandling();
        
        //$this->post('/register', [
        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar',
            //'_token' => Session::token() // ou Session::get('_token') //why he does not need that?
        ]);
        
        $user = User::whereName('John')->first();
        
        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);
        
        //$response = $this->get('/register/confirm?token=' . $user->confirmation_token);
        //$response = $this->get(route('register.confirm', ['token' => $user->confirmation_token]));
                
        //$response->assertRedirect('/threads');
        //$response->assertRedirect(route('threads'));

        $this->get(route('register.confirm', ['token' => $user->confirmation_token]))
            ->assertRedirect(route('threads'));

        tap($user->fresh(), function ($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }
    
    /** @test */
    function confirming_an_invalid_token()
    {
        $this->withExceptionHandling();
        
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'Unknown token.');
    }
}
