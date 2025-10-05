<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('external_id')->nullable()->after('id');
            $table->unique('external_id');
            $table->string('user_name')->nullable()->after('name');
            $table->unique('user_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->string('city')->nullable()->after('company_name');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('external_id');
            $table->dropColumn('user_name');
            $table->dropColumn('phone');
            $table->dropColumn('company_name');
            $table->dropColumn('city');            
            $table->dropColumn('deleted_at');            
        });
    }
};
