<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::table('clients', function (Blueprint $table) {
            $table->longText('contact_address_notes')->nullable()->change();
        });

        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->longText('importantnotes')->nullable()->change();
        });

        Schema::table('courts', function (Blueprint $table) {
            $table->longText('importantnotes')->nullable()->change();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->longText('importantnotes')->nullable()->change();
        });

    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('contact_address_notes', 1000)->nullable()->change();
        });

        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->string('importantnotes', 255)->nullable()->change();
        });

        Schema::table('courts', function (Blueprint $table) {
            $table->string('importantnotes', 255)->nullable()->change();
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('importantnotes', 255)->nullable()->change();
        });
        
    }
};
