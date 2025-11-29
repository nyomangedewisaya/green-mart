<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Order; // Pastikan ada model Order
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar
        $query = Courier::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('service', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', $status);
        }

        $couriers = $query->latest()->paginate(10)->appends($request->query());

        // 2. Statistik Ringkasan
        $totalCouriers = Courier::count();
        $activeCouriers = Courier::where('is_active', 1)->count();
        $avgCost = Courier::avg('cost');

        // 3. Data untuk Chart (Populasi Kurir berdasarkan Pesanan)
        // Mengambil kolom 'shipping_courier' dari tabel orders
        $courierStats = Order::select('shipping_courier', DB::raw('count(*) as total'))
            ->groupBy('shipping_courier')
            ->orderByDesc('total')
            ->take(5) // Top 5
            ->get();

        $chartLabels = $courierStats->pluck('shipping_courier')->toArray();
        $chartData = $courierStats->pluck('total')->toArray();

        // Jika data kosong (belum ada order), isi dummy biar chart tetap muncul (opsional, visual only)
        if(empty($chartLabels)) {
            $chartLabels = ['Belum Ada Data'];
            $chartData = [0];
        }

        return view('admin.couriers.index', compact(
            'couriers', 'totalCouriers', 'activeCouriers', 'avgCost', 
            'chartLabels', 'chartData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:50',
            'service'    => 'required|string|max:50',
            'cost'       => 'required|numeric|min:0',
            'estimation' => 'required|string|max:50',
        ]);

        $slug = Str::slug($request->name . '-' . $request->service);

        Courier::create([
            'name'       => $request->name,
            'service'    => $request->service,
            'slug'       => $slug, 
            'cost'       => $request->cost,
            'estimation' => $request->estimation,
            'is_active'  => true,
        ]);

        return back()->with('success', 'Kurir baru berhasil ditambahkan.');
    }

    public function update(Request $request, Courier $courier)
    {
        if ($request->has('toggle_active')) {
            $courier->update(['is_active' => !$courier->is_active]);
            $status = $courier->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Layanan kurir berhasil $status.");
        }

        $request->validate([
            'name'       => 'required|string|max:50',
            'service'    => 'required|string|max:50',
            'cost'       => 'required|numeric|min:0',
            'estimation' => 'required|string|max:50',
        ]);

        $data = [
            'name'       => $request->name,
            'service'    => $request->service,
            'cost'       => $request->cost,
            'estimation' => $request->estimation,
        ];

        if ($request->name !== $courier->name || $request->service !== $courier->service) {
            $data['slug'] = Str::slug($request->name . '-' . $request->service);
        }

        $courier->update($data);

        return back()->with('success', 'Data kurir berhasil diperbarui.');
    }
}