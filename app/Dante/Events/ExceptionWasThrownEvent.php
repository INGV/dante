<?php

namespace App\Dante\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExceptionWasThrownEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $message;
    public $status;
    public $debug;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
		$this->url              = $data['url'] ?? '--';
        $this->message          = $data['message'] ?? '--';
        $this->status           = $data['status'] ?? '--';
        $this->statusMessage    = $data['statusMessage'] ?? '--';
        $this->random_string    = $data['random_string'] ?? '--';
        $this->log_file         = $data['log_file'] ?? '--';
        $this->server_address   = request()->ip().' - '.request()->server('SERVER_ADDR').':'.request()->server('SERVER_PORT');
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
