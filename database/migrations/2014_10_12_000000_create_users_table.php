<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password', 60);
            //used to verify email
            $table->string('activated_token');
            //used to verify email
            $table->tinyInteger('activated');
            //used to keep track of users sort preference. 
            $table->string('sort');
            // what tasks to display "All", "Completed" "Uncompleted".
            $table->string('display_tasks')->default('All');
            // "due_date" or "priority".
            $table->string('task_order')->default('due_date');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
