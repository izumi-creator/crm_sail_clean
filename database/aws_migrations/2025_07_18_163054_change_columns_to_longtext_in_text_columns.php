<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->longText('inquirycontent')->nullable()->change();
            $table->longText('explanation')->nullable()->change();
        });

        Schema::table('related_parties', function (Blueprint $table) {
            $table->longText('relatedparties_explanation')->nullable()->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->longText('case_summary')->nullable()->change();
            $table->longText('special_notes')->nullable()->change();
            $table->longText('childsupport_memo')->nullable()->change();
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->longText('explanation')->nullable()->change();
            $table->longText('special_notes')->nullable()->change();
        });

        Schema::table('advisory_consultations', function (Blueprint $table) {
            $table->longText('case_summary')->nullable()->change();
            $table->longText('special_notes')->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->longText('content')->nullable()->change();
        });

        Schema::table('negotiations', function (Blueprint $table) {
            $table->longText('content')->nullable()->change();
        });        

    }

    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('inquirycontent', 1000)->nullable()->change();
            $table->string('explanation', 1000)->nullable()->change();
        });

        Schema::table('related_parties', function (Blueprint $table) {
            $table->string('relatedparties_explanation', 1000)->nullable()->change();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('case_summary', 1000)->nullable()->change();
            $table->string('special_notes', 1000)->nullable()->change();
            $table->string('childsupport_memo', 1000)->nullable()->change();
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->string('explanation', 1000)->nullable()->change();
            $table->string('special_notes', 1000)->nullable()->change();
        });

        Schema::table('advisory_consultations', function (Blueprint $table) {
            $table->string('case_summary', 1000)->nullable()->change();
            $table->string('special_notes', 1000)->nullable()->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('content', 1000)->nullable()->change();
        });

        Schema::table('negotiations', function (Blueprint $table) {
            $table->string('content', 1000)->nullable()->change();
        });
        
    }
};
