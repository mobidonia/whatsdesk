<?php

namespace App\Models;

use Akaunting\Module\Facade as Module;
use App\Traits\HasConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\WelcomeNotification\ReceivesWelcomeNotification;

class User extends Authenticatable
{
    use Billable;
    use HasApiTokens;
    use HasConfig;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use ReceivesWelcomeNotification;
    use TwoFactorAuthenticatable;

    protected $modelName = "App\Models\User";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    public function company()
    {
        if ($this->hasRole('owner')) {
            return $this->hasOne(Company::class);
        } else {
            //staff
            return $this->hasOne(Company::class, 'id', 'company_id');
        }
    }

    public function currentCompany()
    {
        if (!$this->hasRole('owner') && !$this->hasRole('staff')) {
            return null;
        }

        //If the owner hasn't set company_id set it now
        if ($this->hasRole('owner')) {
            //Check sessions, if there is company ID, then it is set
            if (session()->has('company_id')) {
                $company = Company::find(session('company_id'));
                if ($company != null) {
                    return $company;
                }
            }

            if ($this->company_id == null) {
                $this->company_id = Company::where('user_id', $this->id)->first()->id;
                $this->update();
            }

            //Get company for current user
            $company = Company::where('user_id', $this->id)->first();
            if ($company == null) {
                //There is error, company is not found, or removed
                auth()->logout();
                abort(403);
            }

            return Company::where('user_id', $this->id)->first();
        } else {
            //Staff
            return Company::findOrFail($this->company_id);
        }
    }

    public function getCurrentCompany()
    {
        return $this->currentCompany();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function plan()
    {
        return $this->hasOne(\App\Models\Plans::class, 'id', 'plan_id');
    }

    public function mplanid()
    {
        return $this->plan_id ? $this->plan_id : intval(config('settings.free_pricing_id'));
    }

    public function getExtraMenus()
    {
        $menus = [];
        if ($this->hasRole('admin')) {
            foreach (Module::all() as $key => $module) {
                if (is_array($module->get('adminmenus'))) {
                    foreach ($module->get('adminmenus') as $key => $menu) {
                        $menu['alias'] = $module->get('alias');
                        if (isset($menu['onlyin'])) {
                            if (config('app.'.$menu['onlyin'])) {
                                array_push($menus, $menu);
                            }
                        } else {
                            array_push($menus, $menu);
                        }

                    }
                }

                $availableApps = config('settings.apps_available', '');
                //If empty string, all apps allowed, if not filter the apps
                if ($availableApps != '') {
                    $availableApps = explode(',', $availableApps);
                    $menus = array_filter($menus, function ($menu) use ($availableApps) {
                        return in_array($menu['alias'], $availableApps);
                    });
                }
            }
        } elseif ($this->hasRole('client')) {
            foreach (Module::all() as $key => $module) {
                if (is_array($module->get('clientmenus'))) {
                    foreach ($module->get('clientmenus') as $key => $menu) {
                        if (isset($menu['onlyin'])) {
                            if (config('app.'.$menu['onlyin'])) {
                                array_push($menus, $menu);
                            }
                        } else {
                            array_push($menus, $menu);
                        }

                    }
                }
            }
        } elseif ($this->hasRole('owner')) {
            $allowedPluginsPerPlan = auth()->user()->company ? auth()->user()->company->getPlanAttribute()['allowedPluginsPerPlan'] : null;
            foreach (Module::all() as $key => $module) {
                if (is_array($module->get('ownermenus')) && ($module->get('alwayson') || $allowedPluginsPerPlan == null || in_array($module->get('alias'), $allowedPluginsPerPlan))) {
                    foreach ($module->get('ownermenus') as $key => $menu) {

                        if (isset($menu['onlyin'])) {
                            if(str_contains( $menu['onlyin'],config('settings.app_code_name'))) {
                                array_push($menus, $menu);
                            }
                        } else {
                            array_push($menus, $menu);
                        }
                    }
                }
            }
        } elseif ($this->hasRole('staff')) {
            foreach (Module::all() as $key => $module) {
                if (is_array($module->get('staffmenus'))) {
                    foreach ($module->get('staffmenus') as $key => $menu) {
                        array_push($menus, $menu);
                    }
                }
            }
        }

        //Sort the menus by priority
        usort($menus, function ($a, $b) {
            return (isset($a['priority'])?$a['priority']:100) <=> (isset($b['priority'])?$b['priority']:100);
        });

        return $menus;
    }

    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    public function routeNotificationForExpo()
    {
        return $this->expotoken.''; //"ExponentPushToken[".$this->expotoken."]";
    }

    protected static function booted()
    {
        parent::booted();


        static::updated(function ($user) {

            Log::info('User updated: ' . $user->email);
            if ($user->hasRole('admin')) {
                // Update the translation table with the latest admin user info
                // Assuming the translation table is 'ltu_contributors' as per seeder
                DB::table('ltu_contributors')
                    ->where('email', $user->getOriginal('email'))
                    ->update([
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'updated_at' => now(),
                    ]);
            }
        });
    }
}
