<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{

    public function getBreakdown(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date_format:jS M Y',
        ]);

        $startDate = Carbon::createFromFormat('jS M Y', $request->start_date);
        $purchaseOrders = PurchaseOrder::where('service_start_date', $startDate->format('Y-m-d'))->get();

        if ($purchaseOrders->isEmpty()) {
            return response()->json(['error' => 'No purchase orders found for the given date'], 404);
        }

        $ticket = [];
        $totalGrandTotal = 0;

        foreach ($purchaseOrders as $purchaseOrder) {
            $breakdown = [];
            $grandTotal = 0;
            $serviceAdded = [];

            $services = $purchaseOrder->services;
            $maxCycle = $services->max('end_cycle');

            for ($cycle = 1; $cycle <= $maxCycle; $cycle++) {
                $cycleStartDate = $startDate->copy()->addMonths($cycle - 1);

                if ($startDate->day <= 14) {
                    $cycleEndDate = $cycleStartDate->copy()->endOfMonth();
                } else {
                    $cycleEndDate = $cycleStartDate->copy()->addMonth()->day(14);
                }

                $cycleServices = [];
                $cycleTotal = 0;

                foreach ($services as $service) {
                    if ($cycle == $service->end_cycle) {
                        if (!isset($serviceAdded[$service->id][$cycle])) {
                            $cycleServices[] = [
                                'name' => $service->name,
                                'price' => number_format($service->price, 2),
                            ];
                            $cycleTotal += $service->price;
                            $grandTotal += $service->price;
                            $serviceAdded[$service->id][$cycle] = true;
                        }
                    }
                }

                if (!empty($cycleServices)) {
                    usort($cycleServices, function ($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });

                    $breakdown[] = [
                        'cycle_period' => $cycleStartDate->format('jS M Y') . ' - ' . $cycleEndDate->format('jS M Y'),
                        'services' => $cycleServices,
                        'total' => number_format($cycleTotal, 2),
                    ];
                }
            }

            $ticket[] = [
                'purchase_order_id' => $purchaseOrder->id,
                'breakdown' => $breakdown,
                'grand_total' => number_format($grandTotal, 2),
            ];

            $totalGrandTotal += $grandTotal;
        }

        return response()->json([
            'ticket' => $ticket,
            'total_grand_total' => number_format($totalGrandTotal, 2),
        ]);
    }
}
