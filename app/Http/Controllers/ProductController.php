<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MongoProduct;
use MongoDB\BSON\ObjectId;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // --- read filters from querystring ---
        $category     = trim((string) $request->query('category', ''));
        $minPriceRaw  = $request->query('min_price');
        $maxPriceRaw  = $request->query('max_price');
        $availability = (array) $request->query('availability', []); // e.g. ['Instock','Out of Stock']

        $minPrice = is_numeric($minPriceRaw) ? (float) $minPriceRaw : null;
        $maxPrice = is_numeric($maxPriceRaw) ? (float) $maxPriceRaw : null;

        // --- build query on Mongo Eloquent ---
        $q = MongoProduct::query();

        if ($category !== '') {
            $q->where('category', $category);
        }

        if ($minPrice !== null && $maxPrice !== null && $minPrice <= $maxPrice) {
            $q->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            $q->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            $q->where('price', '<=', $maxPrice);
        }

        // normalize availability values
        $norm = array_map(fn($v) => strtolower(trim($v)), $availability);
        $statuses = [];
        if (in_array('instock', $norm, true))      $statuses[] = 'Instock';
        if (in_array('out of stock', $norm, true) || in_array('out_of_stock', $norm, true) || in_array('outofstock', $norm, true)) {
            $statuses[] = 'Out of Stock';
        }
        if ($statuses) {
            $q->whereIn('status', $statuses);
        }

        $products = $q->get();

        // Distinct categories for the filter select
        $categories = [];
        try {
            $categories = DB::connection('mongodb')
                ->getMongoDB()
                ->selectCollection('products')
                ->distinct('category') ?? [];
            sort($categories);
        } catch (\Throwable $e) {
            // fallback: derive from current products (if any)
            $categories = $products->pluck('category')->filter()->unique()->sort()->values()->all();
        }

        return view('products', compact('products', 'categories'));
    }

    public function raw()
    {
        $conn = DB::connection('mongodb');
        $db   = $conn->getMongoDB();
        $col  = $db->selectCollection('products');

        $cursor = $col->find([], ['limit' => 3, 'sort' => ['_id' => 1]]);
        $rows   = array_map(fn($d) => json_decode(json_encode($d), true), iterator_to_array($cursor));

        return response()->json([
            'db'    => $conn->getDatabaseName(),
            'count' => $col->countDocuments(),
            'docs'  => $rows,
        ]);
    }

    // detail page (unchanged)
    public function show(string $id)
    {
        $product = MongoProduct::where('_id', $id)->first();

        if (!$product && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $doc = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->findOne(['_id' => new ObjectId($id)]);
            if ($doc) $product = (new MongoProduct())->newFromBuilder((array) $doc);
        }

        abort_unless($product, 404);
        return view('product_show', ['product' => $product]);
    }
}
