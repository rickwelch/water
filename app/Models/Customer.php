<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaravelIdea\Helper\App\Models\_IH_ServiceSite_QB;
use Stripe\StripeClient;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'notifyemails',
        'billingemail',
        'serviceaddress',
        'zone',
        'address',
        'city',
        'state',
        'zip',
        'textphones',
        'lot',
        'status',
        'e_billing',
        'yearly_billing',
        'balance',
        'adjustment',
    ];
/*
    function serviceSite(){
        return $this->hasOne(ServiceSite::class, 'id', 'service_site_id');
    }
*/
    public function serviceSites()
    {
        return ServiceSite::where('customer_id', $this->id)->get()->toArray();
    }
/*
    public function serviceSites(): HasMany
    {
        return $this->hasMany(ServiceSite::class);
    }
*/
    /**
     * Needed because email is both nullable and unique, table can have multiple NULLS but only 1 empty string.
     *
     * @return void
     */
    public function setEmailAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['email'] = null;
        } else {
            $this->attributes['email'] = $value;
        }
    }

    /**
     * @return string
     *
     * returns either current or has_balance depending on balance
     */
    public function getBalanceClassAttribute() : string
    {
        if ($this->balance <= 0) {
            return 'current';
        }

        return 'has_balance';
    }

    public function invoices()
    {
        return Invoice::where('customer_qid', $this->qid)->get();
    }

    public function recentInvoices()
    {
        return Invoice::where('customer_qid', $this->qid)->orderBy('due_date', 'DESC')->limit(4)->get();
    }

    public function getFullnameAttribute()
    {
        return trim(trim($this->firstname).' '.$this->middlename).' '.$this->lastname;
    }

    public function getInvoice($due_date)
    {
        return Invoice::where('customer_qid', $this->qid)->where('due_date', $due_date)->first();
    }

    public function cellphones(): HasMany
    {
        return $this->hasMany(Cellphone::class);
    }

    public function textMessages()
    {
      return Message::whereIn('number', $this->cellphones->pluck('number'))->orderBy('created_at', 'DESC')->get();
    }

    function addStripeCustomer(){
        if($this->e_billing == false or $this->billingemail == null) return false;
        $stripe = new StripeClient(config()->get('stripe.stripe_secret_key'));
        $phones = explode(' ', $this->textphones);
        $stripeCustomer = $stripe->customers->create([
            'description' => $this->serviceaddress,
            'email' => $this->billingemail,
            'phone' => $phones[0] ?? '',
            'name' => $this->fullname,
            'address' => [
                'line1' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->zip,
            ],
            'metadata' => [
                'Service Address' => $this->serviceaddress,
                'qid' => $this->qid,
            ],
        ]);
        $this->stripe_customer_id = $stripeCustomer->id;
        $this->save();
        \Log::debug('Created Stripe Customer ID: ' . $this->stripe_customer_id . ' for ' . $this->fullname);
        return $this->stripe_customer_id;
    }

}
