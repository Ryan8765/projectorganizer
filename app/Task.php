<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /*
	 *
	 *	  Database table used for this model
	 *	
	 */
	
	protected $table = 'tasks';


	/*
	 *
	 *	  What fields are mass assignable. 
	 *	
	 */
	

    protected $fillable = [ 'description', 'due_date', 'priority', 'completed', 'order', 'user_id', 'project_id' ];

    /**********************************************************
		
		This is to create a relationship to the categories table.  Since a task has one category - it "belongs" to a category.

		This method is also used when you're getting the "category" that this task is in.  Watch the end of the video:  https://laracasts.com/series/laravel-5-fundamentals/episodes/14  starting at 11:32.

		Once you grab a task via eloquent  say $task = Tasks::first();  You can then use this method below to get  the associated category for that task by:
		$category = $task->categories.  
	
	**********************************************************/

	public function project() {
		return $this->belongsTo('App\Project');
	}
}
