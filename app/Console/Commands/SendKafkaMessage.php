<?php

namespace App\Console\Commands;

use App\Enums\KafkaAction;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;

class SendKafkaMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-kafka-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = new Message(
            headers: ['event' => 'kafka_testing'],
            body: ['message' => "This is just testing if kafka is working", "action" => KAFKAAction::TESTING_KAFKA],
            key: config('app.KAFKA_HEADER_KEY')
        );

        //public order to kafka
        Kafka::publish()->onTopic('orders')->withMessage($message)->send();
    }
}
