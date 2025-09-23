@extends('admin.layout.admin')

@section('page_title','Products')

@section('content')
  @php $hasQ = strlen(request('q','')) > 0; @endphp

  {{-- Page header --}}
  <div class="flex items-center justify-between gap-4 mb-6">
    {{-- Left: Title --}}
    <h1 class="text-xl md:text-2xl font-bold tracking-tight text-slate-900">Products</h1>

    {{-- Right: Search + actions --}}
    <div class="flex items-center gap-3">
      {{-- Collapsible search (icon → slides open, outlined pill) --}}
      <form id="pageSearch"
            method="GET"
            action="{{ route('admin.products.index') }}"
            class="relative h-11 flex items-center overflow-hidden rounded-full
                   transition-all duration-300 ease-out
                   {{ $hasQ ? 'w-[28rem] bg-white border border-black pl-4 pr-4' : 'w-11 bg-transparent border border-transparent' }}">
        {{-- toggle icon --}}
        <button id="pageSearchBtn" type="button" aria-label="Open search" aria-expanded="{{ $hasQ ? 'true' : 'false' }}"
                class="shrink-0 h-11 w-11 grid place-items-center text-slate-700 hover:text-slate-900 focus:outline-none">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/>
          </svg>
        </button>

        {{-- input (slides in, full-width) --}}
        <input id="pageSearchInput" type="search" name="q" placeholder="Search…"
               value="{{ request('q') }}"
               class="appearance-none bg-transparent border-0 outline-none focus:ring-0 caret-black
                      text-sm placeholder:text-slate-400
                      transition-all duration-300 ease-out
                      {{ $hasQ ? 'w-full opacity-100 pl-1' : 'w-0 opacity-0 pl-0' }}" />

        {{-- keep current params except q --}}
        @foreach(request()->except('q') as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? implode(',', $v) : $v }}">
        @endforeach
      </form>

      {{-- Delete --}}
      <button id="deleteSelectedBtn" type="submit" form="bulkDeleteForm"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-600 text-white shadow-sm hover:bg-rose-500 disabled:opacity-50 disabled:cursor-not-allowed"
              disabled>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0V5a2 2 0 012-2h2a2 2 0 012 2v2"/>
        </svg>
        Delete Selected
      </button>

      {{-- Add --}}
      <a href="{{ route('admin.products.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white shadow-sm hover:bg-indigo-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12M6 12h12"/>
        </svg>
        Add Product
      </a>
    </div>
  </div>

  {{-- Bulk delete form + table --}}
  <form id="bulkDeleteForm" method="POST" action="{{ route('admin.products.bulk-destroy') }}">
    @csrf
    @method('DELETE')

    <div class="bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
          <thead class="bg-slate-50 text-slate-600 text-sm">
            <tr>
              <th class="px-4 py-3 w-10">
                <input id="selectAll" type="checkbox" class="rounded border-slate-300">
              </th>
              <th class="px-4 py-3 text-left w-14">Image</th>  {{-- NEW --}}
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
                if ($raw) {
                  $img = \Illuminate\Support\Str::startsWith($raw, 'data:image')
                      ? $raw
                      : 'data:image/png;base64,' . $raw;
                } else {
                  $img = 'https://placehold.co/64x64/png';
                }
              @endphp
              <tr class="hover:bg-slate-50/60">
                <td class="px-4 py-3">
                  <input type="checkbox" name="ids[]" value="{{ (string) $p->_id }}"
                         class="rowCheck rounded border-slate-300">
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
                <td class="px-4 py-3">{{ $p->price }}</td>
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
                <td class="px-4 py-8 text-slate-500" colspan="10">No products yet.</td> {{-- colspan +1 for image --}}
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </form>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Select-all + Delete button enable
      const selectAll = document.getElementById('selectAll');
      const checks    = Array.from(document.querySelectorAll('.rowCheck'));
      const delBtn    = document.getElementById('deleteSelectedBtn');

      const refresh = () => {
        delBtn.disabled = !checks.some(c => c.checked);
        if (checks.length) selectAll.checked = checks.every(c => c.checked);
      };

      selectAll?.addEventListener('change', () => {
        checks.forEach(c => c.checked = selectAll.checked);
        refresh();
      });
      checks.forEach(c => c.addEventListener('change', refresh));
      refresh();

      // Sliding search — outlined pill
      const wrap  = document.getElementById('pageSearch');
      const btn   = document.getElementById('pageSearchBtn');
      const input = document.getElementById('pageSearchInput');

      let open = {!! $hasQ ? 'true' : 'false' !!};

      const openSearch = () => {
        open = true;
        wrap.classList.remove('w-11','bg-transparent','border-transparent');
        wrap.classList.add('w-[28rem]','bg-white','border','border-black','pl-4','pr-4');
        input.classList.remove('w-0','opacity-0','pl-0');
        input.classList.add('w-full','opacity-100','pl-1');
        btn.setAttribute('aria-expanded','true');
        setTimeout(() => input.focus(), 140);
      };

      const closeSearch = () => {
        if (!input.value) {
          open = false;
          wrap.classList.add('w-11','bg-transparent','border','border-transparent');
          wrap.classList.remove('w-[28rem]','bg-white','border-black','pl-4','pr-4');
          input.classList.add('w-0','opacity-0','pl-0');
          input.classList.remove('w-full','opacity-100','pl-1');
          btn.setAttribute('aria-expanded','false');
        }
      };

      btn.addEventListener('click', () => (open ? closeSearch() : openSearch()));
      document.addEventListener('click', (e) => { if (open && !wrap.contains(e.target)) closeSearch(); });
      document.addEventListener('keydown', (e) => { if (open && e.key === 'Escape') closeSearch(); });

      if (open) openSearch();
    });
  </script>
@endsection
