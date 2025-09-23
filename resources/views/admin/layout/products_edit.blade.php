@extends('admin.layout.admin')

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Edit Product</h1>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.products.index') }}"
         class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
      <button type="submit" form="productEditForm"
              class="inline-flex items-center gap-2 px-4 py-2 rounded bg-slate-900 text-white hover:bg-slate-700">
        Save Changes
      </button>
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-6 rounded-lg bg-red-50 text-red-800 px-4 py-3">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form id="productEditForm"
        method="POST"
        action="{{ route('admin.products.update', (string) $product->_id) }}"
        enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow ring-1 ring-gray-200 p-6 md:p-8 max-w-4xl mx-auto">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">_id</label>
        <input value="{{ (string) $product->_id }}" disabled
               class="mt-1 w-full rounded border-gray-200 bg-gray-50 text-gray-600">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" value="{{ old('name', $product->name) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <input name="category" value="{{ old('category', $product->category) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      {{-- Description --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4"
                  class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                  placeholder="Short description...">{{ old('description', $product->description) }}</textarea>
      </div>

      {{-- Size --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Size</label>
        <input name="size" value="{{ old('size', $product->size) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
               placeholder="100ml">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Inventory</label>
        <input type="number" name="inventory" min="0"
               value="{{ old('inventory', $product->inventory) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" step="any" name="price" min="0"
               value="{{ old('price', $product->price) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status"
                class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
          @php $st = old('status', $product->status); @endphp
          <option {{ $st==='Instock' ? 'selected' : '' }}>Instock</option>
          <option {{ $st==='Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Sold Pieces</label>
        <input type="number" name="sold_pieces" min="0"
               value="{{ old('sold_pieces', $product->sold_pieces) }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Image Path (relative)</label>
        <input name="image_path" value="{{ old('image_path') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
               placeholder="images/products/chanel.jpg">
        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Or Upload New Image File</label>
        <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
        <p class="text-xs text-gray-500 mt-1">If both are provided, uploaded file takes priority.</p>
      </div>
    </div>
  </form>
@endsection
