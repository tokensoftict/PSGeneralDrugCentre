<?php

namespace App\Console\Commands;

use Carbon\Exceptions\Exception;
use Illuminate\Console\Command;
use Junges\Kafka\Exceptions\ConsumerException;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\ConsumerMessage;

class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume';
    protected $description = 'Consume messages from Kafka topics';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer = Kafka::consumer(['orders'])
            ->withHandler(function ($message) {
                // Process the incoming message
                $body = $message->getBody();
                dump($body);
                // Handle the message payload as needed
            })
            ->build();

        try {
            $consumer->consume();
        } catch (Exception|ConsumerException $e) {
            dump($e->getMessage());
        }

    }
}
