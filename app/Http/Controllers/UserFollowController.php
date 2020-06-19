<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserFollowController extends Controller
{
    public function store($id)
    {
        \Auth::user()->follow($id);
        
        return back();
    }
    
    public function destroy($id)
    {
        \Auth::user()->destroy($id);
        
        return back();
    }
    
     public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers']);
    }
}
