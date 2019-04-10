<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsensoToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::table('subscriptions', function (Blueprint $table) {
          $table->boolean('consenso_affiliazione')->default(0)->after('type');
          $table->boolean('consenso_dati_sanitari')->default(0)->after('type');
          $table->boolean('consenso_foto')->default(0)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {

        });
    }
}
