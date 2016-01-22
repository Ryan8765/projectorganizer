<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{


	/*
	 *
	 *	  Database table used for this model
	 *	
	 */
	
	protected $table = 'projects';


	/*
	 *
	 *	  What fields are mass assignable. 
	 *	
	 */
	

    protected $fillable = [ 'name', 'color', 'completed', 'user_id' ];

    /**********************************************************
		
		This is to create a relationship to the categories table.  Since a task has one category - it "belongs" to a category.

		This method is also used when you're getting the "category" that this task is in.  Watch the end of the video:  https://laracasts.com/series/laravel-5-fundamentals/episodes/14  starting at 11:32.

		Once you grab a task via eloquent  say $task = Tasks::first();  You can then use this method below to get  the associated category for that task by:
		$category = $task->categories.  
	
	**********************************************************/

	public function users() {
		return $this->belongsTo('App\User');
	}

	//project has many tasks.  you can then use this method on the class to get all tasks like so: Project::first()->tasks;
	public function tasks() {
		return $this->hasMany('App\Task');
	}
}
