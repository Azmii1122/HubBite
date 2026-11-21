<?php

namespace App\Models;

/**
 * Compatibility alias model for Buyer views/controllers sharing the unified `stores` table.
 */
class Merchant extends Store
{
    protected $table = 'stores';
}
