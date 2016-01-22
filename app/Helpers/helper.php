<?php 

use App\Task;
use App\User;

/*
 *
 *	  Helper function to sanitize records being sent back via
 *	  Ajax.  Takes a list of "keys" to be searched for to sanitize (name
 *    from the database -columns- "due_date", "priority" etc.  Also takes
 *    a records parameter - these are the records returned from the 
 *    database query.
 */	 


function sanitizeForOutput_H( $keys_array, $records ) {
	$updated_value = "";
	$sanitized_array = array();

	//loop through all database records returned
	foreach( $records as $record ) {
		$updated_record_array = array();
		foreach ( $keys_array as $key ) {
			$value = htmlentities( $record->$key, ENT_QUOTES );
			$updated_record_array[$key] = $value; 
		}
		//add the new record onto the sanitized array (sanitized array is an array iwth many arrays inside - records).
		$sanitized_array[] = $updated_record_array;
	}//
	return $sanitized_array;
}//end sanitizeTags_H 




/*
 *
 *	  Function to handle outputting the correct class for 
 *	  completed/not completed items.  Takes the database
 *	  value for the column 'completed' - 0 for no
 *    1 for yes. 
 */

function completedItemClass_H( $value ) {

	if($value == 0) {
		return 'uncompleted-item';
	} else {
		return 'completed-item';
	}


}//end funciton completedITemClass



/*
 *		Function to return the correct query for Tasks.  
 *	  	Takes the "session" variable for user table "display_tasks"
 *		column.  If display_tasks = All - get all tasks.  If display_tasks
 *		= complete - only display completed, if display_tasks = 
 *		uncompleted only show uncompleted.
 */



function filteredTasksToDisplayNoProject_H( $display_tasks, $request ) {
		//current user
    	$user_id = auth()->user()->id;

		//tasks to return.
		$tasks = "";

	
			if( $display_tasks === "All" ) {
				$tasks = Task::where('user_id', $user_id)->orderBy($request->session()->get('task_order'), 'asc')->get();
			} elseif ( $display_tasks === "Completed" ) {
				$tasks = Task::where('user_id', $user_id)->where('completed', 1 )->orderBy($request->session()->get('task_order'), 'asc')->get();

			} elseif ( $display_tasks === "Uncompleted" ) {
				$tasks = Task::where('user_id', $user_id)->where('completed', 0 )->orderBy($request->session()->get('task_order'), 'asc')->get(); 
			}
	
		

		return $tasks;

}// end filteredTasksToDisplay


function filteredTasksToDisplayWithProject_H( $display_tasks, $request, $project_id ) {
		//current user
    	$user_id = auth()->user()->id;


		//tasks to return.
		$tasks = "";

		
			if( $display_tasks === "All" ) {
				$tasks = Task::where('user_id', $user_id)->where('project_id', $project_id)->orderBy($request->session()->get('task_order'), 'asc')->get();
			} elseif ( $display_tasks === "Completed" ) {
				$tasks = Task::where('user_id', $user_id)->where('project_id', $project_id)->where('completed', 1 )->orderBy($request->session()->get('task_order'), 'asc')->get();

			} elseif ( $display_tasks === "Uncompleted" ) {
				$tasks = Task::where('user_id', $user_id)->where('project_id', $project_id)->where('completed', 0 )->orderBy($request->session()->get('task_order'), 'asc')->get(); 

			}
		
		

		return $tasks;

}// end filteredTasksToDisplay



/*
 *
 *	  Display priority output - takes value 1, 2 and 3 and turns
 *	  it into string value for priority.
 */

function displayPriority_H( $priority ) {
	$returnPriority = "";

	if( $priority == 1 ) {
		$returnPriority = "High";
	} elseif( $priority == 2 ) {
		$returnPriority = "Medium";
	} elseif( $priority == 3 ) {
		$returnPriority = "Low";
	}

	return $returnPriority;

}



/*
 *
 *	  Used to add "completed/not completed" as a tasks class. 
 *	
 */

function completed_H( $completed ) {

	if( $completed == 0 ) {
		return "uncompleted-item";
	} elseif( $completed == 1 ) {
		return "completed-item";
	}

}//end completed






