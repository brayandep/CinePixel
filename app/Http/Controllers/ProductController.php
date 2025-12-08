<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductMovement;
use Barryvdh\DomPDF\Facade\Pdf;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtro por tipo de producto (regalo / snack)
        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Filtro por nombre (coincidencias)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where('name', 'like', "%{$search}%");
        }

        // Ordenar por nombre y paginar de 6 en 6
        $products = $query->orderBy('name')
                        ->paginate(6)
                        ->withQueryString(); // mantiene filtros en la paginación

        return view('productos.index', [
            'products'     => $products,
            'product_type' => $request->product_type,
            'search'       => $request->q,
        ]);
    }
    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:disponible,no_disponible'],
            'product_type'=> ['required', 'in:regalo,snack'],   // 👈 nuevo
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'max:2048'], // 2MB
        ], [
            'name.required'  => 'El nombre del producto es obligatorio.',
            'price.required' => 'El costo del producto es obligatorio.',
            'stock.required' => 'La cantidad del producto es obligatoria.',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // guarda en storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name'        => $data['name'],
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'status'      => $data['status'],
            'product_type' => $data['product_type'],   // 👈 nuevo
            'description' => $data['description'] ?? null,
            'image_path'  => $imagePath,
        ]);

        return redirect()
            ->route('products.create')
            ->with('success', 'Producto registrado correctamente.');
    }
    public function addProduct(Request $request, Product $product)
{
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    // AUMENTAR STOCK
    $product->stock += $request->quantity;
    $product->save();

    ProductMovement::create([
        'product_id'   => $product->id,
        'movement_type'=> 'entrada',
        'quantity'     => $request->quantity,
        'unit_price'   => $product->price,
        'total_price'  => $product->price * $request->quantity,
        'stock_after'  => $product->stock, // 🔥 IMPORTANTE
    ]);

    return redirect()->route('products.index')
        ->with('success', 'Producto agregado correctamente.');
}


public function sellProduct(Request $request, Product $product)
{
    $request->validate([
        'quantity' => 'required|integer|min:1|max:' . $product->stock,
    ]);

    // REDUCIR EL STOCK
    $product->stock -= $request->quantity;
    $product->save();

    ProductMovement::create([
        'product_id'   => $product->id,
        'movement_type'=> 'venta',
        'quantity'     => $request->quantity,
        'unit_price'   => $product->price,
        'total_price'  => $product->price * $request->quantity,
        'stock_after'  => $product->stock, // 🔥 IMPORTANTE
    ]);

    return redirect()->route('products.index')
        ->with('success', 'Producto vendido correctamente.');
}

    public function history(Request $request)
    {
        $query = ProductMovement::with('product');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('productos.historial', [
            'movements' => $movements,
            'from'      => $request->from,
            'to'        => $request->to,
            'movement_type' => $request->movement_type,
        ]);
    }
    public function historyPdf(Request $request)
{
    $query = ProductMovement::with('product');

    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    if ($request->filled('movement_type')) {
        $query->where('movement_type', $request->movement_type);
    }

    $movements = $query->orderBy('created_at', 'desc')->get();

    $pdf = Pdf::loadView('productos.historial_pdf', [
        'movements' => $movements
    ]);

    return $pdf->download('historial_productos.pdf');
}


}
