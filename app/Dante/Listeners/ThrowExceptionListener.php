<?php

namespace App\Dante\Listeners;

use App\Dante\Events\ExceptionWasThrownEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Dante\Mail\ThrowExceptionMail;
use Illuminate\Support\Facades\Mail;

class ThrowExceptionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DanteExceptionWasThrownEvent  $event
     * @return void
     */
    public function handle(ExceptionWasThrownEvent $event)
    {
        $data = [
			'url'				=> $event->url,
            'message'			=> $event->message,
            'status'            => $event->status,
            'statusMessage'		=> $event->statusMessage,
            'random_string'		=> $event->random_string,
            'log_file'			=> $event->log_file,
            'server_address'	=> $event->server_address,
        ];

        Mail::to(config('dante.emailRecipients'))->send(new ThrowExceptionMail($data));
    }
}
