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
    // Promo save → /api/promos
    (function(){
      const $ = (id) => document.getElementById(id);
      const copyBtn = $('promoCopy');

      copyBtn?.addEventListener('click', async () => {
        const text = $('promoMsg').value;
        try {
          if (navigator.clipboard && window.isSecureContext) {
            // HTTPS / localhost
            await navigator.clipboard.writeText(text);
          } else {
            // HTTP fallback
            const textarea = document.createElement("textarea");
            textarea.value = text;
            textarea.style.position = "fixed";
            textarea.style.left = "-9999px";
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
          }
          const b = copyBtn; 
          b.textContent='Copied!'; 
          setTimeout(()=>b.textContent='Copy',1200);
        } catch(e){
          alert('Copy failed.'); 
        }
      });
    })();
  </script>
</x-admin-layout>
