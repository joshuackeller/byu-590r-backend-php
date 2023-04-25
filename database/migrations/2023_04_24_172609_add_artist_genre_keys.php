<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('artist_genre', function (Blueprint $table) {
            $table->unique(['artist_id', 'genre_id']);
        });
    }

    public function down()
    {
        Schema::table('artist_genre', function (Blueprint $table) {
            $table->dropUnique(['artist_id', 'genre_id']);
        });
    }
};
