<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Reply extends Model
{
    protected $fillable = ['content', 'user_id', 'discussion_id'];

    public function discussion()
    {
    	return $this->belongsTo('App\Discussion');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function like()
    {
    	return $this->hasMany('App\Like');
    }

    public function is_liked_by_auth_user()
    {
    	$id = Auth::id();

    	$likers = array();

    	// dd($this->like);

    	foreach ($this->like as $like) {
    		array_push($likers, $like->user_id);
    	}
    	// echo "id=$id<br>";
// dd($likers);
    	if (in_array($id, $likers)){
    		return true;
    	} else {
    		return false;
    	}
    }
}
