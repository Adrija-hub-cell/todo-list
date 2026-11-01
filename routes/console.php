<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment('Small steps make big days.');
})->purpose('Display an inspirational quote');
