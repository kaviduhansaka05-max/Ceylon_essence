<div>
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Customers</h1>
    <div>
      <input type="search" wire:model.debounce.500ms="q"
             placeholder="Search name or email…"
             class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500" />
    </div>
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">ID</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">2FA</th>
            <th class="px-4 py-3 text-left">Registered</th>
            <th class="px-4 py-3 text-left">Updated</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
          @forelse ($users as $u)
            @php
              $twoFactor = !empty($u->two_factor_confirmed_at) || !empty($u->two_factor_secret);
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 text-slate-500">{{ $u->id }}</td>
              <td class="px-4 py-3 font-medium">{{ $u->name }}</td>
              <td class="px-4 py-3">{{ $u->email }}</td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                  {{ ($u->status ?? 'active') === 'active'
                      ? 'bg-emerald-100 text-emerald-700'
                      : 'bg-rose-100 text-rose-700' }}">
                  {{ ucfirst($u->status ?? 'active') }}
                </span>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                  {{ $twoFactor ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
                  {{ $twoFactor ? 'Enabled' : 'Disabled' }}
                </span>
              </td>

              <td class="px-4 py-3">{{ optional($u->created_at)->diffForHumans() ?? '—' }}</td>
              <td class="px-4 py-3">{{ optional($u->updated_at)->diffForHumans() ?? '—' }}</td>

              <td class="px-4 py-3">
                <form method="POST" action="{{ route('admin.customers.toggle', $u->id) }}">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1 rounded-full text-xs font-medium 
                          {{ ($u->status ?? 'active') === 'active' 
                              ? 'bg-rose-600 text-white hover:bg-rose-700' 
                              : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
                    {{ ($u->status ?? 'active') === 'active' ? 'Block' : 'Unblock' }}
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-8 text-slate-500 text-center" colspan="8">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3">
      {{ $users->links() }}
    </div>
  </div>
</div>
