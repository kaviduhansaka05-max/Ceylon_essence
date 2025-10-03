<x-admin-layout>
  <div class="p-6 space-y-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- KPI cards --}}
    {{-- ... (unchanged KPI, trends, pipeline, top products, recent orders, low stock) ... --}}

    {{-- Promo Code Blast --}}
    <section class="rounded-3xl bg-white shadow-sm ring-1 ring-gray-100 p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Promo Code Blast</h3>
        <p class="text-xs text-gray-500">Generate & save a one-off code, then share.</p>
      </div>

      <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
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
    (function(){
      const API_PROMO_URL = '{{ url('/api/promos') }}';
      const $ = (id) => document.getElementById(id);
      const genBtn = $('promoGenBtn'); const saveBtn = $('promoSaveBtn');
      if (!genBtn) return;

      const tokenMeta = document.querySelector('meta[name="csrf-token"]');
      const token = tokenMeta ? tokenMeta.getAttribute('content') : null;

      const d = new Date(); d.setDate(d.getDate() + 7);
      $('promoExpiry').value = d.toISOString().slice(0,10);

      let generated = null;

      genBtn.addEventListener('click', () => {
        const type   = $('promoType').value;
        const amount = Math.max(1, parseInt($('promoAmount').value || 0, 10));
        const expiry = $('promoExpiry').value || null;

        const prefix = type === 'percent' ? 'CEY' : 'CEY$';
        const rnd = Math.random().toString(36).slice(2,7).toUpperCase();
        const code = `${prefix}-${amount}-${rnd}`;

        const site    = "{{ url('/') }}";
        const offText = type === 'percent' ? `${amount}% off` : `$${amount} off`;
        const expText = expiry ? `\nValid until ${expiry}.` : '';
        const msg     = `✨ Limited Time: ${offText} at Ceylon Essence! Use code ${code} at checkout.\nShop now: ${site}/products${expText}`;

        $('promoCode').textContent = code;
        $('promoMsg').value = msg;
        $('promoResult').classList.remove('hidden');
        $('promoSavedBadge').classList.add('hidden');

        generated = { code, type, amount, expires_at: expiry, active: true };
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

          const data = await res.json().catch(()=>({}));

          if (!res.ok || !data.ok) throw new Error(data.message || 'Save failed');

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
          if (!code) return alert("No promo code generated yet.");
          navigator.clipboard?.writeText(code).then(()=>showCopied()).catch(()=>fallbackCopy(code));

          function fallbackCopy(text) {
            const temp = document.createElement("textarea");
            temp.value = text; document.body.appendChild(temp);
            temp.select(); document.execCommand("copy");
            document.body.removeChild(temp);
            showCopied();
          }
          function showCopied() {
            const b = $('promoCopy'); b.textContent = 'Copied!';
            setTimeout(()=> b.textContent = 'Copy', 1200);
          }
        } catch { alert('Copy failed. Try manually.'); }
      });

      $('promoShare')?.addEventListener('click', async () => {
        const text = $('promoMsg').value;
        if (navigator.share) { try { await navigator.share({ title: 'Ceylon Essence Promo', text }); } catch(e) {} }
        else { await navigator.clipboard.writeText(text).catch(()=>{}); alert('Share not supported. Message copied.'); }
      });
    })();
  </script>
</x-admin-layout>
