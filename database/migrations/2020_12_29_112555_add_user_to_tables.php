<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUserToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('label', function (Blueprint $table) {
            $table->string('user_id')->default('0');
        });

        Schema::table('card', function (Blueprint $table) {
            $table->string('user_id')->default('0');
        });

        Schema::table('example', function (Blueprint $table) {
            $table->string('user_id')->default('0');
        });

        // By default, assing all labels, cards and examples to the first user
        DB::table('label')->update(['user_id' => 1]);
        DB::table('card')->update(['user_id' => 1]);
        DB::table('example')->update(['user_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('label', 'user_id')){
            Schema::table('label', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('card', 'user_id')){
            Schema::table('card', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('example', 'user_id')){
            Schema::table('example', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
}
