<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $expires_at
 * @property array{bank_name: string, account_name: string, account_number: string} $bank_details
 * @property bool $demo
 */
class Booking extends Model
{
    protected $guarded = ['id'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['bank_details' => 'array', 'expires_at' => 'datetime', 'demo' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    /** @return HasMany<Ticket, $this> */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /** @return HasMany<BookingEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(BookingEvent::class);
    }

    public function expireIfNeeded(): void
    {
        static::whereKey($this->id)->whereIn('status', ['awaiting_payment', 'needs_information'])
            ->where('expires_at', '<=', now())->update(['status' => 'expired']);
        $this->refresh();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'awaiting_payment' => 'Awaiting payment', 'under_review' => 'Payment under review',
            'needs_information' => 'More information needed', 'confirmed' => 'Booking confirmed',
            'rejected' => 'Payment not verified', 'expired' => 'Reservation expired', default => $this->status,
        };
    }
}
