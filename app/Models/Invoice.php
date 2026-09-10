<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Stripe\StripeClient;

class Invoice extends Model
{
    use HasFactory;

    private $stripe_invoice = null;

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'qid', 'customer_qid');
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'id');
    }

    public function billing(): HasOne
    {
        return $this->hasOne(Billing::class, 'id', 'billing_id');
    }
    public function getPaymentsArrayAttribute(){
        $ret = [];
        foreach($this->payments as $payment){
            $ret[] = $payment; //->toArray();
        }
        return $ret;
    }
/*
    public function getPaymentsAttribute(){
        return $this->payments();
    }
*/
    public function getStripeAttribute()
    {
        if ($this->stripe_invoice == null and $this->stripe_invoice_id != null) {
            $stripe = new StripeClient(config()->get('stripe.stripe_secret_key'));
            $this->stripe_invoice = $stripe->invoices->retrieve($this->stripe_invoice_id);
        }

        return $this->stripe_invoice; // default null set above
    }

    /**
     * @return void
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function payOutOfBand()
    {
        if ($this->stripe) {
            if ($this->stripe->status == 'open') {
                if (app()->environment('production')) {
                    // $stripe = new StripeClient(config()->get('stripe.stripe_secret_key'));
                    // $stripe->invoices->pay($this->stripe->id,['paid_out_of_band' => true]);
                } else {
                    \Log::debug('If in Production, would have called invoice->payOutOfBand();');
                }
            }
        }
    }

    /**
     * Process a payment made through Stripe. Called from the webhook ( WebhookController::stripeWebhook).
     *
     * @param  $stripeInvoice  The Stripe invoice object.
     * @return void
     */
    public function processStripePayment($stripeInvoice)
    {

        if ($stripeInvoice->paid and $this->payment_type == null) { // record new payment
            // echo "Payment type:".$this->payment_type."<br>";
            $payment = $stripeInvoice->amount_paid;
            $payment_date = date('Y-m-d', $stripeInvoice->status_transitions->paid_at);
            $this->customer->balance = $this->customer->balance - $payment;
            $this->customer->save();
            // echo "Updated customer<br>";

            $paymentModel = Payment::createFromStripe($this->id, $payment, $payment_date);

            // echo "Created payment entry<br>";
            $this->status = 'paid';
            $this->payment_amount = $payment;
            $this->payment_type = 'stripe';
            // echo "Due:" . $stripeInvoice->amount_due . "   Paid: " . $stripeInvoice->amount_paid . "<hr/>";
            $this->payment_date = $payment_date;
            $this->save();
        }

    }
    /*
    public function generateBrowserStructure(){
        $payments = [];
        $paymentAmount = 0;
        foreach($this->payments as $payment){
            $payments[] = [
                'id' => $payment->id,
                'payment_amount' => number_format($payment->payment_amount/100,2),
                'payment_type' => $payment->payment_type,
                'check_number' => $payment->check_number,
                'payment_date' => $payment->payment_date,
                'deposit_date' => $payment->deposit_date,
            ];
            $paymentAmount += $payment->payment_amount;
        }

        return [
            'id' => $this->id,
            'name' => $this->customer->fullname,
            'address' => $this->customer->address,
            'city' => $this->customer->city,
            'state' => $this->customer->state,
            'zip' => $this->customer->zip,
            'serviceaddress' => $this->customer->serviceaddress,
            'number' => $this->invoice_number,
            'due_date' => $this->due_date,
            'amount_due' => '$ ' . number_format($this->balance/100,2),
            'payment_amount' => '$' . number_format($paymentAmount/100, 2),
            'customer_balance' => '$' . number_format($this->customer->balance/100, 2),
            'payments' => $payments,
            'status' => $this->status,
        ];
    }
*/
}
