<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

//import my models I need to use. 
use App\Project;

use Response;







class ProjectsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
        //get all project inputs from the post

        $inputs = $request->all();

        if( $inputs['color'] ) {


            /*
             *
             *      Handle adding sort order to projects. "sort"
             *      column in projects table.  Get max sort # 
             *      then add 1 for the sort column value. 
             *    
             */

            //gets the max order number from projects table. 
            $max_project_sort = Project::max('order');
            $order_number = null;

            if( $max_project_sort ) {
                $order_number = $max_project_sort + 1;
            } else {
                $order_number = 1;
            }


            

            $project = new Project;
            //this is a global way to access the auth class. 
            $user = auth()->user();

            $project->name      = $inputs['name'];
            $project->color     = $inputs['color'];
            $project->completed = 0;
            $project->user_id   = $user->id;
            $project->order     = $order_number;
            $project->save();

            return redirect($inputs['referrer']);

        } else {

        }
    }

    /**
     * Show the form for editing the specified resource.
     * This function takes an AJAX request and an ID
     * To edit a project record. 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        //current user
        $user_id = auth()->user()->id;

        //project to return with JSON. 
        $returnProject = Project::where('user_id', $user_id)->where('id', $id)->get();

        //what informatoin do we need returned to the view from the database records?  Column ID's from database.
        $keys = array('name', 'color');

        //sanitize the project for save output
        $sanitized_project = sanitizeForOutput_H( $keys, $returnProject );


        //return the sanitized object. 
        if( $sanitized_project ) {
            dd($sanitized_project);
            Response::json( $sanitized_project );
        } else {
            return false;
        }
            

    }

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
        $inputs   = $request->all();
        $name     = $inputs['name'];
        $color    = $inputs['color'];
        $referrer = $inputs['referrer'];



        //update the project if the correct user is logged in and you can find the right ID. 
        $updateProject = Project::where('id', $id)->where( 'user_id', $user_id )->first();
        if( $updateProject ) {
            $updateProject->name = $name;
            $updateProject->color = $color;
            $updateProject->save();
        }

        return back();
        

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyproject($id)
    {
        
        //current user
        $user_id = auth()->user()->id;


        //delete all tasks for the project
        $deleteProjectTasks = Project::where('id', $id)->where( 'user_id', $user_id )->first()->tasks();
        //if tasks found delete them
        if( $deleteProjectTasks ) {
            $deleteProjectTasks->delete();
        }
        //update the project if the correct user is logged in and you can find the right ID. 
        $updateProject = Project::where('id', $id)->where( 'user_id', $user_id )->first();

        $updateProject->delete();


        return url('/tasks');

    }//end destroy



    /*
     *
     *      Takes the new sort order (a JavaScript object - key is the
     *      project ID and value is the new sort order)
     *    
     */
    
    public function sortupdate( Request $request ) {
        //current user
        $user_id = auth()->user()->id;
        $inputs = $request->all();

    

        //for each new sort order - find the project and update it to the new sort order.
        if( $inputs ) {
            foreach( $inputs as $key=>$value ) {
                //make sure numbers are being sent
                if( is_numeric($key) && is_numeric($value) ) {
                    $project = Project::where('id', $key)->where( 'user_id', $user_id )->firstOrFail();
                    $project->order = $value;
                    $project->save();
                }
            }
        }//end if        


    }//end sortupdate.


    public function projectcomplete( Request $request ) {
        $inputs = $request->all();
        //current user
        $user_id = auth()->user()->id;

        if( $inputs ) {
            foreach( $inputs as $key=>$value ) {
                if( is_numeric($key) && is_numeric($value) ) {
                    $project = Project::where('id', $key)->where( 'user_id', $user_id )->firstOrFail();
                    $project->completed = $value;
                    $project->save();
                }
            }
        }
        


    }//projectcomplete
}
