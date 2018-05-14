<?php

namespace App\Interfaces;


interface TransactionInInterface {
    public function get($id);

    public function create($amount, $description);
}