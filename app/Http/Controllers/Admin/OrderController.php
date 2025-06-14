<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function destroy(Order $order)
    {
        // حذف عناصر الطلب أولاً
        $order->items()->delete();
        // ثم حذف الطلب
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'تم حذف الطلب بنجاح');
    }
}
