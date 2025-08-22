<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('spent', 15, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active')->default(true);

            // Foreign key to projects table
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');

            // Creator of the budget
            $table->integer('creator_id')->unsigned()->nullable();
            $table->foreign('creator_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->timestamps();

            // Index for efficient project-based queries
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('budgets');
    }
};