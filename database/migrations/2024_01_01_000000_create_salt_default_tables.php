<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use mmerlijn\LaravelSalt\Enums\CounterEnum;
use mmerlijn\LaravelSalt\Enums\LockTypeEnum;
use mmerlijn\LaravelSalt\Enums\NoteSubjectEnum;
use mmerlijn\LaravelSalt\Enums\NoteTypeEnum;
use mmerlijn\LaravelSalt\Enums\PatientActionsEnum;
use mmerlijn\msgRepo\Enums\LangEnum;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Enums\YesNoEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 255)->nullable();
            $table->string('email_ext', 255)->nullable();
            $table->enum('sex', PatientSexEnum::database())->comment('F->female, M->Male, X->other');
            $table->string('initials', 20)->nullable();
            $table->string('lastname', 80)->nullable();
            $table->string('own_lastname', 80);
            $table->string('prefix', 20)->nullable();
            $table->string('own_prefix', 20)->nullable();
            $table->date('dob');
            $table->string('bsn', 10)->nullable();
            $table->boolean('multiple_births')->default(false);
            $table->timestamp('deceased')->nullable();
            $table->string('postcode', 15)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('street', 80)->nullable();
            $table->string('building', 20)->nullable();
            $table->string('country', 20)->nullable();
            $table->string("last_requester", 8)->comment('agbcode')->nullable();
            $table->string('last_organization', 8)->comment('agbcode')->nullable();
            $table->string('general_practitioner', 8)->nullable()->comment('huisarts');
            $table->string('phone', 40)->nullable();
            $table->string('phone2', 40)->nullable();
            $table->string('phone_note', 255)->nullable();
            $table->string('uzovi', 10)->nullable();
            $table->string('policy_nr', 100)->nullable();
            $table->string('lbsnr', 15)->nullable();
            $table->enum('lang', LangEnum::database())->default('NL');
            $table->json('labels')->nullable();
            $table->timestamp('email_validated_at')->nullable();
            $table->unsignedInteger('contact_id')->nullable()->comment('link naar db_salt12.contact');
            $table->unsignedInteger('labtrain_id')->nullable()->comment('link naar labtrain patientnr');
            $table->unsignedSmallInteger('created_by')->nullable();
            $table->unsignedSmallInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at', 'bsn', 'dob'], 'patient_inded');
            $table->index(['deleted_at', 'last_requester'], 'patient_requester_ind');
            $table->index(['deleted_at', 'sex', 'dob', 'lastname', 'own_lastname', 'postcode'], 'patient_search_index');
        });

        Schema::create('patient_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('patient_id');
            $table->enum('type', PatientActionsEnum::database());
            $table->string('comment', 255)->nullable();
            $table->timestamp('at')->nullable();
            $table->json('detail')->nullable();
            $table->timestamps();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });



        Schema::create('requesters', function (Blueprint $table) {
            $table->string('agbcode', 8)->primary();
            $table->enum('type', VektisType::database())->default('ZORGVERLENER');
            $table->enum('sex', PatientSexEnum::database())->comment('F->female, M->Male, X->other')->nullable();
            $table->string('name');
            $table->string('initials', 20)->nullable();
            $table->string('lastname', 80)->nullable();
            $table->string('own_lastname', 80);
            $table->string('prefix', 20)->nullable();
            $table->string('own_prefix', 20)->nullable();
            $table->string('postcode', 7)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('building', 20)->nullable();
            $table->string('postbus', 10)->nullable();
            $table->string('extra_address_line', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->enum('is_gp', YesNoEnum::database())->default('N');
            $table->json('qualifications')->nullable();
            $table->json('owners')->nullable()->comment('array of agbcodes');
            $table->timestamp('vektis_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at', 'agbcode', 'own_lastname'], 'v_reqz_ind');
        });

        Schema::create('requester_convertors', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 255)->nullable()->comment('Name of the convertor');
            $table->string('from_agbcode', 10)->unique()->nullable()->comment('agbcode');
            $table->string('to_agbcode', 10)->nullable()->comment('agbcode');
            $table->date('from_date');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['deleted_at', 'from_agbcode'], 'requester_convertor_ind');
        });

        Schema::create('organization_has_requester', function (Blueprint $table) {
            $table->string('organization_agbcode', 8);
            $table->string('requester_agbcode', 8);
            $table->timestamps();
            $table->primary(['organization_agbcode', 'requester_agbcode']);
        });

        Schema::create('tool_uzovi', function (Blueprint $table) {
            $table->string('code', 4)->primary();
            $table->string('name', 255);
            $table->string('website', 255);
            $table->timestamp('active_from');
            $table->timestamp('active_to')->nullable();
            $table->string("concern")->nullable();
            $table->text("note")->nullable();
            $table->timestamps();
        });
        Schema::create('tool_counters', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('item', 50);
            $table->string('prefix', 8);
            $table->integer('size')->unsigned();
            $table->bigInteger('counter')->unsigned()->default(1000000000);
            $table->string('counter_text')->default('');
            $table->string('extension', 5)->nullable();
            $table->enum('type', CounterEnum::database())->default(CounterEnum::SALT_REQUEST_NR);
            $table->timestamps();
        });
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('subject_id');
            $table->enum('subject_type', NoteSubjectEnum::database());
            $table->enum('type', NoteTypeEnum::database())->default(NoteTypeEnum::_);
            $table->text('note');
            $table->unsignedMediumInteger('created_by')->default(500);
            $table->timestamp('delete_after');
            $table->timestamps();
        });

        Schema::create('locks', function (Blueprint $table) {
            $table->id();
            $table->enum('locked_type', LockTypeEnum::database());
            $table->unsignedInteger('locked_id');
            $table->timestamp('lock_end');
            $table->unsignedMediumInteger('user_id');
            $table->timestamps();
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }

    private function address(Blueprint $table): void
    {
        $table->string('postcode', 7)->nullable();
        $table->string('city', 100)->nullable();
        $table->string('street', 100)->nullable();
        $table->string('building', 20)->nullable();
        $table->string('postbus', 10)->nullable();
        $table->string('country', 4)->nullable();
        $table->string('extra_address_line', 100)->nullable();
    }

    private function contact(Blueprint $table): void
    {
        $table->string('email', 255)->nullable();
        $table->string('phone', 20)->nullable();
        $table->string('fax', 20)->nullable();
    }
};

