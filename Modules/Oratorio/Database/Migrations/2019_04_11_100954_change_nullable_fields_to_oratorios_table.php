<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableFieldsToOratoriosTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::table('oratorios', function (Blueprint $table) {
      $table->string('logo')->nullable()->change();
      $table->string('sms_sender')->nullable()->change();
      $table->string('reg_token')->nullable()->change();
      $table->integer('last_id_event')->nullable()->default(null)->change();
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    //
  }
}
