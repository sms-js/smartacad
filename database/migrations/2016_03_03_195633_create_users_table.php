<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::connection('admin_mysql')->hasTable('user_types')) {
            Schema::connection('admin_mysql')->create('user_types', function (Blueprint $table) {
                $table->increments('user_type_id');
                $table->string('user_type', 150);
                $table->integer('type')->default(1);
                $table->timestamps();
                $table->softDeletes();

                $table->engine = 'InnoDB';
            });
        }
        
        Schema::create('users', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('password', 150);
            $table->string('email')->unique();
            $table->string('first_name',45);
            $table->string('last_name',45);
            $table->string('middle_name', 45)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone_no', 15)->unique()->nullable();
            $table->string('phone_no2', 15)->nullable();
            $table->date('dob')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('lga_id')->nullable()->unsigned()->index();
            $table->integer('salutation_id')->nullable()->unsigned()->index();
            $table->integer('verified')->unsigned()->default(0);
            $table->integer('status')->unsigned()->default(1);
            $table->string('verification_code')->nullable();
            $table->integer('user_type_id')->unsigned()->index();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->engine = 'InnoDB';
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

        if (Schema::connection('admin_mysql')->hasTable('user_types')) {
            Schema::connection('admin_mysql')->drop('user_types');
        }
    }
}
