<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    protected $fillable = ['content', 'user_id'];

    public function user()
    {
	return $this->belongsTo(User::class);
    }

/**	
	// add for kadai
	public function favoritings()
	{
		return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
	}
	public function favorite($mId) {
	
	$exist = $this->is_favoriting($mId);

	$its_me = $this->id == $mId;

		if ($exist || $its_me) {

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

	$its_me = $this->id == $mId;

		if ($exist && !$its_me) {

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
*/ 
}
