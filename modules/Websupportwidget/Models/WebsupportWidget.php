<?php

namespace Modules\Websupportwidget\Models;

use App\Models\MyModel;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebsupportWidget extends MyModel
{
    use HasFactory;

    protected $table = 'websupportwidgets';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected $imagePath = '/uploads/companies/';

    protected $casts = [
        'chat_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'help_enabled' => 'boolean',
        'call_enabled' => 'boolean',
        'show_company_logo' => 'boolean',
        'show_agent_status' => 'boolean',
        'business_hours' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            $company_id = session('company_id', null);
            if ($company_id) {
                $model->company_id = $company_id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function getImageLinkAttribute()
    {
        return $this->getImage($this->logo, '');
    }

    /**
     * Generate widget embed URL
     */
    public function getEmbedUrlAttribute()
    {
        return config('app.url').'/websupport/widget?id='.$this->id;
    }

    /**
     * Check if widget is currently online based on business hours
     */
    public function getIsOnlineAttribute()
    {
        if (! $this->business_hours) {
            return true; // Always online if no business hours set
        }

        $now = now($this->timezone);
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        $todayHours = $this->business_hours[$currentDay] ?? null;

        if (! $todayHours || ! $todayHours['enabled']) {
            return false;
        }

        return $currentTime >= $todayHours['open'] && $currentTime <= $todayHours['close'];
    }

    /**
     * Get WhatsApp chat URL
     */
    public function getWhatsappUrlAttribute()
    {
        $message = urlencode($this->chat_welcome_message);

        return 'https://wa.me/'.$this->whatsapp_number.'?text='.$message;
    }
}
