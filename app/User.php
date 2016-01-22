<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'activated', 'activated_token', 'sort',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activated_token',
    ];


        /**********************************************************
        
        This creates a relationship to the tasks table.  Since categories can have many tasks.  YOu don't have to name the method "Tasks", but it tends to be easier to keep track of this way.  NOte that in "hasMany" - "Tasks" is the name of the class, not the file.  (check tasks model). 

        This method is also used when you're getting all of the "tasks" in a particular category.  Watch the end of the video:  https://laracasts.com/series/laravel-5-fundamentals/episodes/14  starting at 11:32.

        Once you grab a category via eloquent  say $category = Categories::first();  You can then use this method below to get all of the associated tasks for that category by:
        $allCategoryTasks = $category->tasks.  You can also string where clauses on if you use the method version $allCategoryTasks = $category->tasks()->where( blah blah blah).
        Note that the method below "tasks" is the method used for $category->tasks.
    
    **********************************************************/
    
    public function projects() {
        return $this->hasMany('App\Project');
    }
}
