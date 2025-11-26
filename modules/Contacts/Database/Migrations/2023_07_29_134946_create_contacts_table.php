<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('custom_contacts_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->default('text');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->softDeletes();
            $table->timestamps();
        });

        //Countries
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lat')->default("");
            $table->string('lng')->default("");
            $table->string('iso2');
            $table->string('iso3');
            $table->string('phone_code');
            $table->string('timezone');
            $table->string('languages');
            $table->timestamps();
        });

         //Do a insert
         try {
            $countries = json_decode(File::get(base_path('/modules/Contacts/Database/Migrations/json/countries.json')), true);
            $locations = json_decode(File::get(base_path('/modules/Contacts/Database/Migrations/json/locations.json')), true);

            $associativeLocationsArray = [];

            // Iterate through the data and construct the associative array
            foreach ($locations as $location) {
                $associativeLocationsArray[$location['code']] = $location;
            }
            

            foreach ($countries as $key => $country) {
                //dd($associativeLocationsArray[$country['ISO2']]);
                DB::table('countries')->insertGetId([
                    'name'=>$country['Name'],
                    'lat'=>isset($associativeLocationsArray[$country['ISO2']])?$associativeLocationsArray[$country['ISO2']]['lat']:"",
                    'lng'=>isset($associativeLocationsArray[$country['ISO2']])?$associativeLocationsArray[$country['ISO2']]['lng']:"",
                    'iso2'=>$country['ISO2'],
                    'iso3'=>$country['ISO3'],
                    'phone_code'=>$country['Phone Code'],
                    'timezone'=>$country['Time'],
                    'languages'=>$country['Languages']
                ]);
            }
        } catch (\Throwable $th) {
            //dd($th);
        }

        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->string('avatar')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->softDeletes();
            $table->timestamps();
            $table->timestampTz('last_reply_at')->nullable();
            $table->timestampTz('last_client_reply_at')->nullable();
            $table->timestampTz('last_support_reply_at')->nullable();
            $table->string('last_message')->default("");
            $table->boolean('is_last_message_by_contact')->default(false);
            $table->boolean('has_chat')->default(false);
            $table->boolean('resolved_chat')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });


        Schema::create('groups_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');
        });

        Schema::create('custom_contacts_fields_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->unsignedBigInteger('custom_contacts_field_id');
            $table->foreign('custom_contacts_field_id')->references('id')->on('custom_contacts_fields');
            $table->string('value');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
