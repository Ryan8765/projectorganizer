<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {

	/*
	 *
	 *	  	Task Routes
	 *	
	 */
	
    Route::auth();
    Route::get('/tasks', 'TasksController@index');
    // shows all rasks for a given project
    Route::get('/tasks/{project_id}', 'TasksController@show');

    //adding a task 
    Route::post('/tasks/{project_id}', 'TasksController@store');
    //delete a task
    Route::delete('/tasks/{task_id}', 'TasksController@destroy');
    //editing a task
    Route::patch('/tasks/{task_id}', 'TasksController@update');
    // updates filters for tasks 
    Route::post('/updatefilters', 'TasksController@updatefilters');
    //updates completed/not completed functionality
    Route::post('/completed', 'TasksController@completed');




    /*
     *
     *	  	Project routes
     *	
     */
    
    //showing the form to edit a project record
    Route::post('/project/{id}/edit', 'ProjectsController@edit');
    //handle the edit project record
    Route::post('/project/{id}', 'ProjectsController@update');
    Route::post('/destroyproject/{id}', 'ProjectsController@destroyproject');

    //sort update for projects (sort order for them)
    Route::post('/sortupdate', 'ProjectsController@sortupdate');

    //change completed/not completed for projects
    Route::post('projectcomplete', 'ProjectsController@projectcomplete');

    Route::get('/home', 'HomeController@index');
    //logging the projects controller resource (not a full resource)
    Route::post('/project', 'ProjectsController@store');
});
