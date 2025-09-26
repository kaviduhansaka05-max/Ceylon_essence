<?php

namespace App\Http\Controllers;

use App\Models\MongoProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;

class HomePageController extends Controller
{
    /**
     * GET /api/top-sellers
     * Returns top 5 products by sold_pieces for homepage.
     */
    public function apiTopSellers(Request $request)
    {
        $products = MongoProduct::query()
            ->orderByDesc('sold_pieces')
            ->orderBy('_id')
            ->limit(5)
            ->get(['_id','name','price','category','image','sold_pieces']);

        $host = $request->getSchemeAndHttpHost();

        $data = $products->map(function ($p) use ($host) {
            $img = $p->image ?? null;

            if (is_string($img) && str_starts_with($img, 'data:')) {
                $imageUrl = $img;
            } elseif (is_string($img) && preg_match('#^https?://#i', $img)) {
                $imageUrl = $img;
            } elseif (is_string($img) && preg_match('/\.(png|jpe?g|gif|webp)$/i', $img)) {
                $imageUrl = $host . '/images/' . ltrim($img, '/');
            } elseif (is_string($img) && $img !== '') {
                $imageUrl = 'data:image/png;base64,' . $img;
            } else {
                $imageUrl = $host . '/images/placeholder.jpg';
            }

            return [
                'id'         => (string) $p->_id,
                'name'       => $p->name ?? 'Unknown',
                'price'      => isset($p->price) ? (float) $p->price : null,
                'category'   => $p->category ?? null,
                'image_url'  => $imageUrl,
                'total_sold' => (int) ($p->sold_pieces ?? 0),
            ];
        })->values()->all();

        return response()->json(['data' => $data]);
    }

    public function show(string $id)
{
    $product = \App\Models\MongoProduct::where('_id', $id)->first();

    if (!$product && preg_match('/^[a-f0-9]{24}$/i', $id)) {
        $doc = \DB::connection('mongodb')->getMongoDB()
            ->selectCollection('products')
            ->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        if ($doc) {
            $product = new \App\Models\MongoProduct((array) $doc);
        }
    }

    abort_unless($product, 404);

    return view('product_show', compact('product'));
}

}
