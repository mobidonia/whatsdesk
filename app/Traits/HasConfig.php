<?php

namespace App\Traits;

use App\Models\Config;

trait HasConfig
{
    //Parent model, needs to have  - $modelName

    private function getRow($key)
    {
        return Config::where('key', $key)->where('model_type', $this->modelName)->where('model_id', $this->id)->first();
    }

    //Get config for key
    public function getConfig($key, $default = null)
    {
        $data = $this->getRow($key);
        if ($data) {
            return $data->value;
        } else {
            return $default;
        }
    }

    public function getAllConfigs()
    {
        return Config::where('model_type', $this->modelName)->where('model_id', $this->id)->get()->pluck('value', 'key')->toArray();
    }

    public function deleteAllConfigs()
    {
        Config::where('model_type', $this->modelName)->where('model_id', $this->id)->delete();
    }

    public function setMultipleConfig($configs)
    {
        if ($configs) {
            foreach ($configs as $key => $value) {
                $data = $this->getRow($key);
                if ($data) {
                    //Update
                    $data->value = $value;
                    $data->update();
                } else {
                    //Insert
                    Config::create([
                        'key' => $key,
                        'value' => $value,
                        'model_type' => $this->modelName,
                        'model_id' => $this->id,
                    ]);
                }

            }
        }

        return true;
    }

    //Set config for key
    public function setConfig($key, $value)
    {
        $data = [];
        $data[$key] = $value;

        return $this->setMultipleConfig($data);
    }

    public function increaseConfig($key)
    {
        $val = $this->getConfig($key, 0);
        $val++;
        $this->setConfig($key, $val);

        return $val;
    }
}
