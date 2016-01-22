@extends('layouts.app')


@section('content')


<!-- begin daily container -->
<div class="daily-container view-block">
  {{-- used in the javascript --}}
  <input id="url-edit-task" type="hidden" value="{{ url('/tasks/') }}">
  <input id="url-sort-order" type="hidden" value="{{ url('/sortupdate') }}">
  <input id="url-project-completed" type="hidden" value="{{ url('/projectcomplete') }}">
  <input id="url-update-filters" type="hidden" value="{{ url('/updatefilters') }}">
  {{-- used to set the display filters filter in JavaScript --}}
  <input id="display-tasks-value" type="hidden" value="{{ $data['display_tasks'] }}">
  {{-- used to set tasks_order for filter --}}
  <input id="display-tasks-order-value" type="hidden" value="{{ $data['task_order'] }}">
  {{-- completed/not complete URL --}}
  <input id="switch-completed-url" type="hidden" value="{{ url('/completed') }}">
  {{-- is this current page a project page? --}}
  <input id="is-project-page" type="hidden" value="{{ $data['is_project_page'] }}">


  {{-- show all project tasks --}}
  @if( !isset($data['project_id']) )
    <h3 class="centered">All Tasks</h3>
  @endif
  {{-- show all project tasks --}}

  @if( isset($data['project_id']) )
    {{-- used by JavaScript to know what URL to post to --}}
    <input id="url-store-task" type="hidden" value="{{ Request::url() }}"> 

    <div class="row">

      @if( isset($data['current_project']) )
        <h3 style="color: {{ $data['current_project'][0]->color }};">{{ $data['current_project'][0]->name }}</h3>
      @endif

      <h5 class="show-task-form"><span class="glyphicon glyphicon-plus"></span> Add Task</h5>
    </div>



    <!-- begin add task form -->
    <div class="row margin-sm add-task-form">
      <div class="col-md-4 col-md-offset-4">
          <div class="well">
            <form role="form">
              {{ csrf_field() }}
              <input class="referrer" type="hidden" name="referrer">
              <div class="form-group">
                <label>Task Description</label>
                <textarea name="description" class="form-control" placeholder="Add a Task..." required data-parsley-error-message="A description is required."></textarea>
              </div>
              <div class="form-group">
                <div class="row">
                  <div class="col-xs-6">
                    <label>Due Date</label>
                    <input class="form-control" type='date' placeholder="Due Date" name="due_date">
                  </div>
                  <div class="col-xs-6">
                    <label>Priority</label>
                    <select class="form-control" name="priority">
                      <option value="3">Low</option>
                      <option value="2">Medium</option>
                      <option value="1">High</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <input  id="create-task-submit-btn" class="form-control btn btn-primary" type="submit" value="Submit" name="submit">
              
            </form>
          </div>
      </div>
    </div>
  @endif
  <!-- end add task form -->
  

  <!-- enter item panel -->
  <div class="row margin margin-sm">
    <div class="col-md-6 col-md-offset-3">
      <div class="panel panel-default li-daily">
          <div class="panel-body item-panel-body">
            <div id="task-container" class="list-group">
              @foreach( $data['tasks'] as $task )
                <div class="list-group-item task-item" data-id="1" data-task-id = "{{ $task->id }}" data-project-id="{{ $task->project_id }}" data-delete-project-url="{{ url('/tasks') }}">
                  <span class="{{ completed_H( $task->completed ) }} message-item task-description"> {{ $task->description }}</span>
                  <br>
                  <br>
                  <em><small class="dates task-due-date">{{ $task->due_date }}</small></em><br>
                  <em><small class="priority task-priority">Priority: {{ displayPriority_H( $task->priority ) }}</small></em>

                  <div class="show-task-menu-button">
                    <span class="glyphicon glyphicon-menu-hamburger"></span>
                  </div>
                  <div class="menu-buttons">
                    <div class="menu-button-item" title="Check Off Item">
                      <span class="glyphicon glyphicon-ok control-buttons" aria-hidden="true"></span>
                    </div>
                    <div class="move-list-item menu-button-item" title="Edit Text" data-toggle="modal" data-target=".edit-task-modal">
                      <span class="edit-task-btn glyphicon glyphicon-pencil control-buttons" aria-hidden="true"></span>
                    </div>
                    <div class="delete menu-button-item" title="Delete Item">
                      <span class="glyphicon glyphicon-remove delete-button control-buttons" aria-hidden="true"></span>
                    </div>
                    
                  </div>
                </div>
              @endforeach
            </div>
          </div>
          <!--/panel-body-->
        </div>
        <!-- end col -->
      </div>
      <!--/panel-->
  </div> 
  <!-- end row -->
  <!-- enter item panel end -->
</div>
<!-- end daily container -->





<!-- filters -->
<div class="filters">
    <h4>Filter Tasks</h4>
    <label>Tasks to Display</label>
    <select id="display-tasks" class="form-control" name="display_tasks">
      <option value="All">All</option>
      <option value="Completed">Completed</option>
      <option value="Uncompleted">Not Completed</option>
    </select>
    <br>
    <div id="task-order">
      <label>Task Order</label>
      <select id="task-order-value" class="form-control filter-value" name="task_order">
        <option value="due_date">Due Date</option>
        <option value="priority">Priority</option>
      </select>
    </div>
    
</div>
<div class="show-filters-button" title="Filter Tasks">
  <span class="glyphicon glyphicon-filter"></span>
</div>
<!-- filters -->


