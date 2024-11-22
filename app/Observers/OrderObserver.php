<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    use DispatchesJobs;

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $userId = auth()->id();
        Cache::tags(['orders', "user:{$userId}"])->flush();
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $userId = auth()->id();
        Cache::tags(['orders', "user:{$userId}"])->flush();
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $userId = auth()->id();
        Cache::tags(['orders', "user:{$userId}"])->flush();
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
    }
}
