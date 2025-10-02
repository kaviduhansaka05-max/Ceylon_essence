<div>
  {{-- Page header --}}
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold tracking-tight">Customers</h1>

    <div class="flex items-center gap-2">
      <input type="search" wire:model.debounce.500ms="q"
             placeholder="Search name or email…"
             class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-slate-500 focus:ring-slate-500" />
    </div>
  </div>

  {{-- ✅ Desktop Table --}}
  <div class="hidden md:block bg-white rounded-2xl shadow ring-1 ring-black/5 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
          <tr>
            <th class="px-4 py-3 text-left">ID</th>
            <th class="px-4 py-3 text-left">Avatar</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Verified</th>
            <th class="px-4 py-3 text-left">2FA</th>
            <th class="px-4 py-3 text-left">Team ID</th>
            <th class="px-4 py-3 text-left">Registered</th>
            <th class="px-4 py-3 text-left">Updated</th>
            <th class="px-4 py-3 text-left">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
          @forelse ($users as $u)
            @php
              $avatar = method_exists($u, 'getProfilePhotoUrlAttribute')
                        ? $u->profile_photo_url
                        : ($u->profile_photo_path
                            ? \Illuminate\Support\Facades\Storage::url($u->profile_photo_path)
                            : 'https://placehold.co/40x40/png');
              $verified = $u->email_verified_at !== null;
              $twoFactor = !empty($u->two_factor_confirmed_at) || !empty($u->two_factor_secret);
            @endphp
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 text-slate-500">{{ $u->id }}</td>
              <td class="px-4 py-3">
                <img src="{{ $avatar }}" alt="{{ $u->name }}" class="h-10 w-10 rounded-full object-cover ring-1 ring-slate-200">
              </td>
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
                @if($verified)
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-700">Verified</span>
                  <div class="text-xs text-slate-500 mt-1">{{ $u->email_verified_at->diffForHumans() }}</div>
                @else
                  <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                  {{ $twoFactor ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
                  {{ $twoFactor ? 'Enabled' : 'Disabled' }}
                </span>
              </td>
              <td class="px-4 py-3">{{ $u->current_team_id ?? '—' }}</td>
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
              <td class="px-4 py-8 text-slate-500" colspan="11">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="px-4 py-3">
      {{ $users->links() }}
    </div>
  </div>

  {{-- ✅ Mobile Cards --}}
  <div class="space-y-4 md:hidden">
    @forelse($users as $u)
      @php
        $avatar = method_exists($u, 'getProfilePhotoUrlAttribute')
                  ? $u->profile_photo_url
                  : ($u->profile_photo_path
                      ? \Illuminate\Support\Facades\Storage::url($u->profile_photo_path)
                      : 'https://placehold.co/40x40/png');
        $verified = $u->email_verified_at !== null;
        $twoFactor = !empty($u->two_factor_confirmed_at) || !empty($u->two_factor_secret);
      @endphp
      <div class="bg-white shadow rounded-xl p-4 space-y-2">
        <div class="flex items-center gap-3">
          <img src="{{ $avatar }}" class="h-12 w-12 rounded-full object-cover ring-1 ring-slate-200" alt="{{ $u->name }}">
          <div>
            <div class="font-semibold">{{ $u->name }}</div>
            <div class="text-xs text-slate-500">{{ $u->email }}</div>
          </div>
        </div>
        <div class="flex flex-wrap gap-2 text-xs">
          <span class="px-2 py-0.5 rounded-full {{ ($u->status ?? 'active') === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
            {{ ucfirst($u->status ?? 'active') }}
          </span>
          <span class="px-2 py-0.5 rounded-full {{ $verified ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
            {{ $verified ? 'Verified' : 'Pending' }}
          </span>
          <span class="px-2 py-0.5 rounded-full {{ $twoFactor ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">
            {{ $twoFactor ? '2FA On' : '2FA Off' }}
          </span>
        </div>
        <div class="text-xs text-slate-500">Registered: {{ optional($u->created_at)->diffForHumans() ?? '—' }}</div>
        <div class="text-xs text-slate-500">Updated: {{ optional($u->updated_at)->diffForHumans() ?? '—' }}</div>
        <form method="POST" action="{{ route('admin.customers.toggle', $u->id) }}">
          @csrf
          <button type="submit"
                  class="mt-2 w-full px-3 py-1 rounded-lg text-xs font-medium 
                  {{ ($u->status ?? 'active') === 'active' 
                      ? 'bg-rose-600 text-white hover:bg-rose-700' 
                      : 'bg-emerald-600 text-white hover:bg-emerald-700' }}">
            {{ ($u->status ?? 'active') === 'active' ? 'Block' : 'Unblock' }}
          </button>
        </form>
      </div>
    @empty
      <p class="text-slate-500 text-sm">No users found.</p>
    @endforelse
  </div>
</div>
