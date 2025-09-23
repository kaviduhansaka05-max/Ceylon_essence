@extends('admin.layout.admin')

@section('content')
  {{-- Page header (single action bar) --}}
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Add Product</h1>

    <div class="flex items-center gap-3">
      <a href="{{ route('admin.products.index') }}"
         class="px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel</a>
      <button type="submit" form="productCreateForm"
              class="inline-flex items-center gap-2 px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/>
        </svg>
        Create Product
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

  <form id="productCreateForm"
        method="POST"
        action="{{ route('admin.products.store') }}"
        enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow ring-1 ring-gray-200 p-6 md:p-8 max-w-4xl mx-auto">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">_id (optional)</label>
        <input name="_id" value="{{ old('_id') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
               placeholder="68cd3ec4aa2bf0194c3d7e5d">
        <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate an ObjectId.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" value="{{ old('name') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <input name="category" value="{{ old('category','Perfume') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      {{-- Description (textarea) --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4"
                  class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
                  placeholder="Short description...">{{ old('description') }}</textarea>
      </div>

      {{-- Size (text so you can store values like 100ml) --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Size</label>
        <input name="size" value="{{ old('size') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
               placeholder="100ml">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Inventory</label>
        <input type="number" name="inventory" value="{{ old('inventory',0) }}" min="0"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input type="number" step="any" name="price" value="{{ old('price',0) }}" min="0"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Status</label>
        <select name="status"
                class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
          <option {{ old('status')==='Instock' ? 'selected' : '' }}>Instock</option>
          <option {{ old('status')==='Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Sold Pieces</label>
        <input type="number" name="sold_pieces" value="{{ old('sold_pieces',0) }}" min="0"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Image Path (relative to /public or /storage/app)</label>
        <input name="image_path" value="{{ old('image_path') }}"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500"
               placeholder="images/products/chanel.jpg">
        <p class="text-xs text-gray-500 mt-1">Example: <code>images/products/chanel.jpg</code></p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Or Upload Image File</label>
        <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.gif"
               class="mt-1 w-full rounded border-gray-300 focus:border-slate-500 focus:ring-slate-500">
        <p class="text-xs text-gray-500 mt-1">If both are provided, uploaded file takes priority.</p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">created_at</label>
        <input value="{{ now() }}" disabled
               class="mt-1 w-full rounded border-gray-200 bg-gray-50 text-gray-600">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">updated_at</label>
        <input value="{{ now() }}" disabled
               class="mt-1 w-full rounded border-gray-200 bg-gray-50 text-gray-600">
      </div>
    </div>
  </form>
@endsection
