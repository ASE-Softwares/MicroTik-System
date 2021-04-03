<?php

namespace App\Observers;

use App\Models\MpesaTransaction;
use App\Notifications\FailedConnection;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class MpesaTransactionObserver
{
    /**
     * Handle the MpesaTransaction "created" event.
     *
     * @param  \App\Models\MpesaTransaction  $mpesaTransaction
     * @return void
     */
    public function created(MpesaTransaction $mpesaTransaction)
    {
        if ($mpesaTransaction->status != 'Conected To Network SuccessFully') {
            $users = User::all();
           return  Notification::send($users, new FailedConnection($mpesaTransaction));            
        }
    }

    /**
     * Handle the MpesaTransaction "updated" event.
     *
     * @param  \App\Models\MpesaTransaction  $mpesaTransaction
     * @return void
     */
    public function updated(MpesaTransaction $mpesaTransaction)
    {
        //
    }

    /**
     * Handle the MpesaTransaction "deleted" event.
     *
     * @param  \App\Models\MpesaTransaction  $mpesaTransaction
     * @return void
     */
    public function deleted(MpesaTransaction $mpesaTransaction)
    {
        //
    }

    /**
     * Handle the MpesaTransaction "restored" event.
     *
     * @param  \App\Models\MpesaTransaction  $mpesaTransaction
     * @return void
     */
    public function restored(MpesaTransaction $mpesaTransaction)
    {
        //
    }

    /**
     * Handle the MpesaTransaction "force deleted" event.
     *
     * @param  \App\Models\MpesaTransaction  $mpesaTransaction
     * @return void
     */
    public function forceDeleted(MpesaTransaction $mpesaTransaction)
    {
        //
    }
}
