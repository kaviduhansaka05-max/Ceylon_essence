<div>
  {{-- Page header --}}
  <div class="flex items-center justify-between gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-slate-900">Products</h1>

    <div class="flex items-center gap-3">
      {{-- Search --}}
      <input type="search" wire:model.debounce.500ms="q"
             placeholder="Search products…"
             class="rounded-full border px-4 py-2 text-sm focus:border-slate-500 focus:ring-slate-500"/>

      {{-- Add --}}
      <a href="{{ route('admin.products.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white shadow-sm hover:bg-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/>
        </svg>
        Add Product
      </a>
    </div>
  </div>

  {{-- ✅ Delete Selected Form --}}
  <form method="POST" action="{{ route('admin.products.bulk-destroy') }}">
    @csrf
    @method('DELETE')

    <div class="mb-3">
      <button type="submit"
        onclick="return confirm('Are you sure you want to delete selected products?')"
        class="px-4 py-2 bg-rose-600 text-white rounded-full hover:bg-rose-500">
        Delete Selected
      </button>
    </div>

    {{-- ✅ Desktop Table --}}
    <div class="hidden md:block bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-slate-50 text-slate-600 text-sm">
            <tr>
              <th class="px-4 py-3 text-left w-10">
                <input type="checkbox" id="select-all">
              </th>
              <th class="px-4 py-3 text-left w-14">Image</th>
              <th class="px-4 py-3 text-left">Name</th>
              <th class="px-4 py-3 text-left">Category</th>
              <th class="px-4 py-3 text-left">Description</th>
              <th class="px-4 py-3 text-left">Size</th>
              <th class="px-4 py-3 text-left">Inventory</th>
              <th class="px-4 py-3 text-left">Price</th>
              <th class="px-4 py-3 text-left">Status</th>
              <th class="px-4 py-3 text-left w-24">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
            @forelse($products as $p)
              @php
                $raw = $p->image ?? null;
                $img = $raw
                  ? (\Illuminate\Support\Str::startsWith($raw, 'data:image') ? $raw : 'data:image/png;base64,' . $raw)
                  : 'https://placehold.co/64x64/png';
              @endphp
              <tr class="hover:bg-slate-50/60">
                <td class="px-4 py-2">
                  <input type="checkbox" name="ids[]" value="{{ $p->_id }}" class="product-checkbox">
                </td>
                <td class="px-4 py-2">
                  <img src="{{ $img }}" alt="{{ $p->name }}"
                       class="h-10 w-10 rounded object-cover ring-1 ring-slate-200">
                </td>
                <td class="px-4 py-3 font-medium">{{ $p->name }}</td>
                <td class="px-4 py-3">{{ $p->category }}</td>
                <td class="px-4 py-3 max-w-[420px] truncate" title="{{ $p->description }}">{{ $p->description }}</td>
                <td class="px-4 py-3">{{ $p->size }}</td>
                <td class="px-4 py-3">{{ $p->inventory }}</td>
                <td class="px-4 py-3">${{ number_format((float) $p->price, 2) }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ strtolower($p->status) === 'instock'
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-rose-100 text-rose-700' }}">
                    {{ $p->status }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('admin.products.edit', (string) $p->_id) }}"
                     class="text-indigo-700 hover:text-indigo-900 underline">Edit</a>
                </td>
              </tr>
            @empty
              <tr>
                <td class="px-4 py-8 text-slate-500" colspan="10">No products yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3">
        {{ $products->links() }}
      </div>
    </div>

    {{-- ✅ Mobile Cards --}}
    <div class="space-y-4 md:hidden">
      @forelse($products as $p)
        @php
          $raw = $p->image ?? null;
          $img = $raw
            ? (\Illuminate\Support\Str::startsWith($raw, 'data:image') ? $raw : 'data:image/png;base64,' . $raw)
            : 'https://placehold.co/64x64/png';
        @endphp
        <div class="bg-white rounded-xl shadow p-4 space-y-2">
          <div class="flex gap-3 items-center">
            <img src="{{ $img }}" class="h-14 w-14 rounded object-cover ring-1 ring-slate-200" alt="{{ $p->name }}">
            <div>
              <div class="font-semibold text-slate-900">{{ $p->name }}</div>
              <div class="text-xs text-slate-500">{{ $p->category }}</div>
            </div>
          </div>
          <div class="text-sm text-slate-700 line-clamp-3">{{ $p->description }}</div>
          <div class="flex justify-between items-center text-sm">
            <span>Size: {{ $p->size ?? '—' }}</span>
            <span>Qty: {{ $p->inventory }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="font-semibold text-indigo-700">${{ number_format((float) $p->price, 2) }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium
              {{ strtolower($p->status) === 'instock'
                  ? 'bg-emerald-100 text-emerald-700'
                  : 'bg-rose-100 text-rose-700' }}">
              {{ $p->status }}
            </span>
          </div>
          <div class="flex justify-end">
            <a href="{{ route('admin.products.edit', (string) $p->_id) }}"
               class="px-3 py-1 text-xs rounded bg-indigo-600 text-white hover:bg-indigo-500">
              Edit
            </a>
          </div>
        </div>
      @empty
        <p class="text-slate-500 text-sm">No products yet.</p>
      @endforelse
    </div>
  </form>
</div>

{{-- ✅ Select All JS --}}
<script>
  document.getElementById('select-all')?.addEventListener('change', function(e) {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = e.target.checked);
  });
</script>
