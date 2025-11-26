<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param {String} folder
     * @param {Object} laravel_image_resource, the resource
     * @param {Array} versinos
     */
    public function saveImageVersions($folder, $laravel_image_resource, $versions, $return_full_url = false)
    {
        //Make UUID
        $uuid = Str::uuid()->toString();

        if (config('settings.use_s3_as_storage', false)) {
            //S3 - store per company
            $path = $laravel_image_resource->storePublicly('uploads/companies', 's3');

            return Storage::disk('s3')->url($path);
        }

        //Make the versions
        foreach ($versions as $key => $version) {
            $ext = 'jpg';
            if (isset($version['type'])) {
                $ext = $version['type'];
            }

            //Save location
            $saveLocation = public_path($folder).$uuid.'_'.$version['name'].'.'.'jpg';
            if (strlen(config('settings.image_store_path'.'')) > 3) {
                $saveLocation = config('settings.image_store_path'.'').$folder.$uuid.'_'.$version['name'].'.'.'jpg';
            }

            if (isset($version['w']) && isset($version['h'])) {
                $img = Image::make($laravel_image_resource->getRealPath())->fit($version['w'], $version['h']);
                $img->save($saveLocation, 100, $ext);
            } else {
                //Original image
                $img = Image::make($laravel_image_resource->getRealPath());
                $img->save($saveLocation, 100, $ext);
            }
        }

        if ($return_full_url) {
            $url = config('app.url').'/'.$folder.$uuid.'_'.$version['name'].'.'.'jpg';

            return preg_replace('#(https?:\/\/[^\/]+)\/\/#', '$1/', $url);
        } else {
            return $uuid;
        }

    }

    public function saveDocument($folder, $laravel_file_resource)
    {
        if (config('settings.use_s3_as_storage', false)) {
            //S3 - store per company
            $path = $laravel_file_resource->storePublicly('uploads/companies', 's3');

            return Storage::disk('s3')->url($path);
        } else {
            $path = $laravel_file_resource->store($folder, 'public_uploads');
            $url = config('app.url').'/uploads/'.$path;

            return preg_replace('#(https?:\/\/[^\/]+)\/\/#', '$1/', $url);
        }

    }

    public function getCompany()
    {
        if (! auth()->user()->hasRole('owner') && ! auth()->user()->hasRole('staff')) {
            return null;
        }

        //If the owner hasn't set auth()->user()->company_id set it now
        if (auth()->user()->hasRole('owner')) {

            //Check sessions, if there is company ID, then it is set
            if (session()->has('company_id')) {
                $company = Company::find(session('company_id'));
                if ($company == null) {
                    //There is error, company is not found, or removed
                   //Continue with the flow
                }else{
                    return $company;
                }
            }

            if (auth()->user()->company_id == null) {
                auth()->user()->company_id = Company::where('user_id', auth()->user()->id)->first()->id;
                auth()->user()->update();
            }
            //Get company for currerntly logged in user
            $company = Company::where('user_id', auth()->user()->id)->first();
            if ($company == null) {
                //There is error, company is not found, or removed
                auth()->logout();
                abort(403);
            }

            return Company::where('user_id', auth()->user()->id)->first();
        } else {
            //Staff
            return Company::findOrFail(auth()->user()->company_id);
        }

    }

    public function ownerOnly()
    {
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function ownerAndStaffOnly()
    {
        if (! auth()->user()->hasRole(['owner', 'staff'])) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function adminOnly()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function simple_replace_spec_char($subject)
    {
        $char_map = [
        ];

        return $subject;
        //return strtr($subject, $char_map);
    }

    public function replace_spec_char($subject)
    {
        $char_map = [
            'ъ' => '-', 'ь' => '-', 'Ъ' => '-', 'Ь' => '-',
            'А' => 'A', 'Ă' => 'A', 'Ǎ' => 'A', 'Ą' => 'A', 'À' => 'A', 'Ã' => 'A', 'Á' => 'A', 'Æ' => 'A', 'Â' => 'A', 'Å' => 'A', 'Ǻ' => 'A', 'Ā' => 'A', 'א' => 'A',
            'Б' => 'B', 'ב' => 'B', 'Þ' => 'B',
            'Ĉ' => 'C', 'Ć' => 'C', 'Ç' => 'C', 'Ц' => 'C', 'צ' => 'C', 'Ċ' => 'C', 'Č' => 'C', '©' => 'C', 'ץ' => 'C',
            'Д' => 'D', 'Ď' => 'D', 'Đ' => 'D', 'ד' => 'D', 'Ð' => 'D',
            'È' => 'E', 'Ę' => 'E', 'É' => 'E', 'Ë' => 'E', 'Ê' => 'E', 'Е' => 'E', 'Ē' => 'E', 'Ė' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Є' => 'E', 'Ə' => 'E', 'ע' => 'E',
            'Ф' => 'F', 'Ƒ' => 'F',
            'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G', 'Ĝ' => 'G', 'Г' => 'G', 'ג' => 'G', 'Ґ' => 'G',
            'ח' => 'H', 'Ħ' => 'H', 'Х' => 'H', 'Ĥ' => 'H', 'ה' => 'H',
            'I' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Í' => 'I', 'Ì' => 'I', 'Į' => 'I', 'Ĭ' => 'I', 'I' => 'I', 'И' => 'I', 'Ĩ' => 'I', 'Ǐ' => 'I', 'י' => 'I', 'Ї' => 'I', 'Ī' => 'I', 'І' => 'I',
            'Й' => 'J', 'Ĵ' => 'J',
            'ĸ' => 'K', 'כ' => 'K', 'Ķ' => 'K', 'К' => 'K', 'ך' => 'K',
            'Ł' => 'L', 'Ŀ' => 'L', 'Л' => 'L', 'Ļ' => 'L', 'Ĺ' => 'L', 'Ľ' => 'L', 'ל' => 'L',
            'מ' => 'M', 'М' => 'M', 'ם' => 'M',
            'Ñ' => 'N', 'Ń' => 'N', 'Н' => 'N', 'Ņ' => 'N', 'ן' => 'N', 'Ŋ' => 'N', 'נ' => 'N', 'ŉ' => 'N', 'Ň' => 'N',
            'Ø' => 'O', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'О' => 'O', 'Ő' => 'O', 'Ŏ' => 'O', 'Ō' => 'O', 'Ǿ' => 'O', 'Ǒ' => 'O', 'Ơ' => 'O',
            'פ' => 'P', 'ף' => 'P', 'П' => 'P',
            'ק' => 'Q',
            'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'ר' => 'R', 'Р' => 'R', '®' => 'R',
            'Ş' => 'S', 'Ś' => 'S', 'Ș' => 'S', 'Š' => 'S', 'С' => 'S', 'Ŝ' => 'S', 'ס' => 'S',
            'Т' => 'T', 'Ț' => 'T', 'ט' => 'T', 'Ŧ' => 'T', 'ת' => 'T', 'Ť' => 'T', 'Ţ' => 'T',
            'Ù' => 'U', 'Û' => 'U', 'Ú' => 'U', 'Ū' => 'U', 'У' => 'U', 'Ũ' => 'U', 'Ư' => 'U', 'Ǔ' => 'U', 'Ų' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ǖ' => 'U', 'Ǜ' => 'U', 'Ǚ' => 'U', 'Ǘ' => 'U',
            'В' => 'V', 'ו' => 'V',
            'Ý' => 'Y', 'Ы' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y',
            'Ź' => 'Z', 'Ž' => 'Z', 'Ż' => 'Z', 'З' => 'Z', 'ז' => 'Z',
            'а' => 'a', 'ă' => 'a', 'ǎ' => 'a', 'ą' => 'a', 'à' => 'a', 'ã' => 'a', 'á' => 'a', 'æ' => 'a', 'â' => 'a', 'å' => 'a', 'ǻ' => 'a', 'ā' => 'a', 'א' => 'a',
            'б' => 'b', 'ב' => 'b', 'þ' => 'b',
            'ĉ' => 'c', 'ć' => 'c', 'ç' => 'c', 'ц' => 'c', 'צ' => 'c', 'ċ' => 'c', 'č' => 'c', '©' => 'c', 'ץ' => 'c',
            'Ч' => 'ch', 'ч' => 'ch',
            'д' => 'd', 'ď' => 'd', 'đ' => 'd', 'ד' => 'd', 'ð' => 'd',
            'è' => 'e', 'ę' => 'e', 'é' => 'e', 'ë' => 'e', 'ê' => 'e', 'е' => 'e', 'ē' => 'e', 'ė' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'є' => 'e', 'ə' => 'e', 'ע' => 'e',
            'ф' => 'f', 'ƒ' => 'f',
            'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĝ' => 'g', 'г' => 'g', 'ג' => 'g', 'ґ' => 'g',
            'ח' => 'h', 'ħ' => 'h', 'х' => 'h', 'ĥ' => 'h', 'ה' => 'h',
            'i' => 'i', 'ï' => 'i', 'î' => 'i', 'í' => 'i', 'ì' => 'i', 'į' => 'i', 'ĭ' => 'i', 'ı' => 'i', 'и' => 'i', 'ĩ' => 'i', 'ǐ' => 'i', 'י' => 'i', 'ї' => 'i', 'ī' => 'i', 'і' => 'i',
            'й' => 'j', 'Й' => 'j', 'Ĵ' => 'j', 'ĵ' => 'j',
            'ĸ' => 'k', 'כ' => 'k', 'ķ' => 'k', 'к' => 'k', 'ך' => 'k',
            'ł' => 'l', 'ŀ' => 'l', 'л' => 'l', 'ļ' => 'l', 'ĺ' => 'l', 'ľ' => 'l', 'ל' => 'l',
            'מ' => 'm', 'м' => 'm', 'ם' => 'm',
            'ñ' => 'n', 'ń' => 'n', 'н' => 'n', 'ņ' => 'n', 'ן' => 'n', 'ŋ' => 'n', 'נ' => 'n', 'ŉ' => 'n', 'ň' => 'n',
            'ø' => 'o', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'о' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'ō' => 'o', 'ǿ' => 'o', 'ǒ' => 'o', 'ơ' => 'o',
            'פ' => 'p', 'ף' => 'p', 'п' => 'p',
            'ק' => 'q',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'ר' => 'r', 'р' => 'r', '®' => 'r',
            'ş' => 's', 'ś' => 's', 'ș' => 's', 'š' => 's', 'с' => 's', 'ŝ' => 's', 'ס' => 's',
            'т' => 't', 'ț' => 't', 'ט' => 't', 'ŧ' => 't', 'ת' => 't', 'ť' => 't', 'ţ' => 't',
            'ù' => 'u', 'û' => 'u', 'ú' => 'u', 'ū' => 'u', 'у' => 'u', 'ũ' => 'u', 'ư' => 'u', 'ǔ' => 'u', 'ų' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ǖ' => 'u', 'ǜ' => 'u', 'ǚ' => 'u', 'ǘ' => 'u',
            'в' => 'v', 'ו' => 'v',
            'ý' => 'y', 'ы' => 'y', 'ŷ' => 'y', 'ÿ' => 'y',
            'ź' => 'z', 'ž' => 'z', 'ż' => 'z', 'з' => 'z', 'ז' => 'z', 'ſ' => 'z',
            '™' => 'tm',
            '@' => 'at',
            'Ä' => 'ae', 'Ǽ' => 'ae', 'ä' => 'ae', 'æ' => 'ae', 'ǽ' => 'ae',
            'ĳ' => 'ij', 'Ĳ' => 'ij',
            'я' => 'ja', 'Я' => 'ja',
            'Э' => 'je', 'э' => 'je',
            'ё' => 'jo', 'Ё' => 'jo',
            'ю' => 'ju', 'Ю' => 'ju',
            'œ' => 'oe', 'Œ' => 'oe', 'ö' => 'oe', 'Ö' => 'oe',
            'щ' => 'sch', 'Щ' => 'sch',
            'ш' => 'sh', 'Ш' => 'sh',
            'ß' => 'ss',
            'Ü' => 'ue',
            'Ж' => 'zh', 'ж' => 'zh',
        ];

        return strtr($subject, $char_map);
    }

    public function makeAlias($name)
    {
        $name = $this->replace_spec_char($name);
        $name = str_replace(' ', '-', $name);

        //return strtolower(preg_replace('/[^A-Za-z0-9-]/', '', $name));
        return Str::slug($name, '');
    }
}
