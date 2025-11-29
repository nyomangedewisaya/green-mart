<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        
        $userId = Auth::id();
        $productId = $request->product_id;

        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
            $message = 'Produk ditambahkan ke wishlist.';
        }

        return back()->with('success', $message);
    }
}