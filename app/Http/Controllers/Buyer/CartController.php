<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with(['product.seller'])
            ->where('user_id', Auth::id())
            ->whereHas('product')
            ->get()
            ->groupBy(function ($cart) {
                return $cart->product->seller->name;
            });

        return view('buyer.cart.index', compact('carts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = Cart::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->increment('quantity', $request->quantity);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Cart $cart)
    {
        // Validasi Stok
        if ($request->quantity > $cart->product->stock) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Stok hanya tersisa ' . $cart->product->stock,
                ],
                422,
            );
        }

        if ($request->quantity < 1) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'Minimal pembelian 1',
                ],
                422,
            );
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'status' => 'success',
            'message' => 'Keranjang diperbarui',
        ]);
    }

    // DESTROY (AJAX)
    public function destroy(Cart $cart)
    {
        $cart->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Produk dihapus',
        ]);
    }
}
