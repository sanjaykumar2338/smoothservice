<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'description',
        'addon',
        'group_multiple',
        'assign_team_member',
        'set_deadline_check',
        'set_a_deadline',
        'set_a_deadline_duration',
        'user_id',
        'price_options',
        'combinations',
        'pricing_option_data',
        'one_time_service_currency',
        'one_time_service_currency_value',
        'multiple_orders',
        'recurring_service_currency',
        'recurring_service_currency_value',
        'recurring_service_currency_every',
        'recurring_service_currency_value_two',
        'recurring_service_currency_value_two_type',
        'with_trial_or_setup_fee',
        'when_recurring_payment_received',
        'when_recurring_payment_received_two_order_currency',
        'when_recurring_payment_received_two_order_currency_value',
        'total_requests',
        'active_requests',
        'show_in_the_service_page',
        'trial_currency',
        'trial_price',
        'trial_for',
        'trial_period'
    ];

    protected $casts = [
        'price_options' => 'array',
    ];

    public function parentServices()
    {
        return $this->belongsToMany(Service::class, 'service_parent_services', 'service_id', 'parent_service_id');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'service_team_members', 'service_id', 'team_member_id');
    }
}
