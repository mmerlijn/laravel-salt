<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
//Class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            //name
            $table->enum('sex', ['M', 'F', 'X', ''])->comment('F->female, M->Male, X->other');
            $table->string('initials', 20)->nullable();
            $table->string('lastname', 80)->nullable();
            $table->string('own_lastname', 80);
            $table->string('prefix', 20)->nullable();
            $table->string('own_prefix', 20)->nullable();
            //phone
            $table->string('phone', 20)->nullable();
            //address
            $table->string('postcode', 6)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('street', 80)->nullable();
            $table->string('building', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tests');
        Schema::enableForeignKeyConstraints();
    }
};