<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'id', 'invoice_id');
    }

    /*
          public static function createFromWeb($invoice_id, $input){
            $model = new self();
            $model->invoice_id = $invoice_id;
            $model->payment_amount = $input['amount'] * 100;
            $model->payment_type = 'check';
            $model->check_number = $input['check_number'];
            $model->payment_date = (!empty($input['payment_date'])) ? $input['payment_date'] : date('Y-m-d');
            $model->deposit_date = $input['deposit_date'];
            $model->save();
            return $model;
        }
    */
    public static function createFromStripe($invoice_id, $amount, $date)
    {
        $model = new self;
        $model->invoice_id = $invoice_id;
        $model->payment_amount = $amount;
        $model->payment_type = 'stripe';
        $model->payment_date = $date;
        $model->save();

        return $model;
    }
}
