<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

	//add
	public function microposts()
	{
		return $this->hasMany(Micropost::class);
	}
	// add 2018.03.13
	public function followings()
	{
		return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
	}
	public function followers()
	{
		return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
	}
	// add 2018.03.13
	public function follow($userId) {
	
	$exist = $this->is_following($userId);

	$its_me = $this->id == $userId;

		if ($exist || $its_me) {

			//既にフォローしていば何もしない

			return false;

		} else {

			// 未フォローであればフォローする

			$this->followings()->attach($userId);

			return true;
		}
	}
	public function unfollow($userId) {
	
	$exist = $this->is_following($userId);

	$its_me = $this->id == $userId;

		if ($exist && !$its_me) {

			//既にフォローしていばフォローを外す
			$this->followings()->detach($userId);
			return true;
		} else {

			// 未フォローであれば何もしない
			return false;
		}
	}

	public function is_following($userId) {
		return $this->followings()->where('follow_id',$userId)->exists();
	}

	public function feed_microposts() {
			$follow_user_ids = $this->followings()->lists('users.id')->toArray();
			$follow_user_ids[] = $this->id;
			return Micropost::whereIn('user_id', $follow_user_ids);
	}
	// add for kadai
	public function favo_microposts() {
			$favorite_ids = $this->favoritings()->lists('users.id')->toArray();
			//$favorite_ids[] = $this->id;
			return Micropost::whereIn('micropost_id', $favorite_ids);
	}
	public function favoritings()
	{
		return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
	}
	public function favorite($mId) {
	
	$exist = $this->is_favoriting($mId);

	//$its_me = $this->id == $mId;

	//	if ($exist || $its_me) {
		if ($exist) {

			//既にお気に入りにしていれば何もしない

			return false;

		} else {

			// 未お気に入りであればお気に入りする

			$this->favoritings()->attach($mId);

			return true;
		}
	}
	public function unfavorite($mId) {
	
	$exist = $this->is_favoriting($mId);

	//$its_me = $this->id == $mId;

	//	if ($exist && !$its_me) {
		if ($exist) {

			//既にお気に入りにしていればフォローを外す
			$this->favoritings()->detach($mId);
			return true;

		} else {

			// 未お気に入りであれば何もしない
			return false;
		}
	}
	public function is_favoriting($mId) {
		return $this->favoritings()->where('micropost_id',$mId)->exists();
	}
			
}
