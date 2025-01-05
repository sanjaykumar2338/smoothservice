<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'company_settings';

    // Fillable fields
    protected $fillable = [
        'user_id',
        'company_name',
        'custom_domain',
        'timezone',
        'sidebar_color',
        'accent_color',
        'contact_link',
        'logo',
        'favicon',
        'application_icon', // New field
        'sidebar_logo',     // New field
        'spp_linkback',     // New field
    ];

    /**
     * Relationship: Belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
