<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Rules\SpamFree;
use App\Exceptions\ThrottleException;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return true;
        return Gate::allows('create', new \App\Reply);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => ['required', new SpamFree]
        ];
    }
    
    //overwrite from FormRequest, sinon throw new AuthorizationException('This action is unauthorized');  = status code  403 
    protected function failedAuthorization()
    {
        throw new ThrottleException( //custom exception
            'You are replying too frequently. Please take a break.'
        ); 
    }
    
    //public function persist($thread) //responsible for persisting your form
    //{
    //    return $thread->addReply([
    //        'body' => request('body'),
    //        'user_id' => auth()->id()
    //    ])->load('owner');         
    //}
}
