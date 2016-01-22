<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Project;
use App\User;
use App\Task;
//used to return response through JSON.
use Response;

class TasksController extends Controller
{

	//register middleware for this whole controller - register auth middleware to stop users from entering site. 
	public function __construct() {
		$this->middleware('auth');
	}


    public function index(Request $request) {

    	//data array to pass to the view. 
    	$data = array();

    	//current user
    	$user_id = auth()->user()->id;




    	//set global sort session variables - keeps track of how user wants their projects sorted and filtered.
    	if(  !$request->session()->has('display_tasks') ) {
    		$user = User::where('id', $user_id)->get();
    		$request->session()->put('display_tasks', $user[0]['display_tasks']);
    		$request->session()->put('task_order', $user[0]['task_order']);
    	}

    	//get the session task_order data.
    	$data['session'] = $request->session()->get('task_order');
    	
    	$projects = Project::where('user_id', $user_id)->orderBy('completed', 'asc')->orderBy('order', 'asc')->get();
    	$data['projects'] = $projects; 
    	$data['display_tasks'] = $request->session()->get('display_tasks');
    	$data['task_order'] = $request->session()->get('task_order');
    	//get all tasks filtered and ordered as user has selectged.
    	$tasks = filteredTasksToDisplayNoProject_H( $request->session()->get('display_tasks'), $request );
    	$data['tasks'] = $tasks;
    	$data['is_project_page'] = "false";



    	return view('tasks')->with('data', $data);
    }


    /*
     *
     *	  Function to show all tasks for a given project. 
     *	
     */
    

    public function show( $project_id, Request $request ) {

    	//if not a valid project number return to home view
    	if( !is_numeric( $project_id ) ) {
    		return redirect()->action('TasksController@index');
    	}

    	//data array to pass to the view. 
    	$data = array();

    	//current user
    	$user_id = auth()->user()->id;

    	//current viewed project
    	$current_project = Project::where('id', $project_id)->get();
    	
    	$projects = Project::where('user_id', $user_id)->orderBy('completed', 'asc')->orderBy('order', 'asc')->get();

    	
    	$data['project_id'] = $project_id;
    	$data['projects'] = $projects; 
    	$data['current_project'] = $current_project;
    	$data['display_tasks'] = $request->session()->get('display_tasks');
    	$data['task_order'] = $request->session()->get('task_order');
    	$tasks = filteredTasksToDisplayWithProject_H( $request->session()->get('display_tasks'), $request, $project_id );
    	$data['tasks'] = $tasks;
    	$data['is_project_page'] = "true";


    	return view('tasks')->with('data', $data);

    }//end show





    /*
     *
     *	  Function to store (create) a new task
     *	
     */

    public function store( Request $request, $project_id ) {
    	//current logged in user. 
    	$user_id = auth()->user()->id;
    
    	
    	//get all post inputs
    	
    	$inputs = $request->all();

    	

    	//make sure project ID belongs to current user.  Stop someone from adding a task to your project that isn't you.
    	$project = Project::findOrFail($project_id); 

    	if($project->user_id != $user_id)
        abort(403, 'This project does not belong to you.');
    	


    	

    	//if a project ID and inputs are provided - log them to the database, if not redirect to home with $errors. 
    	if( $project_id && $inputs['description'] ) {

    		$task = New Task;

			$task->description = $inputs['description'];
			$task->due_date    = $inputs['due_date'];
			$task->priority    = $inputs['priority'];
			$task->completed   = 0;
			$task->order       = 0;
			$task->user_id     = $user_id;
			$task->project_id  = $project_id;
    		$task->save();

    		//get all tasks
    		$tasks = filteredTasksToDisplayWithProject_H( $request->session()->get('display_tasks'), $request, $project_id );

   		

			//what informatoin do we need returned to the view from the database records?  Column ID's from database.
			$keys = array( 'id', 'project_id', 'due_date', 'priority', 'description', 'completed' );
			
			//strip tags and sanitize output before sending to the view. 
			$sanitized_tasks = sanitizeForOutput_H( $keys, $tasks );

			//return the sanitized object. 
    		return Response::json($sanitized_tasks);

    	} else {

    		return false;

    	}//end if



    }//end store




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //current user
        $user_id = auth()->user()->id;

        //get all post inputs
        $inputs = $request->all();

        $project_id  = $id;
		$description = $inputs['description'];
		$due_date    = $inputs['due_date'];
		$priority    = $inputs['priority'];
		$task_id     = $inputs['task_id'];
       	
