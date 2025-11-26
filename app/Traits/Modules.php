<?php

namespace App\Traits;

use Akaunting\Module\Facade as Module;

trait Modules
{
    private function vendorFields($configs, $fieldsType = 'vendor_fields')
    {
        $fields = [];
        $allowedPluginsPerPlan = auth()->user()->company ? auth()->user()->company->getPlanAttribute()['allowedPluginsPerPlan'] : null;
        //dd($allowedPluginsPerPlan);
        foreach (Module::all() as $key => $module) {

            if (is_array($module->get($fieldsType)) && ($allowedPluginsPerPlan == null || in_array($module->get('alias'), $allowedPluginsPerPlan))) {

                $insertThisModuleVendorFields = true;
                //If Module is payment method, check if admin has approved usage
                if ($module->get('isPaymentModule')) {
                    //check if admin has approved usage
                    if (! config($module->get('alias').'.useVendor')) {
                        $insertThisModuleVendorFields = false;
                    }

                }

                if ($insertThisModuleVendorFields) {
                    foreach ($module->get($fieldsType) as $key => $field) {
                        if ($field['key'] == 'time_zone') {
                            $field['data'] = config('timezones');
                        }
                        if (! (isset($field['onlyin']) && str_contains( $field['onlyin'],config('settings.app_project_type')))) {

                            array_push($fields, $field);
                        }
                    }
                }

            }

        }

        foreach ($fields as &$field) {
            if (isset($configs[$field['key']])) {
                $field['value'] = $configs[$field['key']];
            } elseif (! isset($field['value'])) {
                $field['value'] = '';
            }
        }

        return $fields;

    }

    public function setMultipleConfig($repo, $request, $fields)
    {
        if ($request->has('custom')) {

            //Now loop to find and upload image
            for ($i = 0; $i < count($fields); $i++) {
                if ($fields[$i]['ftype'] == 'image') {
                    if (isset($request->custom[$fields[$i]['key']])) {
                        $image = $this->saveImageVersions(
                            'uploads/companies/',
                            $request->custom[$fields[$i]['key']],
                            [
                                ['name' => 'large'],
                            ],
                            true
                        );
                        $repo->setConfig($fields[$i]['key'], $image);
                    }
                } else {
                    $repo->setConfig($fields[$i]['key'], $request->get('custom')[$fields[$i]['key']]);
                }
            }
        }

    }
}
