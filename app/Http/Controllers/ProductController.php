<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MongoProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;          // make sure this is present
use Carbon\Carbon;

class ProductController extends Controller
{
    // ----------------------------
    // WEB (your existing methods)
    // ----------------------------

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = MongoProduct::query();

        if ($q !== '') {
            $rx = new Regex(preg_quote($q, '/'), 'i');

            $query->where(function ($sub) use ($rx, $q) {
                $sub->where('name',        'regex', $rx)
                    ->orWhere('category',  'regex', $rx)
                    ->orWhere('description','regex', $rx)
                    ->orWhere('size',      'regex', $rx)
                    ->orWhere('status',    'regex', $rx);

                if (is_numeric($q)) {
                    $sub->orWhere('price',     (float) $q)
                        ->orWhere('inventory', (int) $q);
                }
            });
        }

        $products = $query->orderBy('name')->get();

        return view('admin.layout.products', compact('products'));
    }

    public function create()
    {
        return view('admin.layout.products_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            '_id'         => ['nullable','regex:/^[a-f0-9]{24}$/i'],
            'name'        => ['required','string','max:255'],
            'category'    => ['required','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'size'        => ['nullable','string','max:100'],
            'inventory'   => ['required','integer','min:0'],
            'price'       => ['required','numeric','min:0'],
            'status'      => ['required','in:Instock,Out of Stock'],
            'sold_pieces' => ['required','integer','min:0'],

            // For WEB: image via path or file
            'image_path'  => ['nullable','string','max:1024','required_without:image_file'],
            'image_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
        ]);

        $imageB64 = $this->resolveImageBase64FromWeb($request, $data);

        $id  = $data['_id'] ?? (string) new ObjectId();
        $now = Carbon::now();

        MongoProduct::create([
            '_id'         => $id,
            'name'        => $data['name'],
            'category'    => $data['category'],
            'description' => $data['description'] ?? null,
            'size'        => $data['size'] ?? null,
            'inventory'   => (int) $data['inventory'],
            'price'       => (float) $data['price'],
            'status'      => $data['status'],
            'sold_pieces' => (int) $data['sold_pieces'],
            'image'       => $imageB64,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required','array'],
            'ids.*' => ['string'],
        ]);

        $ids = array_values(array_unique($data['ids']));

        $deleted1 = MongoProduct::whereIn('_id', $ids)->delete();

        $hexIds   = array_filter($ids, fn($id) => preg_match('/^[a-f0-9]{24}$/i', $id));
        $deleted2 = 0;
        if ($hexIds) {
            try {
                $col = DB::connection('mongodb')->getMongoDB()->selectCollection('products');
                $result = $col->deleteMany([
                    '_id' => ['$in' => array_map(fn($id) => new ObjectId($id), $hexIds)]
                ]);
                $deleted2 = $result->getDeletedCount();
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $total = $deleted1 + $deleted2;

        return redirect()->route('admin.products.index')
            ->with('success', $total > 0 ? "Deleted {$total} product(s)." : 'No products were deleted.');
    }

    public function edit(string $id)
    {
        $product = MongoProduct::where('_id', $id)->first();

        if (!$product && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $doc = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->findOne(['_id' => new ObjectId($id)]);
            if ($doc) { $product = new MongoProduct((array) $doc); }
        }

        abort_unless($product, 404);

        return view('admin.layout.products_edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        return $this->updateproduct($request, $id);
    }

    public function updateproduct(Request $request, string $id)
    {
        $data = $request->validate([
            'name'        => ['sometimes','required','string','max:255'],
            'category'    => ['sometimes','required','string','max:255'],
            'description' => ['sometimes','nullable','string','max:2000'],
            'size'        => ['sometimes','nullable','string','max:100'],
            'inventory'   => ['sometimes','required','integer','min:0'],
            'price'       => ['sometimes','required','numeric','min:0'],
            'status'      => ['sometimes','required','in:Instock,Out of Stock'],
            'sold_pieces' => ['sometimes','required','integer','min:0'],

            // WEB uploads
            'image_path'  => ['nullable','string','max:1024'],
            'image_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
        ]);

        $update = [];
        foreach (['name','category','description','size','inventory','price','status','sold_pieces'] as $f) {
            if (array_key_exists($f, $data)) {
                if (in_array($f, ['inventory','sold_pieces'])) {
                    $update[$f] = (int) $data[$f];
                } elseif ($f === 'price') {
                    $update[$f] = (float) $data[$f];
                } else {
                    $update[$f] = $data[$f];
                }
            }
        }

        // image (optional)
        $imageB64 = $this->resolveImageBase64FromWeb($request, $data, true);
        if ($imageB64 !== null) {
            $update['image'] = $imageB64;
        }

        $update['updated_at'] = Carbon::now();

        $updated = MongoProduct::where('_id', $id)->update($update);

        if (!$updated && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $res = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->updateOne(['_id' => new ObjectId($id)], ['$set' => $update]);
            $updated = $res->getModifiedCount();
        }

        return redirect()->route('admin.products.index')
            ->with('success', $updated ? 'Product updated.' : 'No changes were made.');
    }

    // ----------------------------
    // API (JSON) methods
    // ----------------------------

    // GET /api/v1/products
    public function apiIndex(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $perPage  = (int) $request->get('per_page', 20);
        $perPage  = max(1, min($perPage, 100));

        $query = MongoProduct::query();

        if ($q !== '') {
            $rx = new Regex(preg_quote($q, '/'), 'i');

            $query->where(function ($sub) use ($rx, $q) {
                $sub->where('name', 'regex', $rx)
                    ->orWhere('category', 'regex', $rx)
                    ->orWhere('description','regex', $rx)
                    ->orWhere('size', 'regex', $rx)
                    ->orWhere('status', 'regex', $rx);

                if (is_numeric($q)) {
                    $sub->orWhere('price', (float) $q)
                        ->orWhere('inventory', (int) $q);
                }
            });
        }

        $paginator = $query->orderBy('name')->paginate($perPage)->appends($request->query());

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }

    // GET /api/v1/products/{id}
    public function apiShow(string $id)
    {
        $product = MongoProduct::where('_id', $id)->first();

        if (!$product && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $doc = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->findOne(['_id' => new ObjectId($id)]);
            if ($doc) { $product = new MongoProduct((array) $doc); }
        }

        if (! $product) return response()->json(['message' => 'Not found'], 404);

        return response()->json($product);
    }

    // POST /api/v1/products
    public function apiStore(Request $request)
    {
        $data = $request->validate([
            '_id'         => ['nullable','regex:/^[a-f0-9]{24}$/i'],
            'name'        => ['required','string','max:255'],
            'category'    => ['required','string','max:255'],
            'description' => ['nullable','string','max:2000'],
            'size'        => ['nullable','string','max:100'],
            'inventory'   => ['required','integer','min:0'],
            'price'       => ['required','numeric','min:0'],
            'status'      => ['required','in:Instock,Out of Stock'],
            'sold_pieces' => ['required','integer','min:0'],

            // API accepts file | base64 | path
            'image_file'   => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'image_base64' => ['nullable','string'],
            'image_path'   => ['nullable','string','max:1024'],
        ]);

        $imageB64 = $this->resolveImageBase64FromApi($request, $data);

        $id  = $data['_id'] ?? (string) new ObjectId();
        $now = Carbon::now();

        $product = MongoProduct::create([
            '_id'         => $id,
            'name'        => $data['name'],
            'category'    => $data['category'],
            'description' => $data['description'] ?? null,
            'size'        => $data['size'] ?? null,
            'inventory'   => (int) $data['inventory'],
            'price'       => (float) $data['price'],
            'status'      => $data['status'],
            'sold_pieces' => (int) $data['sold_pieces'],
            'image'       => $imageB64,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        return response()->json($product, 201);
    }

    // PUT/PATCH /api/v1/products/{id}
    public function apiUpdate(Request $request, string $id)
    {
        $data = $request->validate([
            'name'        => ['sometimes','required','string','max:255'],
            'category'    => ['sometimes','required','string','max:255'],
            'description' => ['sometimes','nullable','string','max:2000'],
            'size'        => ['sometimes','nullable','string','max:100'],
            'inventory'   => ['sometimes','required','integer','min:0'],
            'price'       => ['sometimes','required','numeric','min:0'],
            'status'      => ['sometimes','required','in:Instock,Out of Stock'],
            'sold_pieces' => ['sometimes','required','integer','min:0'],

            'image_file'   => ['nullable','file','mimes:jpg,jpeg,png,webp,gif','max:5120'],
            'image_base64' => ['nullable','string'],
            'image_path'   => ['nullable','string','max:1024'],
        ]);

        $update = [];
        foreach (['name','category','description','size','inventory','price','status','sold_pieces'] as $f) {
            if ($request->has($f)) {
                if (in_array($f, ['inventory','sold_pieces'])) $update[$f] = (int) $data[$f];
                elseif ($f === 'price')                       $update[$f] = (float) $data[$f];
                else                                          $update[$f] = $data[$f];
            }
        }

        $imageB64 = $this->resolveImageBase64FromApi($request, $data, true);
        if ($imageB64 !== null) $update['image'] = $imageB64;

        $update['updated_at'] = Carbon::now();

        $updated = MongoProduct::where('_id', $id)->update($update);

        if (! $updated && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $res = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->updateOne(['_id' => new ObjectId($id)], ['$set' => $update]);
            $updated = $res->getModifiedCount();
        }

        if (! $updated) return response()->json(['message' => 'Not found or no changes'], 404);

        $product = MongoProduct::where('_id', $id)->first();
        return response()->json($product);
    }

    // DELETE /api/v1/products/{id}
    public function apiDestroy(string $id)
    {
        $deleted = MongoProduct::where('_id', $id)->delete();

        if (! $deleted && preg_match('/^[a-f0-9]{24}$/i', $id)) {
            $res = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->deleteOne(['_id' => new ObjectId($id)]);
            $deleted = $res->getDeletedCount();
        }

        if (! $deleted) return response()->json(['deleted' => false, 'message' => 'Not found'], 404);

        return response()->json(['deleted' => true]);
    }

    // DELETE /api/v1/products   body: { "ids": [...] }
    public function apiBulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids'   => ['required','array','min:1'],
            'ids.*' => ['string'],
        ]);

        $ids    = array_values(array_unique($data['ids']));
        $hexIds = array_filter($ids, fn($x) => preg_match('/^[a-f0-9]{24}$/i', $x));

        $deleted1 = MongoProduct::whereIn('_id', $ids)->delete();

        $deleted2 = 0;
        if ($hexIds) {
            $res = DB::connection('mongodb')->getMongoDB()
                ->selectCollection('products')
                ->deleteMany(['_id' => ['$in' => array_map(fn($i) => new ObjectId($i), $hexIds)]]);
            $deleted2 = $res->getDeletedCount();
        }

        return response()->json(['deleted' => $deleted1 + $deleted2]);
    }

    /**
     * GET /api/v1/top-sellers
     * Returns top 5 products by sold_pieces with the shape your frontend expects.
     */
   public function apiTopSellers(Request $request)
{
    // Fetch products ordered by sold_pieces
    $products = MongoProduct::query()
        ->orderByDesc('sold_pieces')
        ->orderBy('_id') // stable tie-breaker
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

    // If <5 products exist, just return what we have
    return response()->json(['data' => $data]);
}

    // ----------------------------
    // Helpers (WEB/API image)
    // ----------------------------

    private function resolveImageBase64FromWeb(Request $request, array $data, bool $optional = false): ?string
    {
        // precedence: file > path (WEB forms)
        if ($request->hasFile('image_file')) {
            return base64_encode(file_get_contents($request->file('image_file')->getRealPath()));
        }

        if (!empty($data['image_path'] ?? null)) {
            $rel = ltrim($data['image_path'], '/\\');
            $candidates = [
                public_path($rel),
                storage_path("app/$rel"),
                storage_path("app/public/$rel"),
                base_path($rel),
            ];
            foreach ($candidates as $p) {
                if (is_file($p) && is_readable($p)) {
                    return base64_encode(file_get_contents($p));
                }
            }
            if (!$optional) {
                abort(back()->withErrors(['image_path' => "File not found or unreadable: {$data['image_path']}"])->withInput());
            }
        }

        return $optional ? null : null;
    }

    private function resolveImageBase64FromApi(Request $request, array $data, bool $optional = false): ?string
    {
        // precedence: file > base64 (data URL or raw) > path
        if ($request->hasFile('image_file')) {
            return base64_encode(file_get_contents($request->file('image_file')->getRealPath()));
        }

        if (!empty($data['image_base64'] ?? null)) {
            $val = $data['image_base64'];
            if (str_starts_with($val, 'data:image')) {
                $parts = explode(',', $val, 2);
                return $parts[1] ?? null;
            }
            return $val;
        }

        if (!empty($data['image_path'] ?? null)) {
            $rel = ltrim($data['image_path'], '/\\');
            $candidates = [
                public_path($rel),
                storage_path("app/$rel"),
                storage_path("app/public/$rel"),
                base_path($rel),
            ];
            foreach ($candidates as $p) {
                if (is_file($p) && is_readable($p)) {
                    return base64_encode(file_get_contents($p));
                }
            }
            if (!$optional) {
                return abort(response()->json([
                    'message' => "File not found or unreadable: {$data['image_path']}"
                ], 422));
            }
        }

        return $optional ? null : null;
    }
}
