<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow' , 'user_id' , 'follow_id')->withTimestamps();
    }
    
    public function follower()
    {
        return $this->belongsToMany(User::class, 'user_follow' , 'follow_id' , 'user_id')->withTimestamps();
    }
    
     public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id' , 'micropost_id' )->withTimestamps();
    }

    
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'follower' , 'favorites']);
    }
    
     public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;

        if ($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    public function favorite($micropost)
    {
        $exist = $this->is_favoriting($micropost);
        $its_me = $this->id == $micropost;
        
        //dd($exist);
        if($exist || !$its_me){
            return false;
        }else{
            $this->favorites()->attach($micropost);
            return true;
        }
        
    }
    
    public function unfavorite($micropost)
    {
        $exist = $this->is_favoriting($micropost);
        $its_me = $this->id == $micropost;
        if($exist && $its_me){
            $this->favorites()->detach($micropost);
            return true;
        }else{
            return false;
        }
    }
    
    public function is_favoriting($micropost)
    {
        return  $this->favorites()->where('micropost_id' , $micropost)->exists();
    }
   
}
