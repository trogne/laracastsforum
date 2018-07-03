<?php

namespace App\Http\Controllers;

use App\Reply;
//use App\Favorite;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');    
    }
    
    public function store(Reply $reply)
    {
        ////\DB::table('favorites')->insert([
        //Favorite::create([
        //    'user_id' => auth()->id(),
        //    'favorited_id' => $reply->id,
        //    'favorited_type' => get_class($reply)
        //]);

        //$reply->favorites()->create(['user_id' => auth()->id()]); //because using polymorphic relation, eloquent will automatically set the id and the class for the reply (favorited_id and favorited_type)
        $reply->favorite();
        
        return back();
    }
    
    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }    
}
