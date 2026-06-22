<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Twilio\Rest\Client;

class SendVotingLink implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $link = url('/vote/'.$this->token);

        $client->messages->create(
            $this->phone,
            [
                'from' => config('services.twilio.from'),
                'body' => "You are invited to vote. Click: ".$link
            ]
        );

        SendVotingLink::dispatch($voter->phone, $plainToken);
    }
}
