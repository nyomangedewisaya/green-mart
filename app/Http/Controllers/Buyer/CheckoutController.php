<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Courier;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        $cartIds = $request->input('cart_ids');

        if (empty($cartIds)) {
            return back()->with('error', 'Pilih minimal satu produk.');
        }

        $selectedCarts = Cart::with(['product.seller'])
            ->whereIn('id', $cartIds)
            ->where('user_id', Auth::id())
            ->get();

        // Hitung total barang saja (Subtotal Awal)
        $subtotalProduct = $selectedCarts->sum(function($item) {
            $price = $item->product->price - ($item->product->price * ($item->product->discount / 100));
            return $price * $item->quantity;
        });

        $groupedCarts = $selectedCarts->groupBy(fn($item) => $item->product->seller->id);

        $couriers = Courier::where('is_active', true)->get();

        return view('buyer.checkout.create', compact('groupedCarts', 'cartIds', 'subtotalProduct', 'couriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address'        => 'required|string|max:500',
            'phone'          => 'required|string|max:20',
            'payment_method' => 'required|string',
            'cart_ids'       => 'required|array',
            // Validasi shipping array: shipping[seller_id]
            'shipping'       => 'required|array', 
        ]);

        $user = Auth::user();
        
        // 1. FITUR SIMPAN ALAMAT (Jika dicentang)
        if ($request->has('save_address')) {
            $user->update([
                'address' => $request->address,
                'phone'   => $request->phone
            ]);
        }

        $cartIds = $request->cart_ids;
        $cartItems = Cart::with('product')->whereIn('id', $cartIds)->where('user_id', $user->id)->get();
        
        if ($cartItems->isEmpty()) return redirect()->route('buyer.cart.index')->with('error', 'Keranjang kosong.');

        $groupedItems = $cartItems->groupBy(fn($item) => $item->product->seller_id);
        $shippingData = $request->shipping; // Array [seller_id => 'CourierName|Cost']

        DB::transaction(function () use ($groupedItems, $request, $user, $cartIds, $shippingData) {
            
            foreach ($groupedItems as $sellerId => $items) {
                
                // Parse Data Kurir (Format: "JNE Reguler|12000")
                $shippingInfo = explode('|', $shippingData[$sellerId]); 
                $courierName = $shippingInfo[0] ?? 'Kurir Toko';
                $shippingCost = (int) ($shippingInfo[1] ?? 0);

                // Hitung Subtotal Produk
                $productTotal = 0;
                foreach ($items as $item) {
                    $price = $item->product->price - ($item->product->price * ($item->product->discount / 100));
                    $productTotal += ($price * $item->quantity);
                }

                // Total Amount = Produk + Ongkir + Biaya Layanan (Misal 1000)
                $grandTotal = $productTotal + $shippingCost + 1000;

                $orderCode = 'GM-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));

                $order = Order::create([
                    'user_id'          => $user->id,
                    'seller_id'        => $sellerId,
                    'order_code'       => $orderCode,
                    'total_amount'     => $grandTotal,
                    'status'           => 'pending',
                    'payment_method'   => $request->payment_method,
                    'address'          => $request->address . ' (Telp: ' . $request->phone . ')',
                    
                    // SIMPAN DATA KURIR
                    'shipping_cost'    => $shippingCost,
                    'shipping_courier' => $courierName,
                    'shipping_service' => 'Standard', // Bisa dinamis nanti
                    
                    'order_date'       => now(),
                ]);

                foreach ($items as $item) {
                    $finalPrice = $item->product->price - ($item->product->price * ($item->product->discount / 100));
                    
                    OrderDetail::create([
                        'order_id'   => $order->id,
                        'product_id' => $item->product_id,
                        'quantity'   => $item->quantity,
                        'price'      => $finalPrice,
                        'subtotal'   => $finalPrice * $item->quantity,
                    ]);

                    $item->product->decrement('stock', $item->quantity);
                }

                // Notifikasi
                Notification::create([
                    'user_id' => $order->seller->user_id,
                    'target'  => 'personal',
                    'type'    => 'info',
                    'title'   => 'Pesanan Baru Masuk! 📦',
                    'message' => "Pesanan #{$orderCode} perlu dikirim via {$courierName}."
                ]);
            }

            Cart::whereIn('id', $cartIds)->delete();
        });

        return redirect()->route('buyer.orders.index')->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }
}