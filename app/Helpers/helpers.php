<?php

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function trans($key = null, $replace = [], $locale = null)
    {

        if (is_null($key)) {
            return app('translator');
        }

        $vendor_entity_name = env('VENDOR_ENTITY_NAME', 'Company');
        $vendor_entity_name_plural = env('VENDOR_ENTITY_NAME_PLURAL', 'Companies');

        $message = app('translator')->get($key, $replace, $locale);
        if (strpos($key, 'estaurant') !== false && $vendor_entity_name != 'Company' && $vendor_entity_name_plural != 'Companies' /* Also check in the value to change to is not company  */) {
            $translatedEntity_plural = __($vendor_entity_name_plural);
            $translatedEntity = __($vendor_entity_name);

            //ES
            $message = str_replace('Companyes', $translatedEntity_plural, $message);
            $message = str_replace('Companye', $translatedEntity, $message);
            $message = str_replace('companyes', strtolower($translatedEntity_plural), $message);
            $message = str_replace('companye', strtolower($translatedEntity), $message);

            //ES
            $message = str_replace('Companies', $translatedEntity_plural, $message);
            $message = str_replace('Company', $translatedEntity, $message);
            $message = str_replace('companies', strtolower($translatedEntity_plural), $message);
            $message = str_replace('company', strtolower($translatedEntity), $message);

        }

        return $message;
    }
}
