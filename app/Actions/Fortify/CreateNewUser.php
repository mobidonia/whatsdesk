<?php

namespace App\Actions\Fortify;

use App\Events\NewClient;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {

       
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'], // Add 'phone' to the validation rules
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $phone = "+" . $input['country_code'] . trim($input['phone']);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' =>$phone, // Add 'phone' to the user creation
        ]);

        //Passed by the form ( only if register from company page)
        $company_id=null;
        try {
            $company_id = $input['company_id'];
        } catch (\Exception $e) {
            $company_id = null;
        }
        if (!$company_id ) {
            $company_id = session('company_id');
        }


        //If we have a company session, then we need to create the user as client in the company
        if ($company_id) {
            $company = Company::find($company_id);
            if ($company != null) {
                $user->assignRole('client');
                $user->company_id = $company->id;
                $user->save();

                //Fire event
                event(new NewClient($user, $company));

                return $user;
            }

            
        }


        //Continue as company owner
        $user->assignRole('owner');

        //Create company
        $lastCompanyId = DB::table('companies')->insertGetId([
            'name' => $input['name'],
            'subdomain' => strtolower(preg_replace('/[^A-Za-z0-9]/', '', $input['name'])),
            'user_id' => $user->id,
            'created_at' => now(),  
            'updated_at' => now(),
            'phone'=>$phone, // Add 'phone' to the company creation
            'logo'=>asset('uploads').'/default/no_image.jpg',
        ]);

        $user->company_id = $lastCompanyId;

        return $user;
    }
    
}
