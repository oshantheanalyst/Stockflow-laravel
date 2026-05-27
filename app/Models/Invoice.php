<?php

namespace App\Models;

// Backwards compatibility wrapper for Invoice.
    // Inherits all features from Order (mapping to the 'orders' table).
class Invoice extends Order
{
    // Queries automatically run against the 'orders' table.
}
