<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Watcher;
use Auth;
use Session;

class WatchersController extends Controller
{
    public function watch($id)
    {
    	Watcher::create([
    		'discussion_id' => $id,
    		'user_id' => Auth::id()
    	]);

    	Session::flash('success', 'Estas siguiendo este tema');

    	return redirect()->back();
    }

    public function unwatch($id)
    {
    	$watcher = Watcher::where('discussion_id', $id)->where('user_id', Auth::id());

    	$watcher->delete();
// dd($watcher);
    	Session::flash('success', 'Ya no sigues este tema');

    	return redirect()->back();
    }
}
