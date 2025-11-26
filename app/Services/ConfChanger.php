<?php

namespace App\Services;

class ConfChanger
{
    public static function switchCurrency($restaurant)
    {

        if ($restaurant && strlen($restaurant->currency) > 1) {
            config(['settings.cashier_currency' => $restaurant->currency]);

            //In this method, we should also handle convertion
            config(['settings.do_convertion' => $restaurant->do_covertion.'' == '1']);
        }
    }

    public static function switchCurrencyDirectly($currency, $do_covertion)
    {
        if (strlen($currency) > 1) {
            config(['settings.cashier_currency' => $currency]);

            //In this method, we should also handle convertion
            config(['settings.do_convertion' => $do_covertion.'' == '1']);
        }
    }

    public static function switchLanguage($restaurant)
    {

        //If we have enabled miltilanguage_menus
        if (config('settings.enable_miltilanguage_menus')) {
            //If we want to change the menu
            if (isset($_GET['lang'])) {
                //3. Change locale to the new local
                if ($_GET['lang'] != 'android-chrome-256x256.png') {
                    app()->setLocale($_GET['lang']);
                    session(['applocale_change' => $_GET['lang']]);
                }

            } else {
                //If current locale is not in the list of menus, use the default menu
                if ($restaurant->localMenus()->where('language', config('app.locale'))->first() == null) {
                    //Find default
                    $defaultLanguage = $restaurant
                        ->localMenus()->where('default', 1)->first();
                    if ($defaultLanguage && $defaultLanguage != 'android-chrome-256x256.png') {
                        app()->setLocale($defaultLanguage->language);
                        session(['applocale_change' => $defaultLanguage->language]);
                    }

                }
            }
        }

    }
}
