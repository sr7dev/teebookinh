<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->longText('name');
            $table->longText('description')->nullable();
            $table->string('date')->nullable();
            $table->longText('number_of_attendees')->nullable();
            $table->longText('unit_of_attendees')->nullable();
            $table->longText('dimension')->nullable();
            $table->longText('type')->nullable();
            $table->longText('whether_virtual')->nullable();
            $table->longText('languages')->nullable()->comment('Languages used');
            $table->longText('is_internal')->nullable()->comment('internal or external');
            $table->longText('any_partners')->nullable()->comment('Any co-hosting/planning community partners?');
            $table->longText('component_covid19')->nullable()->comment('Any component responding to (or recovery) COVID19?');
            $table->longText('component_addressing')->nullable()->comment('Any component on addressing structural racism, anti-racism, racial justice?');
            $table->longText('leadership_level')->nullable()->comment('Community engagement/leadership level');
            $table->longText('resources')->nullable()->comment('Support or resources shared with the participants during the event');
            $table->longText('optional_1')->nullable();
            $table->longText('optional_2')->nullable();
            $table->longText('optional_3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