<!-- edit a task modal -->
<div class="edit-task-modal modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit Task</h4>
      </div>
      <div class="modal-body">
        <form role="form" data-submit-url="{{ url('/tasks/') }}">
            {{ csrf_field() }}
            {{ method_field('PATCH') }} 
            <input class="task-edit-id" type="hidden" name="task_id">

            <div class="form-group">
              <label>Task Description</label>
              <textarea class="form-control task-description" required name="description"></textarea>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-xs-6">
                  <label>Due Date</label>
                  <input class="form-control task-due-date" type='date' placeholder="Due Date" name="due_date">
                </div>
                <div class="col-xs-6">
                  <label>Priority</label>
                  <select class="form-control task-priority" name="priority">
                    <option value="3">Low</option>
                    <option value="2">Medium</option>
                    <option value="1">High</option>
                  </select>
                </div>
              </div>
            </div>
            
            <input  id="submit-task-edit-btn" class="btn btn-success" type="submit" value="Submit" data-dismiss="modal">
            <button class="btn btn-default" data-dismiss="modal">Cancel</button>
            
          </form>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
<!-- edit a project modal -->





<!-- projects section -->
<div class="projects">
 <h4>Projects</h4>
 <h5> <span class="glyphicon glyphicon-list-alt"></span> <a href="{{ url('tasks') }}">Show Tasks for all Projects</a></h5>
 <h5 data-toggle="modal" data-target="#new-project-modal"><span class="glyphicon glyphicon-plus"></span> Add Project</h5>
 <ul class="list-group sort-container">
   @foreach( $data['projects'] as $project )
    <li class="list-group-item project-item {{ completedItemClass_H( $project->completed ) }}" data-project-id="{{ $project->id }}" data-project-order="{{ $project->order }}">
        <div class="project-title">
          <h5 class="project-name" data-project-url="{{ url('/tasks') . '/' . $project->id }}"><span class="glyphicon glyphicon-stop" style="color: {{ $project->color }};"></span> {{ $project->name }} </h5>
        </div>
        <div class="projects-menu-button">
          <span class="glyphicon glyphicon-menu-hamburger" title="Open/Close Menu"></span>
        </div>
        <div class="projects-menu">
          <span class="glyphicon glyphicon-ok" title="Project Completed"></span>
          <span data-toggle="modal" data-target="#edit-project-modal" class="glyphicon glyphicon-pencil edit-project-button" title="Edit Project" data-edit-project-url="{{ url('/project/' . $project->id . '/edit') }}"></span>
          <span class="glyphicon glyphicon-remove" data-toggle="modal" data-target="#delete-project-modal" title="Delete this Project"></span>
          {{-- <span class="glyphicon glyphicon-arrow-up" title="Move Up in List"></span>
          <span class="glyphicon glyphicon-arrow-down" title="Move Down in List"></span> --}}
        </div>
     </li>
   @endforeach
 </ul>
</div>
<div class="project-menu-icon"> 
 <span class="glyphicon glyphicon-folder-open"></span>
</div>
<!-- projects section -->


<!-- new project modal -->
<div id="new-project-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Create a Project</h4>
      </div>
      <form method="POST" action="{{ url('/project') }}">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <div class="col-md-6 col-md-offset-3">
                <label for="project-name">Project Name</label>
                <input id="project-name" class="form-control" type="text" placeholder="Enter a Project Name" name="name" required data-parsley-error-message="A project name is required.">
              </div>              
            </div>
            <div class="row margin-top">
              <div class="col-md-6 col-md-offset-3">
                <label for="project-color">Color &nbsp;</label>
                <input id="project-color" type="color" name="color"> 
              </div>  
            </div>
          </div>              
        </div>
        <div class="modal-footer">
          <input id="current-url" name="referrer" type="hidden" value="{{ Request::url() }}"> 
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <input id="submit-new-project" type="submit" class="btn btn-success" value="Submit" name="submit">
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- new project modal -->


<!-- edit project modal -->
<div id="edit-project-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit this Project</h4>
      </div>
      <div class="modal-body">
        <form id="edit-project-form" data-edit-project-url-partial="{{ url( '/project/' ) }}" method="post"> 
          {{ csrf_field() }}
          <div class="form-group">
            <div class="row">
              <div class="col-md-6 col-md-offset-3">
                <label for="project-name">Project Name</label>
                <input id="project-name" class="form-control" type="text" placeholder="Enter a Project Name" name="name">
              </div>              
            </div>
            <div class="row margin-top">
              <div class="col-md-6 col-md-offset-3">
                <label for="project-color">Color &nbsp;</label>
                <input id="project-color" type="color" name="color"> 
              </div>  
            </div>
            <input name="referrer" type="hidden" value="{{ Request::url() }}"> 
            <input id="edit-project-form-url" type="hidden" value="{{ url('project') }}">
          </div>  
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button id="update-project-btn" type="button" class="btn btn-success">Save Changes</button>
          </div>            
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- edit project modal -->



<!-- delete project modal -->  
<div id="delete-project-modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">CGI Technologies</h4>
      </div>
      <div class="modal-body">
        <p>Delete this project and all its associated tasks?</p>
      </div>
      <div class="modal-footer">
        <form method="post" data-delete-project-url="{{ url( '/project/' ) }}">
          {{ csrf_field() }}
          
          <input id="delete-project-id" type="hidden" name="id">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button id="delete-project-submit" type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
        </form>
        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- delete project modal -->








@stop