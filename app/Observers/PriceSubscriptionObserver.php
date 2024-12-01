<?php

namespace App\Observers;

use App\Mail\PriceChangedNotificationMail;
use App\Mail\PriceSubscriptionConfirmationMail;
use App\Models\PriceSubscription;
use Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Mail;

class PriceSubscriptionObserver
{
    use DispatchesJobs;

    /**
     * Handle the PriceSubscription "created" event.
     */
    public function created(PriceSubscription $priceSubscription): void
    {
        try {
            Mail::to($priceSubscription->getAttribute('email'))
                ->send(new PriceSubscriptionConfirmationMail($priceSubscription));
        } catch (Exception) {
        }
    }

    /**
     * Handle the PriceSubscription "updated" event.
     */
    public function updated(PriceSubscription $priceSubscription): void
    {
        if (! $priceSubscription->isDirty('price')) {
            return;
        }

        try {
            $oldPrice = $priceSubscription->getOriginal('price');
            Mail::to($priceSubscription->getAttribute('email'))
                ->send(new PriceChangedNotificationMail($priceSubscription, $oldPrice));
        } catch (Exception) {
        }
    }

    /**
     * Handle the PriceSubscription "deleted" event.
     */
    public function deleted(PriceSubscription $priceSubscription): void {}

    /**
     * Handle the PriceSubscription "restored" event.
     */
    public function restored(PriceSubscription $priceSubscription): void {}

    /**
     * Handle the PriceSubscription "force deleted" event.
     */
    public function forceDeleted(PriceSubscription $priceSubscription): void {}
}
