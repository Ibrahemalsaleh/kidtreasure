<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('تفاصيل الطلب') }} #{{ $order->order_number }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                عودة للقائمة
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">معلومات الطلب</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">رقم الطلب</p>
                            <p class="font-medium">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">تاريخ الطلب</p>
                            <p class="font-medium">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">حالة الطلب</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->order_status === 'delivered') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ __('orders.status.' . $order->order_status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">حالة الدفع</p>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ __('orders.payment_status.' . $order->payment_status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">معلومات العميل</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">اسم العميل</p>
                            <p class="font-medium">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                            <p class="font-medium">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">رقم الهاتف</p>
                            <p class="font-medium">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">عناصر الطلب</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المجموع</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($item->product_image)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}">
                                                @endif
                                                <div class="mr-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->unit_price_jod, 2) }} د.أ</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->subtotal_jod, 2) }} د.أ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">المجموع الفرعي</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($order->total_amount_jod - $order->shipping_cost + $order->discount_amount, 2) }} د.أ</td>
                                </tr>
                                @if($order->shipping_cost > 0)
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">تكلفة الشحن</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($order->shipping_cost, 2) }} د.أ</td>
                                    </tr>
                                @endif
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-right font-medium">الخصم</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">-{{ number_format($order->discount_amount, 2) }} د.أ</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-medium">المجموع الكلي</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ number_format($order->total_amount_jod, 2) }} د.أ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">تحديث حالة الطلب</h3>
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="order_status" class="block text-sm font-medium text-gray-700">حالة الطلب</label>
                                <select name="order_status" id="order_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                    <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700">حالة الدفع</label>
                                <select name="payment_status" id="payment_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>مدفوع</option>
                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>فشل</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                تحديث الحالة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
