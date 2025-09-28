<x-admin-layout>
  <div class="p-6 space-y-8 bg-gray-50 min-h-screen">

    {{-- ✅ KPI Cards --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      @foreach ($kpis as $k)
        <div class="bg-white rounded-2xl shadow hover:shadow-lg transition p-6">
          <div class="flex items-center gap-3 text-gray-500">
            <span class="inline-flex items-center justify-center rounded-xl bg-gray-100 p-3 text-gray-700">
              {!! $k['icon'] !!}
            </span>
            <span class="text-sm font-medium">{{ $k['label'] }}</span>
          </div>
          <div class="mt-3 text-3xl font-bold text-gray-900">{{ $k['value'] }}</div>
        </div>
      @endforeach
    </section>

    {{-- ✅ Trends + Pipeline --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div class="xl:col-span-2 bg-white rounded-3xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700">Revenue (last 30 days)</h3>
        <div class="mt-4"><canvas id="rev30" class="!h-64"></canvas></div>
      </div>

      <div class="bg-white rounded-3xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Order Pipeline</h3>
        <div class="space-y-5">
          @foreach (['pending','processing','completed','cancelled'] as $st)
            @php $count = (int)($pipeline[$st] ?? 0); $pct = round(($count / $totalPipe) * 100); @endphp
            <div>
              <div class="flex justify-between text-sm text-gray-600">
                <span class="capitalize font-medium">{{ $st }}</span>
                <span>{{ $count }}</span>
              </div>
              <div class="mt-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-2.5 rounded-full {{ $stColors[$st] }}" style="width: {{ $pct }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    {{-- ✅ Products / Orders / Stock --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div class="bg-white rounded-3xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Top Products (30d)</h3>
        <table class="w-full text-sm">
          <thead class="text-xs uppercase text-gray-400">
            <tr><th class="text-left">Product</th><th class="text-right">Units</th><th class="text-right">Revenue</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($topProducts as $p)
              <tr class="hover:bg-gray-50">
                <td class="py-2">{{ $p['name'] }}</td>
                <td class="py-2 text-right">{{ $p['units'] }}</td>
                <td class="py-2 text-right">${{ number_format($p['revenue'], 2) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="py-4 text-gray-500">No data.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="bg-white rounded-3xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Recent Orders</h3>
        <div class="space-y-3">
          @forelse($recentOrders as $o)
            <a href="{{ route('admin.orders.show', (string)$o->_id) }}" class="flex justify-between items-center rounded-xl border px-3 py-2 hover:bg-gray-50">
              <div>
                <div class="font-semibold text-sm">#{{ (string)$o->_id }}</div>
                <div class="text-xs text-gray-500">{{ ucfirst($o->status ?? 'pending') }} • {{ optional($o->created_at)->diffForHumans() }}</div>
              </div>
              <div class="text-sm font-bold">${{ number_format((float)($o->total ?? 0), 2) }}</div>
            </a>
          @empty
            <div class="text-gray-500">No recent orders.</div>
          @endforelse
        </div>
      </div>

      <div class="bg-white rounded-3xl shadow p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Low Stock</h3>
        <table class="w-full text-sm">
          <thead class="text-xs uppercase text-gray-400">
            <tr><th class="text-left">Product</th><th class="text-right">Stock</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($lowStock as $p)
              <tr class="hover:bg-gray-50">
                <td class="py-2">{{ $p->name }}</td>
                <td class="py-2 text-right">{{ (int) $p->inventory }}</td>
              </tr>
            @empty
              <tr><td colspan="2" class="py-6 text-gray-500">All good ✌️</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- ✅ Promo Code Blast (unchanged) --}}
    @include('admin.partials.promo-blast')

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
