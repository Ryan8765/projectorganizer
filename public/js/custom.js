$(document).ready(function() {


	/**********************************************************
		
		Globals
	
	**********************************************************/
	//gets the url for sort order update requests.

	var SORT_ORDER_URL = $('#url-sort-order').val();
	var PROJECT_COMPLETED_URL = $('#url-project-completed').val();




	/*
	 *
	 *	  Sends ajax request to update database - no return data.
	 *	
	 */
	
	function sendAjaxDataNoForm( url, data ) {

		var returnData;
		
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
	        url: url,
	        type:"POST",
	        data: data,
	        success:function(data){
	        	
	        	location.reload();
	        	
	        },error:function(data){ 
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	}//sendAjaxNoForm


	/*
	 *
	 *	  	Same as above - but sends a success callback function
	 *	
	 */
	

	function sendAjaxDataNoFormCallback( url, data, successCallback ) {

		var returnData;
		
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
	        url: url,
	        type:"POST",
	        data: data,
	        success: successCallback,

	        error:function(data){ 
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	}//sendAjaxNoForm
	


	


	/**********************************************************
		
		Handle checking off tasks as completed/not completed
	
	**********************************************************/
	$(document).on('click', '.task-item .glyphicon-ok', function() {

		var task_id = $(this).closest('.task-item').attr('data-task-id');
		var url = $('#switch-completed-url').val();
		var data = {};
		var is_project_page = $('#is-project-page').val();
		var project_id = $(this).closest('.task-item').attr('data-project-id');
		data.project_id = project_id;
		data.is_project_page = is_project_page;
		data.task_id = task_id;

		function successCallback( data ) {
			// location.reload();
			buildListItemsHTML( data );
		}

		sendAjaxDataNoFormCallback( url, data, successCallback );



	});	


	
	/**********************************************************
		
			Handle modifying filter options 
	
	**********************************************************/


	/*
	 *
	 *	  	On page load set fitler to appropriate setting - grabs
	 * 		hidden form field and sets the display_tasks to that.
	 *		also sets task_order filter as well. 
	 */

	(function(){
		var display_tasks_value = $('#display-tasks-value').val();
		var task_order_value = $('#display-tasks-order-value').val();
		$('#display-tasks option').each(function(index) {
			if( $(this).val() == display_tasks_value ) {
				$(this).attr('selected', 'selected');
			}
		});


		$('#task-order-value option').each(function(index) {
			if( $(this).val() == task_order_value ) {
				$(this).attr('selected', 'selected');
			}
		});
	})();

	 
	
	

	/*
	 *
	 *	  	Handle what happens when you change display_tasks 
	 *	    filter.
	 */
	
	$('#display-tasks').on('change', function() {
		var display_tasks = $(this).val();
		var data = {};
		data.display_tasks = display_tasks;
		var url = $('#url-update-filters').val();

		function successCallback() {
			location.reload();
		}

		sendAjaxDataNoFormCallback( url, data, successCallback )

	});


	/*
	 *
	 *	  Handles getting 
	 *	
	 */
	$('#task-order-value').on('change', function() {
		var task_order = $(this).val();
		var data = {};
		data.task_order = task_order;
		var url = $('#url-update-filters').val();

		function successCallback() {
			location.reload();
		}

		sendAjaxDataNoFormCallback( url, data, successCallback )

	});



	
	/**********************************************************
		
		Handle checking off a project - completed project. 
	
	**********************************************************/


	



	$(document).on('click', '.project-item .glyphicon-ok', function() {
	
		var project = $(this).closest('.project-item');
		var project_id = project.attr('data-project-id');
		var completed = project.hasClass('completed-item');
		var data = {};

		//change the class
		if( completed === true ) {
			data[project_id] = 0;
			sendAjaxDataNoForm( PROJECT_COMPLETED_URL, data );
		} else {
			data[project_id] = 1;
			sendAjaxDataNoForm( PROJECT_COMPLETED_URL, data );
		}
	});






	
	/**********************************************************
		
		Handle sorting of projects
	
	**********************************************************/
	/*
	*	Make items sortable with jquery sort
	*/

	$('.sort-container').sortable();

	//function to make sure only digits
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}

	/*
	*	function to send sort order - takes an object with the sort 
	*	order.
	*/
	function sendSortOrderAjax( object ) {

		//handle ajax request.
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});


		$.ajax({
	        url: SORT_ORDER_URL,
	        type:"POST",
	        data: object,
	        success: function(data) {

	        	
	        	

	        },
	        error:function(data){ 
       
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	}//end sendSortOrderAjax



	/*
	*	function to send sort order of projects to database.
	*/
	function sendSortOrder() {
		//get all projects
		var all_projects = $('.project-item');
		var updated_sort_order = {};
		var validatedAsNumbers = true;
		console.log(all_projects);
		//if there are projects get their order and send them to the databse.
		if( all_projects ) {
			//for each project - assign it a new order number in the updated_sort_order object. 
			all_projects.each(function(index) {
				index += 1;
				var project_id = $(this).attr('data-project-id');
				//if not a number don't send.
				if( !isNumber(project_id) ) {

					validatedAsNumbers = false;
				}
				updated_sort_order[project_id] = index;
			});

			//send the updated information.
			console.log('sort order update ' + updated_sort_order);
			if(validatedAsNumbers) {
				sendSortOrderAjax( updated_sort_order );
			}			

		}//end if
	}


	/*
	*	Send the information to the database. 
	*/
	$(document).on( "sortupdate", ".sort-container", function( event, ui ) {
		
		sendSortOrder();

	});


	
	/*
	 *
	 *	  Handle hiding and showing projects/filters/task form
	 *	
	 */


	 //handle hiding and howing the projects
	 $(document).on('click', '.project-menu-icon', function() {
	 	$('.projects').slideToggle( 200 );
	 });

	 //hide and show the filters.
	 $(document).on('click', '.show-filters-button', function() {
	 	$('.filters').slideToggle( 200 );
	 });

	 //hide and show the add task form.
	 $(document).on('click', '.show-task-form', function() {
	 	$('.add-task-form').slideToggle( 200 );
	 });

	 //hide the filters when a user selects a different filter;
	 $('.filter-value').change(function() {
	 	$('.filters').slideToggle( 200 );
	 });



	/*
	*
	*	  Handle menu buttons hide/show for Projects & Tasks
	*	
	*/

	//show/hide menu button click on a task.
	$(document).on('click', '.show-task-menu-button', function() {
		var menu_div = $(this).closest('.task-item').find('.menu-buttons');
		menu_div.fadeToggle( 200 );
	});

	//show task menu buttons on hover
	$(document).on('mouseenter', '.task-item', function() {
		$(this).find('.menu-buttons').show();
	});

	//Hide task menu buttons on leave
	$(document).on('mouseleave', '.task-item', function() {
		$(this).find('.menu-buttons').hide();
	});





	//show/hide menu button click on a task.
	$(document).on('click', '.projects-menu-button', function() {
		var menu_div = $(this).closest('.project-item').find('.projects-menu');
		menu_div.fadeToggle( 200 );
	});

	//show task menu buttons on hover
	// $(document).on('mouseenter', '.project-item', function() {
	// 	$(this).find('.projects-menu').show();
	// });
	
	//Hide task menu buttons on leave
	// $(document).on('mouseleave', '.project-item', function() {
	// 	$(this).find('.projects-menu').hide();
	// });
	
	
	
	/*
	 *
	 *	  Handle sending user to appropriate project on a
	 *	  project click
	 */

	 //function to redirect to another 
	 function redirect( url ) {
	 	window.location.href = url;
	 }


	 $('.project-name').on('click', function() {
	 	var url = $(this).attr('data-project-url');
	 	redirect( url );
	 });


	


		 /**********************************************************
		 	
		 	Handling Ajax Requests Section
		 
		 **********************************************************/
		 	 



	/*
	 *	  Build task list items function.  Takes data from the  	
	 *	  server and creates HTML for the view.  Also takes a  
	 *    selector to post the HTML to.  
	 */

	 function displayPriority_H( priority ) {
		var returnPriority = "";

		if( priority == 1 ) {
			returnPriority = "High";
		} else if( priority == 2 ) {
			returnPriority = "Medium";
		} else if( priority == 3 ) {
			returnPriority = "Low";
		}

		return returnPriority;

	}


	//used to build the HTML - if a value of 0 is supplied "uncompleted-item" will be returned, if a value of 1 "completed-item" will be returned.
	function displayClassForCompleted( value ) {
		if( value == 0 ) {
			return "uncompleted-item";
		} else if ( value == 1 ) {
			return "completed-item";
		}
	}

	 function buildListItemsHTML( data ) {
	 	var length = data.length;
	 	console.log(length);
	 	var html = "";

	 	//loop through data array to build all task items. 
	 	for( var i = 0; i < length; i++ ) {

	 		html += '<div class="list-group-item task-item" data-task-id = "' + data[i].id + '" data-project-id ="' + data[i].project_id + '">';
		 	html += '	<span class="message-item task-description ' + displayClassForCompleted( data[i].completed ) + '">' + data[i].description + '</span>';
		 	html += '	<br><br>';
		 	html += '	<em><small class="dates task-due-date">'+ data[i].due_date +'</small></em><br>';
		 	html += '	<em><small class="priority task-priority">Priority: '+ displayPriority_H( data[i].priority ) +' </small></em>';
		 	html += '<div class="show-task-menu-button"><span class="glyphicon glyphicon-menu-hamburger"></span></div><div class="menu-buttons"><div class="menu-button-item" title="Check Off Item"><span class="glyphicon glyphicon-ok control-buttons" aria-hidden="true"></span></div><div class="move-list-item menu-button-item" title="Edit Text" data-toggle="modal" data-target=".edit-task-modal"><span class="glyphicon glyphicon-pencil control-buttons edit-task-btn" aria-hidden="true"></span></div><div class="delete menu-button-item" title="Delete Item"><span class="glyphicon glyphicon-remove delete-button control-buttons" aria-hidden="true"></span></div></div></div>';


	 	}//end for

	 	//set the new html of the task container. 
	 	$('#task-container').html( html );
	 	
	 }//end buildListItemsHTML






	 /*
	 *
	 *	  Function to post form data to the server - adding tasks.  Takes a form 
	 *	  to be used to post data from, as well as a url to post to.
	 *	  Returns the data if found, otherwise false.  
	 */
	
	function postData( form, url ) {
		var returnData;
		
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
	        url: url,
	        type:"POST",
	        data: form.serialize(),
	        success:function(data){
	        	//parse the json and build the html with it the returned array.
	        	buildListItemsHTML( data ); 
	        	
	        },error:function(data){ 
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	   
		return returnData;
	}//end postData


	 $('#create-task-submit-btn').on('click', function(e) {
	 	e.preventDefault();
	 	var form = $(this).closest('form');
	 	var url = $('#url-store-task').val();

	 	//parslye instance
	 	var validation = form.parsley();
	 	validation.validate();

	 	if ( validation.isValid() ) {
	 		//post the data and build the list items.
		 	var data = postData( form, url );
		 	//reset the form to blank values. 
		 	form.trigger("reset");
	 	}

	 	 	
	 	
	 });	



	 
	 /**********************************************************
	 	
	 	Section to handle editing a project record. 
	 
	 **********************************************************/
	 


	 /*
	  *
	  *	  Function to get data from the server.  The
	  *	  successcallback does something with that data.  
	  *   The form is the form in question in the dom
	  *   The URL is the ajax request URL. 
	  */
	 

	 function getProjectData( url, successCallback ) {
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
	        url: url,
	        type:"POST",
	        success: successCallback,

	        error:function(data){ 
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	}//end postData



	//when user clicks on edit project - get the necessary project data to show in the form.
	$(document).on('click', '.edit-project-button', function() {
		var color = $(this).closest('.project-item').find('.glyphicon-stop').css('color');
		var name = $(this).closest('.project-item').find('.project-name').text();
		var project_id = $(this).closest('.project-item').attr('data-project-id');

		//add url to form
		var url = $('#edit-project-form-url').val();
		url += "/" + project_id;

		$('#edit-project-form').attr('action', url);

		
		//convert color to hex.
		function rgb2hex(rgb) {
     		if (  rgb.search("rgb") == -1 ) {
		          return rgb;
		     } else {
		          rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);
		          function hex(x) {
		               return ("0" + parseInt(x).toString(16)).slice(-2);
		          }
		          return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]); 
		     }
		}

		$('#edit-project-modal #project-name').val( name );
		$('#edit-project-modal #project-color').val( rgb2hex(color) );



	});	



	/*
	 *
	 *	  Function to post data coming from a form
	 *	
	 */
	
	function postDataOfAnyForm( form, url, successCallback ) {
		
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});

		$.ajax({
	        url: url,
	        type:"POST",
	        data: form.serialize(),
	        success: successCallback,
	        error:function(data){ 
	        	var errors = data.responseJSON;
        		
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax
	}//end postData



	


	//when the the save updated project information button is clicked - send ajax request to update the form and then get the necessary project information back. 
	$(document).on('click', '#update-project-btn', function(e) {
		
		var form = $(this).closest('form');
		form.submit();

	});



	/**********************************************************
		
		Section to handle deleting a project and all its associated tasks
	
	**********************************************************/


	//get the project ID and insert it into the hidden form field on the delete form.  Also get project name and insert that as well.
	$(document).on('click', '.project-item .glyphicon-remove', function() {

		var project_id = $(this).closest('li').attr('data-project-id');

		$('#delete-project-modal #delete-project-id').val( project_id );

		var project_name = $(this).closest('li').find('.project-name').text();
		$('#delete-project-modal .modal-title').text( project_name );

		//url to send the post to - need to update for the project id.
		var updateURL = $('#delete-project-modal form').attr('data-delete-project-url');
		updateURL = updateURL + '/' + project_id;
		$('#delete-project-modal form').attr('data-delete-project-url', updateURL);

		$('#delete-project-modal form').attr('action', updateURL);

	});


	//send ajax request and delete the project
	$(document).on('click', '#delete-project-submit', function(e) {
		e.preventDefault();
		var url = $(this).closest('.modal-footer').find('form').attr('data-delete-project-url');
		url = url.replace('project', 'destroyproject');
		alert(url);
		var form = $(this).closest('form');
		console.log("Form " + form.serialize());

		function successCallback(data) {
		
			window.location.href = data;
		}

		postDataOfAnyForm( form, url, successCallback )

	});
	




	$('#submit-new-project').on('click', function() {
		$(this).closest('form').submit();
		
	});
	
	
	/**********************************************************
		
		Handle parsley for adding a form
	
	**********************************************************/
	

	 	// var validation = form.parsley();
	 	// validation.validate();

	 	// $('#submit-new-project').on('click', function(e) {
	 		
	 	// 	e.preventDefault();
	 	// 	var form = $(this).closest('form');
	 	// 	var validation = form.parsley();
	 	// 	validation.validate();
	 	// 	console.log("isvalid " + validation.isValid());
	 	// 	if ( validation.isValid() ) {
		 // 		form.submit();
		 // 	}

	 	// });	

	
	/**********************************************************
		
		Handle edit task functionality
	
	**********************************************************/
	

	//add the clicked task to the modal on popup. transfers all tasks values to the modal.
	$(document).on('click', '.edit-task-btn', function() {
		var description = $(this).closest('.list-group-item').find('.task-description').text();

		description = description.trim();
		console.log("description" + description);
		var priority = $(this).closest('.list-group-item').find('.task-priority').text();

		priority = priority.split(' ');
		priority = priority[1];
		console.log("priority" + priority);
		var due_date = $(this).closest('.list-group-item').find('.task-due-date').text();
		console.log("due_date" + due_date);
		

		$('.edit-task-modal .task-description').val( description );
		var options = $('.edit-task-modal .task-priority option');

		options.each(function () {
			
			if( $(this).attr('value') === priority ) {
				$(this).attr('selected', 'selected');
			}
		});	


		$('.edit-task-modal .task-due-date').val( due_date );

		//add the url to submit to - adds appropriate task id
		var task_id = $(this).closest('.list-group-item').attr('data-task-id');
		console.log( "Task id " + task_id );
		$('.edit-task-modal form .task-edit-id').val(task_id);

		var project_id = $(this).closest('.list-group-item').attr('data-project-id');
		console.log( "Task id " + project_id );



		var partialURL = $('.edit-task-modal form').attr('data-submit-url');
		fullURL = partialURL + "/" + project_id;
		console.log( "URL " + fullURL );

		//set the attribute again on the form to include the task_id
		$('.edit-task-modal form').attr('data-submit-url', fullURL);

	});



	/*
	 *
	 *	  Send the task with the updated information to the 
	 *	  server.
	 */
	
	$(document).on('click', '#submit-task-edit-btn', function(e) {
		e.preventDefault();

		var taskID = $(this).closest()
		var form   = $(this).closest('form');
		var url    = form.attr('data-submit-url');


		//parslye instance
	 	var validation = form.parsley();
	 	validation.validate();

	 	//successful api call function
	 	function successCallback( data ) {
	 		
	 		buildListItemsHTML(  data  );
	 	}

	 	if ( validation.isValid() ) {
	 		postDataOfAnyForm( form, url, successCallback )
	 	}


	});



	/**********************************************************
		
		Handle deleting task section
	
	**********************************************************/
	

	$(document).on('click', '.task-item .glyphicon-remove', function() {

		//get current url.  If the url has a number - we need to not send all project tasks back, but just the project tasks for the associated project in the url.  Ex - http://....tasks\34 - we only want the tasks for project 34.  If no number - we want all tasks.
		var urlHasProject = "no";
		var currentURL = window.location.href;
		currentURL = currentURL.split('/');
		for( var i = 0; i < currentURL.length; i++ ) {
			if( $.isNumeric(currentURL[i]) ) {
				urlHasProject = "yes";
			}
		}
		//get the task-id
		var task_id    = $(this).closest('.task-item').attr('data-task-id');
		var project_id = $(this).closest('.task-item').attr('data-project-id');
		
		var url = $('#url-edit-task').val();
		
		url = url + "/" + task_id;
		

	

	

		//handle ajax request.
		$.ajaxSetup({
		  headers: {
		    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		  }
		});


		$.ajax({
	        url: url,
	        type:"POST",
	        data: { 
	        	task_id: task_id, 
	        	project_id: project_id, 
	        	urlHasProject: urlHasProject, 
	        	_method: 'DELETE' 
	        },
	        success: function(data) {

	        	
	        	buildListItemsHTML(  data  );

	        },
	        error:function(data){ 
	        	var errors = data.responseJSON;
        		console.log("errors " + errors);
	            alert('There was an error processing this request');
	        }
	    }); //end of ajax

	});



});