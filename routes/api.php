<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseOrderController;


Route::get('test', function () {
    return response()->json('API Route works Nut', 200);
});

Route::get('/purchase-orders', [PurchaseOrderController::class, 'getBreakdown']);
