<?php

namespace App\Http\Controllers;

// Base Controller — StockFlow API-First Architecture
    // This controller is intentionally minimal.
    // All business logic is handled by Api/* controllers returning JSON.
    // The frontend communicates exclusively with /api/* endpoints.
abstract class Controller
{
    // No dispatchApiRequest helper needed.
    // The frontend uses JavaScript fetch() with Bearer tokens directly.
}
