<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function store($id)
    {
        \Auth::micropost()->favorite($micropost_id);
        
        return back();
    }
    
    public function destroy($id)
    {
        \Auth::micropost()->unfavorite($micropost_id);
        
        return back();
    }
}
