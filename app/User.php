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
}
