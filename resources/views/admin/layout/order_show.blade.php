@extends('admin.layout.admin')

@section('page_title','Order #'.(string)$order->_id)

@section('content')

  {{-- flash --}}
  @if (session('success'))
    <div class="mb-4 rounded-lg bg-emerald-100 text-emerald-800 px-4 py-2">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded-lg bg-rose-100 text-rose-800 px-4 py-2">{{ session('error') }}</div>
  @endif

  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">Order</h1>
      <div class="mt-1 text-sm text-slate-600">ID: <span class="font-mono">{{ (string)$order->_id }}</span></div>
      <div class="mt-1 text-sm text-slate-600">User: {{ $order->user_id ?? '—' }}</div>
    </div>

    <div class="text-right">
      @php $st = strtolower($order->status ?? 'pending'); @endphp
      <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
        @switch($st)
          @case('completed') bg-emerald-100 text-emerald-700 @break
          @case('processing') bg-sky-100 text-sky-700 @break
          @case('cancelled') bg-rose-100 text-rose-700 @break
          @default bg-amber-100 text-amber-700
        @endswitch">
        {{ ucfirst($order->status ?? 'pending') }}
      </span>

      <div class="mt-2 flex items-center gap-2 justify-end">

        {{-- Confirm (→ processing) --}}
        <form method="POST" action="{{ route('admin.orders.confirm', (string)$order->_id) }}">
          @csrf
          <button
            class="px-3 py-2 rounded bg-slate-900 text-white text-sm hover:bg-slate-700
                   {{ $st !== 'pending' ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ $st !== 'pending' ? 'disabled' : '' }}>
            Confirm order
          </button>
        </form>

        {{-- Complete --}}
        <form method="POST" action="{{ route('admin.orders.complete', (string)$order->_id) }}">
          @csrf
          <button
            class="px-3 py-2 rounded bg-emerald-600 text-white text-sm hover:bg-emerald-700
                   {{ in_array($st, ['completed','cancelled']) ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ in_array($st, ['completed','cancelled']) ? 'disabled' : '' }}>
            Mark completed
          </button>
        </form>

        {{-- Cancel --}}
        <form method="POST" action="{{ route('admin.orders.cancel', (string)$order->_id) }}">
          @csrf
          <button
            class="px-3 py-2 rounded bg-rose-600 text-white text-sm hover:bg-rose-700
                   {{ $st === 'completed' ? 'opacity-50 cursor-not-allowed' : '' }}"
            {{ $st === 'completed' ? 'disabled' : '' }}>
            Cancel
          </button>
        </form>

        <a href="{{ route('admin.orders.index') }}"
           class="px-3 py-2 rounded border text-sm border-gray-300 hover:bg-gray-50">
          Back
        </a>
      </div>
    </div>
  </div>

  {{-- Quick stats --}}
  @php
    $items = $order->items ?? collect();
    // if it's an Eloquent embedded collection, ensure we can sum
    $items = is_iterable($items) ? collect($items) : collect();
    $itemTypes = $items->count();
    $units     = $items->sum(fn($i) => (int)($i->quantity ?? 0));
    $total     = (float)($order->total ?? 0);
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl bg-white p-4 shadow ring-1 ring-black/5">
      <div class="text-sm text-slate-500">Item types</div>
      <div class="text-2xl font-bold">{{ $itemTypes }}</div>
    </div>
    <div class="rounded-2xl bg-white p-4 shadow ring-1 ring-black/5">
      <div class="text-sm text-slate-500">Total units</div>
      <div class="text-2xl font-bold">{{ $units }}</div>
    </div>
    <div class="rounded-2xl bg-white p-4 shadow ring-1 ring-black/5">
      <div class="text-sm text-slate-500">Order total</div>
      <div class="text-2xl font-bold">${{ number_format($total, 2) }}</div>
    </div>
  </div>

  {{-- Items table --}}
  <div class="bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">Item</th>
            <th class="px-4 py-3 text-left">Product ID</th>
            <th class="px-4 py-3 text-left">Price</th>
            <th class="px-4 py-3 text-left">Qty</th>
            <th class="px-4 py-3 text-left">Line Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
          @forelse($items as $it)
            @php
              $raw = $it->image ?? null;
              $img = $raw
                  ? (\Illuminate\Support\Str::startsWith($raw,'data:image') ? $raw : 'data:image/png;base64,'.$raw)
                  : 'https://placehold.co/60x60/png';
            @endphp
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <img src="{{ $img }}" class="h-12 w-12 rounded-lg object-cover ring-1 ring-slate-200" alt="">
                  <div class="font-medium">{{ $it->name ?? '—' }}</div>
                </div>
              </td>
              <td class="px-4 py-3 font-mono text-xs">{{ $it->product_id ?? '—' }}</td>
              <td class="px-4 py-3">${{ number_format((float)($it->price ?? 0), 2) }}</td>
              <td class="px-4 py-3">{{ (int)($it->quantity ?? 0) }}</td>
              <td class="px-4 py-3 font-semibold">${{ number_format((float)($it->total ?? 0), 2) }}</td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-8 text-slate-500" colspan="5">No items.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
