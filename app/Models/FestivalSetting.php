<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, array{name: string, guests: int, price: int}> $ticket_options
 * @property bool $demo
 * @property bool $sales_open
 */
class FestivalSetting extends Model
{
    protected $guarded = ['id'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['demo' => 'boolean', 'sales_open' => 'boolean', 'ticket_options' => 'array'];
    }
}
