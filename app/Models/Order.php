<?php

namespace App\Models;

use App\Enums\Order\OrderStatusEnum;
use App\Observers\OrderObserver;
use App\Traits\HandlesUuidValidationTrait;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

#[ObservedBy([OrderObserver::class])]
class Order extends Model
{
    use HandlesUuidValidationTrait;
    use HasFactory, SoftDeletes;

    public bool $timestamp = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'product_name',
        'amount',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatusEnum::class,
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
            'deleted_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope a query to only include orders of the authenticated user.
     */
    public function scopeCurrentUser(Builder $query): Builder
    {
        return $query->where('user_id', Auth::id());
    }
}
