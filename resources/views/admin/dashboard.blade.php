<x-admin-layout>
  <div class="p-6 space-y-8">

  <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- KPI cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  @php
  $kpis = [
    [
      'label' => "Today’s Revenue",
      'value' => '$'.number_format($metrics['revenueToday'],2),
      'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10v2m0 8v2m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                 </svg>'
    ],
    [
      'label' => 'New Orders',
      'value' => $metrics['newOrdersToday'],
      'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                          d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13l-1.293 3.293A1 1 0 006.618 18H18m-11 0a1 1 0 100 2 1 1 0 000-2zm11 0a1 1 0 100 2 1 1 0 000-2z"/>
                 </svg>'
    ],
    [
      'label' => 'AOV (7d)',
      'value' => '$'.number_format($metrics['aov7'],2),
      'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 17l6-6 4 4 8-8"/>
                 </svg>'
    ],
    [
      'label' => 'Low Stock Alerts',
      'value' => $metrics['lowStockCount'],
      'icon'  => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                          d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                 </svg>'
    ],
  ];
@endphp


      @foreach ($kpis as $k)
        <div class="rounded-2xl bg-white/90 backdrop-blur shadow-sm ring-1 ring-gray-100 p-5">
          <div class="flex items-center gap-3 text-gray-500">
            <span class="inline-flex items-center justify-center rounded-xl bg-gray-100 text-gray-700 p-2">{!! $k['icon'] !!}</span>
            <span class="text-sm">{{ $k['label'] }}</span>
          </div>
          <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-900">{{ $k['value'] }}</div>
        </div>
      @endforeach
    </section>

    {{-- Trends + Pipeline --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      <div class="xl:col-span-2 rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-700">Revenue (last 30 days)</h3>
        </div>
        <div class="mt-4"><canvas id="rev30" class="!h-64"></canvas></div>
      </div>

      <div class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Pipeline</h3>
        @php
          $totalPipe = max(1, array_sum($pipeline));
          $stColors = ['pending'=>'bg-amber-500','processing'=>'bg-sky-600','completed'=>'bg-emerald-600','cancelled'=>'bg-rose-600'];
        @endphp
        <div class="space-y-4">
          @foreach (['pending','processing','completed','cancelled'] as $st)
            @php $count = (int)($pipeline[$st] ?? 0); $pct = round(($count / $totalPipe) * 100); @endphp
            <div>
              <div class="flex items-center justify-between text-sm text-gray-600">
                <div class="font-medium capitalize">{{ $st }}</div><div>{{ $count }}</div>
              </div>
              <div class="mt-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-2.5 rounded-full {{ $stColors[$st] }}" style="width: {{ $pct }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- Top products / Recent orders / Low stock --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">
      <div class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Products (30d)</h3>
        <table class="min-w-full text-sm">
          <thead class="text-xs uppercase text-gray-500">
            <tr><th class="py-2 text-left">Product</th><th class="py-2 text-right">Units</th><th class="py-2 text-right">Revenue</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($topProducts as $p)
              <tr class="hover:bg-gray-50/60">
                <td class="py-2 pr-2">{{ $p['name'] }}</td>
                <td class="py-2 text-right">{{ $p['units'] }}</td>
                <td class="py-2 text-right">${{ number_format($p['revenue'], 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="py-4 text-gray-500">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Recent Orders</h3>
        <div class="space-y-3">
          @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', (string)$o->_id) }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-3 py-2 hover:bg-gray-50">
              <div>
                <div class="text-sm font-semibold">#{{ (string)$o->_id }}</div>
                <div class="text-xs text-gray-500">{{ ucfirst($o->status ?? 'pending') }} • {{ optional($o->created_at)->diffForHumans() }}</div>
              </div>
              <div class="text-sm font-bold">${{ number_format((float)($o->total ?? 0), 2) }}</div>
            </a>
          @empty
            <div class="text-gray-500">No recent orders.</div>
          @endforelse
        </div>
      </div>

      <div class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Low Stock</h3>
        <table class="min-w-full text-sm">
          <thead class="text-xs uppercase text-gray-500">
            <tr><th class="py-2 text-left">Product</th><th class="py-2 text-right">Stock</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($lowStock as $p)
              <tr class="hover:bg-gray-50/60">
                <td class="py-2 pr-2">{{ $p->name }}</td>
                <td class="py-2 text-right">{{ (int) $p->inventory }}</td>
              </tr>
            @empty
              <tr><td colspan="2" class="py-6 text-gray-500">All good ✌️</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- Promo Code Blast --}}
    <section class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Promo Code Blast</h3>
        <p class="text-xs text-gray-500">Generate & save a one-off code, then share.</p>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
          <label class="block text-xs text-gray-600 mb-1">Type</label>
          <select id="promoType" class="w-full rounded-lg border-gray-300">
            <option value="percent">Percent %</option>
            <option value="flat">Flat $</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-600 mb-1">Amount</label>
          <input id="promoAmount" type="number" min="1" class="w-full rounded-lg border-gray-300" placeholder="e.g. 20">
        </div>
        <div>
          <label class="block text-xs text-gray-600 mb-1">Min Order (optional)</label>
          <input id="promoMin" type="number" min="0" class="w-full rounded-lg border-gray-300" placeholder="e.g. 50">
        </div>
        <div>
          <label class="block text-xs text-gray-600 mb-1">Expires</label>
          <input id="promoExpiry" type="date" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="flex items-end gap-2">
          <button id="promoGenBtn" class="flex-1 rounded-xl bg-slate-900 text-white px-4 py-2.5 hover:bg-slate-700">Generate Code</button>
          <button id="promoSaveBtn" class="flex-1 rounded-xl border px-4 py-2.5 hover:bg-gray-50" disabled>Save</button>
        </div>
      </div>

      <div id="promoResult" class="mt-5 hidden">
        <div class="flex items-center justify-between gap-3">
          <div>
            <div class="text-xs text-gray-500">Code</div>
            <div id="promoCode" class="text-2xl font-bold tracking-widest"></div>
            <div id="promoSavedBadge" class="hidden mt-1 inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Saved ✓</div>
          </div>
          <div class="flex items-center gap-2">
            <button id="promoCopy" class="rounded-lg border px-3 py-2 hover:bg-gray-50">Copy</button>
            <button id="promoShare" class="rounded-lg bg-rose-600 text-white px-3 py-2 hover:bg-rose-700">Share</button>
          </div>
        </div>
        <div class="mt-3">
          <label class="block text-xs text-gray-600 mb-1">Share message</label>
          <textarea id="promoMsg" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
        </div>
      </div>
    </section>
  </div>

  {{-- Chart.js + Promo Script --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Revenue chart (unchanged)
    (function(){
      const el = document.getElementById('rev30');
      if (!el) return;
      const labels = @json($labels);
      const data   = @json(array_map(fn($v)=>round($v,2), $series));
      const ctx = el.getContext('2d');
      const g = ctx.createLinearGradient(0, 0, 0, 260); g.addColorStop(0,'rgba(244,63,94,.25)'); g.addColorStop(1,'rgba(244,63,94,.02)');
      new Chart(ctx,{
        type:'line',
        data:{labels,datasets:[{label:'Revenue',data,tension:.3,fill:true,backgroundColor:g,borderColor:'rgb(59,130,246)',borderWidth:2,pointRadius:0,pointHoverRadius:3}]},
        options:{plugins:{legend:{display:false}},scales:{x:{grid:{display:false}},y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.05)'}}}}
      });
    })();

    // Promo save → /api/promos (with CSRF token + graceful fallback)
    (function(){
      const API_PROMO_URL = '{{ url('/api/promos') }}';
      const $ = (id) => document.getElementById(id);
      const genBtn = $('promoGenBtn'); const saveBtn = $('promoSaveBtn');
      if (!genBtn) return;

      // get CSRF token
      const tokenMeta = document.querySelector('meta[name="csrf-token"]');
      const token = tokenMeta ? tokenMeta.getAttribute('content') : null;

      const d = new Date(); d.setDate(d.getDate() + 7);
      $('promoExpiry').value = d.toISOString().slice(0,10);

      let generated = null;

      genBtn.addEventListener('click', () => {
        const type   = $('promoType').value;
        const amount = Math.max(1, parseInt($('promoAmount').value || 0, 10));
        const min    = parseFloat($('promoMin').value || 0);
        const expiry = $('promoExpiry').value || null;

        const prefix = type === 'percent' ? 'CEY' : 'CEY$';
        const rnd = Math.random().toString(36).slice(2,7).toUpperCase();
        const code = `${prefix}-${amount}-${rnd}`;

        const site    = "{{ url('/') }}";
        const offText = type === 'percent' ? `${amount}% off` : `$${amount} off`;
        const minText = min > 0 ? ` (min $${min})` : '';
        const expText = expiry ? `\nValid until ${expiry}.` : '';
        const msg     = `✨ Limited Time: ${offText}${minText} at Ceylon Essence! Use code ${code} at checkout.\nShop now: ${site}/products${expText}`;

        $('promoCode').textContent = code;
        $('promoMsg').value = msg;
        $('promoResult').classList.remove('hidden');
        $('promoSavedBadge').classList.add('hidden');

        generated = { code, type, amount, min, expires_at: expiry, active: true };
        saveBtn.disabled = false; saveBtn.textContent = 'Save';
      });

      saveBtn?.addEventListener('click', async () => {
        if (!generated) return;
        try {
          saveBtn.disabled = true; saveBtn.textContent = 'Saving...';

          const res = await fetch(API_PROMO_URL, {
            method: 'POST',
            headers: { 
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              ...(token ? { 'X-CSRF-TOKEN': token } : {})
            },
            body: JSON.stringify(generated)
          });

          const text = await res.text();
          let data; try { data = JSON.parse(text); } catch { data = {}; }

          if (!res.ok || !data.ok) {
            const reason = data.message || res.statusText || 'Save failed';
            throw new Error(`HTTP ${res.status}: ${reason}\n${text.slice(0,800)}`);
          }

          $('promoSavedBadge').classList.remove('hidden');
          saveBtn.textContent = 'Saved';
        } catch (err) {
          alert(`Save failed\n\n${err.message}`);
          saveBtn.disabled = false; saveBtn.textContent = 'Save';
        }
      });

 $('promoCopy')?.addEventListener('click', () => {
  try {
    const code = $('promoCode').textContent.trim();
    if (!code) {
      alert("No promo code generated yet.");
      return;
    }

    if (navigator.clipboard && window.isSecureContext) {
      // Works on HTTPS / localhost
      navigator.clipboard.writeText(code).then(() => {
        showCopied();
      }).catch(() => {
        fallbackCopy(code);
      });
    } else {
      // Fallback for HTTP
      fallbackCopy(code);
    }

    function fallbackCopy(text) {
      const temp = document.createElement("textarea");
      temp.value = text;
      document.body.appendChild(temp);
      temp.select();
      document.execCommand("copy");
      document.body.removeChild(temp);
      showCopied();
    }

    function showCopied() {
      const b = $('promoCopy');
      b.textContent = 'Copied!';
      setTimeout(() => b.textContent = 'Copy', 1200);
    }
  } catch (e) {
    alert('Copy failed. Try manually.');
  }
});




      $('promoShare')?.addEventListener('click', async () => {
        const text = $('promoMsg').value;
        if (navigator.share) { 
          try { await navigator.share({ title: 'Ceylon Essence Promo', text }); } catch(e) {} 
        }
        else { 
          try { await navigator.clipboard.writeText(text); } catch(e) {} 
          alert('Share not supported. Message copied—paste it anywhere.'); 
        }
      });
    })();
  </script>
</x-admin-layout>
