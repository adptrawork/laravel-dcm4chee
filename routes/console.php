<?php

use App\Console\Commands\SyncWorklistStatus;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SyncWorklistStatus::class)->everyFiveMinutes();
