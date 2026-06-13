<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use mmerlijn\LaravelSalt\Enums\LockTypeEnum;
use mmerlijn\LaravelSalt\Enums\NoteSubjectEnum;
use mmerlijn\LaravelSalt\Enums\NoteTypeEnum;
use mmerlijn\LaravelSalt\Enums\PatientActionsEnum;
use mmerlijn\msgRepo\Enums\LangEnum;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Enums\YesNoEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->dropIfExists('dwh_users');
        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->create('dwh_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 80)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('organization', 255)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->dropIfExists('log_accesses');
        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->create('log_accesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('loggable_id');
            $table->enum('loggable_type', ['patient', 'request']);
            $table->unsignedMediumInteger('user_id');
            $table->timestamps();
        });

        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->dropIfExists('log_changes');
        Schema::connection((config('app.env') == 'testing') ? 'sqlite' : 'mysql_dwh')->create('log_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['close', 'planning']);
            $table->unsignedInteger('record_id');
            $table->string('field', 255);
            $table->string('old', 255)->nullable();
            $table->string('new', 255)->nullable();
            $table->unsignedInteger('by')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pros_jobs');
    }


};

