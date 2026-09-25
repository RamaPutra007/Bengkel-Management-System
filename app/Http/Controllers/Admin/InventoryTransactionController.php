<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Sparepart;
use App\Http\Requests\StoreInventoryTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['sparepart', 'user'])->latest();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('sparepart', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15);
        return view('admin.inventory_transaction.index', compact('transactions'));
    }

    public function create()
    {
        $spareparts = Sparepart::orderBy('name')->get();
        return view('admin.inventory_transaction.create', compact('spareparts'));
    }

    public function store(StoreInventoryTransactionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        DB::transaction(function () use ($data) {
            $transaction = InventoryTransaction::create($data);
            
            $sparepart = Sparepart::lockForUpdate()->findOrFail($data['sparepart_id']);
            
            if ($data['type'] === 'in') {
                $sparepart->stock += $data['quantity'];
            } elseif ($data['type'] === 'out' || $data['type'] === 'adjustment') {
                if ($sparepart->stock < $data['quantity']) {
                    throw new \Exception("Stok tidak mencukupi. Sisa stok: " . $sparepart->stock);
                }
                $sparepart->stock -= $data['quantity'];
            }
            
            $sparepart->save();
        });

        return redirect()->route('admin.inventory-transaction.index')->with('success', 'Pergerakan stok berhasil dicatat.');
    }
}
