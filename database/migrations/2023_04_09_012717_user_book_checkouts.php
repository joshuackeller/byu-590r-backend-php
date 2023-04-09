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
Schema::create('user_book_checkouts', function (Blueprint $table) {
$table->foreignId('user_id')->constrained();
$table->foreignId('book_id')->constrained();
$table->foreignId('checkout_id')->constrained();
$table->primary(['user_id', 'book_id', 'checkout_id']);
});
}

/**
* Reverse the migrations.
*
* @return void
*/
public function down()
{
Schema::dropIfExists('user_book_checkouts');
}
};