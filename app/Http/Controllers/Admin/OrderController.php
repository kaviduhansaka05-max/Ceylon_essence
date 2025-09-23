<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\MongoOrder;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        $status = trim((string) $request->query('status', ''));
        $q = MongoOrder::query()->orderBy('_id', 'desc');
        if ($status !== '') $q->where('status', $status);

        $orders = $q->paginate(15)->appends($request->only('status'));

        return view('admin.layout.orders', compact('orders', 'status'));
    }

    public function show(string $id)
    {
        $order = MongoOrder::where('_id', $id)->firstOrFail();
        return view('admin.layout.order_show', compact('order'));
    }

    // --- Actions ---
    public function confirm(string $id)
    {
        $order = MongoOrder::where('_id', $id)->firstOrFail();

        if (strtolower($order->status ?? 'pending') !== 'pending') {
            return back()->with('error', 'Only pending orders can be confirmed.');
        }

        $order->status = 'processing';
        $order->save();

        return redirect()->route('admin.orders.show', (string)$order->_id)
                         ->with('success', 'Order confirmed (now Processing).');
    }

    public function complete(string $id)
    {
        $order = MongoOrder::where('_id', $id)->firstOrFail();

        if (! in_array(strtolower($order->status ?? ''), ['processing', 'pending'])) {
            return back()->with('error', 'Only pending/processing orders can be completed.');
        }

        $order->status = 'completed';
        $order->save();

        return redirect()->route('admin.orders.show', (string)$order->_id)
                         ->with('success', 'Order marked as Completed.');
    }

    public function cancel(string $id)
    {
        $order = MongoOrder::where('_id', $id)->firstOrFail();

        if (strtolower($order->status ?? '') === 'completed') {
            return back()->with('error', 'Completed orders cannot be cancelled.');
        }

        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('admin.orders.show', (string)$order->_id)
                         ->with('success', 'Order cancelled.');
    }
}
