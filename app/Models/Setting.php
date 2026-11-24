<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if ($setting && $setting->value !== null && $setting->value !== '') {
            return $setting->value;
        }

        // Alias fallback antara 'logo' <-> 'app_logo' dan 'nama_instansi' <-> 'app_name'
        $aliases = [
            'app_logo' => 'logo',
            'logo' => 'app_logo',
            'app_name' => 'nama_instansi',
            'nama_instansi' => 'app_name',
        ];

        if (isset($aliases[$key])) {
            $aliasSetting = self::where('key', $aliases[$key])->first();
            if ($aliasSetting && $aliasSetting->value !== null && $aliasSetting->value !== '') {
                return $aliasSetting->value;
            }
        }

        return $default;
    }

    public static function logoUrl($default = 'logoskolabs.png')
    {
        $logo = self::get('logo');
        if (!$logo) {
            return $default ? asset($default) : null;
        }
        if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }
        if (str_starts_with($logo, 'uploads/') || file_exists(public_path($logo))) {
            return asset($logo);
        }
        return \Illuminate\Support\Facades\Storage::url($logo);
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
