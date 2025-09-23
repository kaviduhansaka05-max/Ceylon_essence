@extends('admin.layout.admin')

@section('page_title','Orders')

@section('content')
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Orders</h1>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-2">
      <select name="status"
              class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500">
        <option value="">All statuses</option>
        @foreach (['pending','processing','completed','cancelled'] as $s)
          <option value="{{ $s }}" {{ ($status ?? '') === $s ? 'selected' : '' }}>
            {{ ucfirst($s) }}
          </option>
        @endforeach
      </select>
      <button type="submit"
              class="px-3 py-2 rounded bg-slate-900 text-white text-sm hover:bg-slate-700">
        Filter
      </button>
      @if(!empty($status))
        <a href="{{ route('admin.orders.index') }}"
           class="px-3 py-2 rounded border text-sm border-gray-300 hover:bg-gray-50">
          Clear
        </a>
      @endif
    </form>
  </div>

  <div class="bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">Order ID</th>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Qty</th>
            <th class="px-4 py-3 text-left">Total</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Created</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
          @forelse ($orders as $o)
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 font-mono text-xs">{{ (string)$o->_id }}</td>
              <td class="px-4 py-3">{{ $o->user_id ?? '—' }}</td>
              <td class="px-4 py-3">{{ $o->quantity ?? 0 }}</td>
              <td class="px-4 py-3 font-semibold">${{ number_format((float)($o->total ?? 0), 2) }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                  @switch(strtolower($o->status ?? 'pending'))
                    @case('completed') bg-emerald-100 text-emerald-700 @break
                    @case('processing') bg-sky-100 text-sky-700 @break
                    @case('cancelled') bg-rose-100 text-rose-700 @break
                    @default bg-amber-100 text-amber-700
                  @endswitch">
                  {{ ucfirst($o->status ?? 'pending') }}
                </span>
              </td>
              <td class="px-4 py-3">{{ optional($o->created_at)->diffForHumans() ?? '—' }}</td>
              <td class="px-4 py-3">
                <a href="{{ route('admin.orders.show', (string)$o->_id) }}"
                   class="text-slate-700 hover:underline">View</a>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-8 text-slate-500" colspan="7">No orders found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3">
      {{ $orders->links() }}
    </div>
  </div>
@endsection
