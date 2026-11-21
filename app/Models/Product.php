<?php

namespace App\Models;

/**
 * Compatibility alias model for Buyer views/controllers sharing the unified `menus` table.
 */
class Product extends Menu
{
    protected $table = 'menus';
}
