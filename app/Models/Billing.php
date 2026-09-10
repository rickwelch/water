<?php

namespace App\Models;

use App\Jobs\DeleteInvoiceJob;
use App\Jobs\GenerateInvoiceJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stripe\StripeClient;
use App\Jobs\FinalizeInvoiceJob;


/**
 * manage billings
 *
 * @TODO create billings for 2024 and 2025 so far
 * @TODO    add billing_id to invoices and make those connections.
 */
class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'billing_date',
        'due_date',
        'billing_amount',
        'period_start',
        'period_end',
        'footer_note',
    ];

    protected $casts = [
        //'billing_date' => 'date',
        //'due_date' => 'date',
        'meta' => 'array',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'billing_id', 'id');
    }

    public function cents2dollars($value)
    {
        return number_format($value/100, 2);
    }

    public function addChargesToCustomers(){

        $sites = ServiceSite::where('connected',1)->get();
        foreach($sites as $site){
            $customer = $site->customer;
            $customer->balance += $this->billing_amount;
            if($customer->adjustment <> 0){
                $customer->balance -= $customer->adjustment;
            }
            $customer->save();
        }

    }

    public function removeChargesFromCustomers(){
        $sites = ServiceSite::where('connected',1)->get();
        foreach($sites as $site){
            $customer = $site->customer;
            $customer->balance -= $this->billing_amount;
            if($customer->adjustment <> 0){
                $customer->balance += $customer->adjustment;
            }
            $customer->save();
        }

    }
    public function deleteInvoices(){
        $invoices = Invoice::where('billing_id', $this->id)->get();
        foreach($invoices as $invoice){
            DeleteInvoiceJob::dispatch($this, $invoice);
        }
    }

    public function generateInvoices(){
        $customers = Customer::where('balance', '>', 1000)->get();
        foreach($customers as $customer){
            \Log::debug('in Invoice::generateInvoices for customer: '.$customer->fullname." due: ".$this->due_date." balance: ".$customer->balance);
            GenerateInvoiceJob::dispatch($this, $customer);
        }

    }

    public function finalizeInvoices(){
        $invoices = Invoice::where('billing_id', $this->id)->get();
        foreach($invoices as $invoice){
            FinalizeInvoiceJob::dispatch($this, $invoice);
        }
    }
}
