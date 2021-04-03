<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FailedConnection extends Notification
{
    use Queueable;
    public $mpesaTransaction;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($mpesaTransaction)
    {
       $this->mpesaTransaction = $mpesaTransaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase($notifiable)
    {
       return [
            'error_message' => $this->mpesaTransaction->status,
            'amount'=>$this->mpesaTransaction->amount,
            'phone_number'=>$this->mpesaTransaction->phone_number
       ]; 
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
