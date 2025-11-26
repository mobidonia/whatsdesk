<?php

namespace App\Traits;

trait Fields
{
    public function convertJSONToFields($fields)
    {
        $fieldsToRender = [];
        if ($fields) {
            foreach ($fields as $key => $field) {
                array_push($fieldsToRender, [
                    'ftype' => isset($field['ftype']) ? $field['ftype'] : 'input',
                    'icon' => isset($field['icon']) ? $field['icon'] : '⚙️',
                    'type' => isset($field['type']) ? $field['type'] : 'text',
                    'id' => 'custom['.$field['key'].']',
                    'name' => isset($field['title']) && $field['title'] != '' ? $field['title'] : $field['key'],
                    'placeholder' => isset($field['placeholder']) ? $field['placeholder'] : '',
                    'separator' => isset($field['separator']) ? $field['separator'] : null,
                    'value' => isset($field['value']) ? $field['value'] : '',
                    'required' => false,
                    'data' => isset($field['data']) ? $field['data'] : [],
                    'additionalInfo' => isset($field['additionalInfo']) ? $field['additionalInfo'] : '',
                ]);
            }
        }

        return $fieldsToRender;
    }
}
