<?php

namespace App\Models;

// Backwards compatibility wrapper for InvoiceItem.
    // Inherits all features from OrderItem (mapping to the 'order_items' table).
class InvoiceItem extends OrderItem
{
    // Queries automatically run against the 'order_items' table.
}
