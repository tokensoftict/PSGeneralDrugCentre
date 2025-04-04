<?php

namespace App\Enums;

enum KafkaAction
{
    const PROCESS_ORDER = "PROCESS_ORDER";
    const PACKED_ORDER = "PACKED_ORDER";
    const TESTING_KAFKA = "TESTING_KAFKA";
}
