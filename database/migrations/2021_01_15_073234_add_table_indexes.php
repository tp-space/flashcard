<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('card', function (Blueprint $table) {
            $table->index('symbol');
            $table->index('user_id');
            $table->index('done');
        });

        Schema::table('label', function (Blueprint $table) {
            $table->index('label');
            $table->index('user_id');
        });

        Schema::table('example', function (Blueprint $table) {
            $table->index('example');
            $table->index('user_id');
        });


        Schema::table('card_label_mapping', function (Blueprint $table) {
            $table->index('card_id');
            $table->index('label_id');
        });

        Schema::table('card_example_mapping', function (Blueprint $table) {
            $table->index('card_id');
            $table->index('example_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('card', function (Blueprint $table) {
            $table->dropIndex('card_symbol_index');
            $table->dropIndex('card_user_id_index');
            $table->dropIndex('card_done_index');
        });

        Schema::table('label', function (Blueprint $table) {
            $table->dropIndex('label_label_index');
            $table->dropIndex('label_user_id_index');
        });

        Schema::table('example', function (Blueprint $table) {
            $table->dropIndex('example_example_index');
            $table->dropIndex('example_user_id_index');
        });


        Schema::table('card_label_mapping', function (Blueprint $table) {
            $table->dropIndex('card_label_mapping_card_id_index');
            $table->dropIndex('card_label_mapping_label_id_index');
        });

        Schema::table('card_example_mapping', function (Blueprint $table) {
            $table->dropIndex('card_example_mapping_card_id_index');
            $table->dropIndex('card_example_mapping_example_id_index');
        });
    }
}
