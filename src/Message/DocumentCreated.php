<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class DocumentCreated
{
    public function __construct(
        public Uuid $id
    ) {
    }
}