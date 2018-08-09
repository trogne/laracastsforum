<?php

namespace App\Http\Controllers\Api;

//use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
        $search = request('name');
        
        //return User::havingName //custom scope
        //return User::searchByName
        //return User::take(5)->pluck('name');
        return $search ? User::where('name', 'LIKE', "$search%")
            ->take(5)
            ->pluck('name') : [];
    }            
}