       	//if priority matches an option
		if( $priority == "3" || $priority == "2" || $priority == "1" ) {
			//update the project if the correct user is logged in and you can find the right ID. 
	        $updateProject = Task::where('id', $task_id)->where( 'user_id', $user_id )->first();

	        

	        if( $updateProject ) {

				$updateProject->description = $description;
				$updateProject->due_date    = $due_date;
				$updateProject->priority    = $priority;

		        $updateProject->save();

		        //get all tasks
	    		$tasks = filteredTasksToDisplayWithProject_H( $request->session()->get('display_tasks'), $request, $project_id );

	    		//what informatoin do we need returned to the view from the database records?  Column ID's from database.
				$keys = array( 'id', 'project_id', 'due_date', 'priority', 'description', 'completed' );
			
				//strip tags and sanitize output before sending to the view. 
				$sanitized_tasks = sanitizeForOutput_H( $keys, $tasks );


				//return the sanitized object. 
    			return Response::json($sanitized_tasks);
	    
	        } else {
	        	return false;
	        }
		} else {
			return false;
		} 

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $task_id)
    {

    	$inputs = $request->all();
    	$project_id = $inputs['project_id'];
    	$urlHasProject = $inputs['urlHasProject'];
        //current user
        $user_id = auth()->user()->id;

        //delete all tasks for the project
        $deleteTask = Task::where('id', $task_id)->where( 'user_id', $user_id )->first();
        //if tasks found delete them
        if( $deleteTask ) {
            $deleteTask->delete();
 
            //if this request was sent from a project page - return the project tasks to the view.  If not return all tasks to the view. 
            if( $urlHasProject == "yes" ) {
            	$tasks = filteredTasksToDisplayWithProject_H( $request->session()->get('display_tasks'), $request, $project_id );
            	
            	//what informatoin do we need returned to the view from the database records?  Column ID's from database.
				$keys = array( 'id', 'project_id', 'due_date', 'priority', 'description', 'completed' );

				$sanitized_tasks = sanitizeForOutput_H( $keys, $tasks );

				//return the sanitized object. 
	    		return Response::json( $sanitized_tasks );
            } else {
            	//get all tasks filtered and ordered as user has selectged.
    			$tasks = filteredTasksToDisplayNoProject_H( $request->session()->get('display_tasks'), $request );

            	
            	//what informatoin do we need returned to the view from the database records?  Column ID's from database.
				$keys = array( 'id', 'project_id', 'due_date', 'priority', 'description', 'completed' );

				$sanitized_tasks = sanitizeForOutput_H( $keys, $tasks );

				//return the sanitized object. 
	    		return Response::json( $sanitized_tasks );
            }//end if
        }//end if
        

    } //end destroy



    public function updatefilters( Request $request ) {
    	//current user
        $user_id = auth()->user()->id;

        $inputs = $request->all();
        $display_tasks = array();



        /*
         *
         *	  Handle display_tasks filter
         *	
         */
        

        if( isset($inputs['display_tasks']) ) {
        	//get the contents of request in array.
	        foreach( $inputs as $key=>$value ) {
	        	$display_tasks[$key] = $value;
	        }

	        $display_tasks = $display_tasks['display_tasks'];

	      
	        if( $inputs ) {
	        	//make sure data is validated
	        	if( $display_tasks === "All" || $display_tasks === "Completed" || $display_tasks === "Uncompleted") {

	        			$current_user = User::findOrFail( $user_id );
	        			$current_user->display_tasks = $display_tasks;
	        			$current_user->save();
	        			$request->session()->put('display_tasks', $display_tasks);
	        	}//end if
	        }//end if

        }//end if



        /*
         *
         *	  Handle task_order section
         *	
         */

       

        if( isset($inputs['task_order']) ) {

        	if( $inputs['task_order'] === "priority" || $inputs['task_order'] === "due_date" ) {
        		$current_user = User::findOrFail( $user_id );
        		$current_user->task_order = $inputs['task_order'];
        		$current_user->save();
        		$request->session()->put('task_order', $inputs['task_order']);
        	}


        }//end if
        
        




    }//end updatefitlers


    /*
     *
     *	  Handles changing completed/not completed in database.
     *	
     */
    

    public function completed( Request $request ) {
    	//current user
        $user_id = auth()->user()->id;

        $inputs = $request->all();


        if( isset( $inputs['task_id'] ) && is_numeric($inputs['task_id']) ) {
        	$existingTask = Task::where( 'user_id', $user_id )->where( 'id', $inputs['task_id'] )->first();

      

        	
       	   
        	/*
        	*	Update the database
        	*/

        	if( $existingTask['original']['completed'] == 0 ) {
        		Task::where( 'user_id', $user_id )->where( 'id', $inputs['task_id'] )->update(['completed'=> 1]);
        	} elseif( $existingTask['original']['completed'] == 1 ) {
        		Task::where( 'user_id', $user_id )->where( 'id', $inputs['task_id'] )->update(['completed'=> 0]);
        	}


        	/*
        	*	Data to send back
        	*/

        	//if coming from a project page only send project information.
        	if( $inputs['is_project_page'] == 'true' ) {

        		$returnTasks = filteredTasksToDisplayWithProject_H( $request->session()->get('display_tasks'), $request, $inputs['project_id'] );
        	}

        	//if coming from view all tasks send all tasks back

        	if( $inputs['is_project_page'] == 'false' ) {
        		$returnTasks = filteredTasksToDisplayNoProject_H( $request->session()->get('display_tasks'), $request );
        	}


        	//what informatoin do we need returned to the view from the database records?  Column ID's from database.
			$keys = array( 'id', 'project_id', 'due_date', 'priority', 'description', 'completed' );

			$sanitized_tasks = sanitizeForOutput_H( $keys, $returnTasks );


			//return the sanitized object. 
	    	return Response::json( $sanitized_tasks );

        }
    }//end completed



    


}
