<?php include 'includes/header.php'; ?>

<div id="view-gr" class="space-y-6 animate-slide-up">

    <div id="gr-insights" class="hidden bg-gradient-to-r from-teal-900 to-emerald-800 rounded-2xl shadow-md p-3 flex items-center text-white overflow-hidden relative">
        <div class="font-black text-[10px] uppercase tracking-widest whitespace-nowrap pr-4 mr-2 border-r border-white/20 flex items-center gap-2 z-10">
            <i class="fas fa-chart-line text-emerald-400 animate-pulse text-sm"></i> <span data-translate="true">Top Received</span>
        </div>
        <div class="scrolling-text-container text-xs font-medium opacity-90 cursor-default" id="gr-top-items" title="Hover to pause">
            </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-700" data-translate="true" data-i18n="hist_gr">Good Receive History</h2>
            <p class="text-xs text-slate-500" data-translate="true" data-i18n="desc_gr">Log of incoming items to warehouse.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                <input type="text" id="search-gr" onkeyup="filterGR()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-teal-500 shadow-sm transition" data-translate-ph="true" data-i18n-ph="ph_search_gr" placeholder="Search GR...">
            </div>
            <?php if($isAdmin || in_array('gr_submit', $rights)): ?>
            <button id="btn-create-gr" onclick="openGrModal()" class="w-full sm:w-auto bg-teal-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-teal-700 btn-animated flex justify-center items-center whitespace-nowrap"><i class="fas fa-plus mr-2"></i> <span data-translate="true" data-i18n="btn_new_gr">New GR Form</span></button>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
        <div id="gr-card-container" class="md:hidden flex flex-col gap-4"></div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-6 py-4 align-top" data-translate="true" data-i18n="th_gr_id">GR ID & Date</th>
                        <th class="px-6 py-4 align-top" data-translate="true">NOMOR GR ERP</th>
                        <th class="px-6 py-4 align-top" data-translate="true" data-i18n="th_gr_by">Received By</th>
                        <th class="px-6 py-4 align-top" data-translate="true" data-i18n="th_gr_rem">Remarks / Supplier</th>
                        <th class="px-6 py-4 align-top min-w-[400px]" data-translate="true" data-i18n="th_gr_items">Items Received</th>
                    </tr>
                </thead>
                <tbody id="gr-table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-gr" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeAllDropdowns(event)">
    <div class="bg-white rounded-3xl w-full max-w-5xl shadow-2xl flex flex-col max-h-[95vh] animate-slide-up overflow-hidden">
        <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center flex-none rounded-t-3xl">
            <h3 class="font-bold text-slate-800 tracking-tight"><i class="fas fa-truck-loading text-teal-600 mr-2 text-lg"></i> <span data-translate="true" data-i18n="form_gr">Form Good Receive (GR)</span></h3>
            <button onclick="closeModal('modal-gr')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 custom-scroll" id="gr-scroll-container">
            <div class="mb-5 bg-teal-50 border border-teal-100 p-4 rounded-2xl text-xs text-teal-700 font-medium shadow-sm" data-translate="true" data-i18n="info_gr">
                <i class="fas fa-info-circle mr-1"></i> Input barang masuk. Data ini akan otomatis menambahkan stok di Master Inventory.
            </div>
            <form id="form-gr">
                <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true">Nomor GR ERP *</label>
                        <input type="text" id="gr-erp-no" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition" required placeholder="Ex: ERP-GR-001">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="rem_supp">Remarks / Supplier / PO Number</label>
                        <input type="text" id="gr-remarks" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition" required placeholder="Ex: PO-12345 / PT. Supplier Tbk">
                    </div>
                </div>
                
                <div class="border-t border-slate-200 pt-5">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-sm font-black text-teal-800 uppercase tracking-wide"><i class="fas fa-boxes mr-1.5"></i> <span data-translate="true" data-i18n="inc_items">Incoming Items</span></label>
                    </div>

                    <div id="gr-items-container" class="space-y-3"></div>
                    
                    <div class="flex justify-between items-center mt-4">
                        <button type="button" onclick="addGrRow()" class="bg-teal-100 text-teal-700 font-bold text-xs px-4 py-2 rounded-xl shadow-sm hover:bg-teal-200 btn-animated"><i class="fas fa-plus mr-1"></i> <span data-translate="true" data-i18n="add_row">Add Item Row</span></button>
                        <div id="gr-grand-total" class="hidden text-right font-black text-lg text-emerald-600 bg-emerald-50 px-4 py-2 rounded-xl border border-emerald-100 shadow-sm">Grand Total: Rp 0</div>
                    </div>
                    
                    <div class="border-t border-slate-200 pt-5 mt-6 pb-20">
                        <label class="text-sm font-black text-teal-800 uppercase tracking-wide mb-3 block"><i class="fas fa-camera mr-1.5"></i> <span data-translate="true">Photo Proof *</span></label>
                        <p class="text-[10px] text-slate-500 mb-3" data-translate="true">Wajib lampirkan foto barang fisik yang diterima di gudang.</p>
                        
                        <div class="flex gap-2 mb-4">
                            <button type="button" onclick="toggleGrPhotoSource('file')" id="btn-gr-file" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-teal-600 text-white shadow-md transition btn-animated"><i class="fas fa-file-upload mr-1.5"></i> <span data-translate="true">Upload</span></button>
                            <button type="button" onclick="toggleGrPhotoSource('camera')" id="btn-gr-cam" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition btn-animated"><i class="fas fa-camera mr-1.5"></i> <span data-translate="true">Camera</span></button>
                        </div>

                        <div id="source-gr-file" class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition flex items-center justify-center h-48 bg-slate-50 cursor-pointer" onclick="document.getElementById('input-gr-photo').click()">
                            <div class="space-y-3 pointer-events-none">
                                <i class="fas fa-cloud-upload-alt text-4xl text-slate-300"></i>
                                <p class="text-sm font-bold text-slate-500" id="gr-file-name" data-translate="true">Click to upload image</p>
                            </div>
                            <input type="file" id="input-gr-photo" accept="image/*" class="hidden" onchange="document.getElementById('gr-file-name').innerText = this.files[0] ? this.files[0].name : 'Click to upload image'">
                        </div>

                        <div id="source-gr-camera" class="hidden border border-slate-200 rounded-2xl overflow-hidden bg-black relative h-56 shadow-inner">
                            <video id="camera-stream-gr" class="w-full h-full object-cover transform scale-x-[-1]" autoplay playsinline></video>
                            <canvas id="camera-canvas-gr" class="hidden"></canvas>
                            <img id="camera-preview-gr" class="hidden w-full h-full object-cover">
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4 z-20">
                                <button type="button" onclick="takeGrSnapshot()" id="btn-capture-gr" class="bg-white/90 backdrop-blur rounded-full p-3 shadow-lg text-slate-800 hover:text-teal-600 hover:scale-110 transition duration-200"><i class="fas fa-camera text-2xl"></i></button>
                                <button type="button" onclick="retakeGrPhoto()" id="btn-retake-gr" class="hidden bg-white/90 backdrop-blur rounded-full p-3 shadow-lg text-red-600 hover:scale-110 transition duration-200"><i class="fas fa-redo text-2xl"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
            <button type="button" onclick="closeModal('modal-gr')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-translate="true" data-i18n="btn_cancel">Cancel</button>
            <button type="button" onclick="submitGr()" id="btn-submit-gr" class="px-8 py-2.5 bg-teal-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-teal-700"><i class="fas fa-save mr-1.5"></i> <span data-translate="true" data-i18n="btn_save_stk">Save & Update Stock</span></button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // ==========================================
    // SETTINGAN KECEPATAN TEKS STATISTIK (MARQUEE)
    // ==========================================
    // Ubah angka di bawah ini untuk mengatur kecepatan (dalam detik).
    // Semakin KECIL angkanya = Semakin CEPAT jalannya.
    // Semakin BESAR angkanya = Semakin LAMBAT jalannya.
    const SCROLL_SPEED_SECONDS = 70; 
    // ==========================================

    let grData = [];
    let grRowCount = 0;
    let videoStreamGr = null, capturedGrBase64 = null, activeSourceGr = 'file';

    window.onload = () => {
        applyLanguage();
        loadData();
    };

    function loadData() {
        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getInventory'})})
        .then(r => r.json()).then(d => { inventoryData = Array.isArray(d) ? d : (d.data || []); });

        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getReceives', role:currentUser.role})})
        .then(r => r.json()).then(d => { 
            if(d.code === 401) { logoutAction(); return; }
            grData = Array.isArray(d) ? d : (d.data || []); 
            renderGR(grData); 
        });
    }

    function filterGR() {
        const term = document.getElementById('search-gr').value.toLowerCase();
        const filtered = grData.filter(r => (r.gr_id || '').toLowerCase().includes(term) || (r.fullname || '').toLowerCase().includes(term) || (r.remarks || '').toLowerCase().includes(term) || (r.erp_gr_no || '').toLowerCase().includes(term));
        renderGR(filtered);
    }

    function renderGR(data = grData) {
        const tb = document.getElementById('gr-table-body');
        const cardContainer = document.getElementById('gr-card-container');
        const formatDt = (dtStr) => { if(!dtStr || dtStr === '0000-00-00 00:00:00' || dtStr === '-') return '-'; const d = new Date(dtStr); if(isNaN(d)) return dtStr; return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}); };
        
        let itemFreq = {};
        data.forEach(r => {
            (r.items || []).forEach(i => {
                if(i.name) itemFreq[i.name] = (itemFreq[i.name] || 0) + parseInt(i.qty);
            });
        });

        // Render Animasi Insight / Top Items dengan Settingan Kecepatan
        let sortedItems = Object.entries(itemFreq).sort((a, b) => b[1] - a[1]).slice(0, 10);
        if(sortedItems.length > 0) {
            let marqueeText = sortedItems.map((item, index) => `<span class="inline-block mx-4"><b>#${index+1}</b> ${item[0]} <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] ml-1 text-emerald-200">+${item[1]} Rcvd</span></span>`).join(' <i class="fas fa-circle text-[5px] text-white/30 mx-2"></i> ');
            document.getElementById('gr-insights').classList.remove('hidden');
            document.getElementById('gr-top-items').innerHTML = `<div class="scrolling-text" style="animation-duration: ${SCROLL_SPEED_SECONDS}s;">${marqueeText}</div>`;
        } else {
            document.getElementById('gr-insights').classList.add('hidden');
        }

        if(!data || data.length === 0) { tb.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</td></tr>`; cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</div>`; AutoTranslator.processDOM(); return; }

        let htmlArrayTable = []; let htmlArrayCard = [];
        data.forEach(r => {
            let itemsHtmlTable = '<div class="flex flex-col gap-2 mt-1">';
            let itemsHtmlCard = '<div class="flex flex-col gap-2 mt-2">';
            let grandTotal = 0;
            
            (r.items || []).forEach(i => {
                const itemPrice = parseFloat(i.price || 0);
                const itemTotal = itemPrice * parseInt(i.qty);
                grandTotal += itemTotal;
                
                let priceHtml = itemPrice > 0 ? `<div class="flex gap-2 text-[9px] text-slate-500 mt-1.5 pt-1.5 border-t border-teal-100"><div class="flex-1"><span class="opacity-70">Harga:</span> <span class="font-bold text-teal-800">Rp ${itemPrice.toLocaleString('id-ID')}</span></div><div class="flex-1 text-right"><span class="opacity-70">Total:</span> <span class="font-bold text-teal-900">Rp ${itemTotal.toLocaleString('id-ID')}</span></div></div>` : '';

                const itemBlock = `<div class="bg-teal-50 p-3 rounded-xl border border-teal-100 shadow-sm flex flex-col"><div class="flex justify-between items-start"><span class="text-[11px] font-bold text-teal-800 pr-2 leading-tight">${i.code} - ${i.name}</span><span class="text-xs font-black text-teal-900 bg-white px-2 py-0.5 rounded shadow-sm border border-teal-50 whitespace-nowrap">+${i.qty} <span class="text-[9px] font-normal text-slate-500">${i.uom}</span></span></div>${priceHtml}</div>`;
                itemsHtmlTable += itemBlock; itemsHtmlCard += itemBlock;
            });
            
            if(grandTotal > 0) {
                const gtHtml = `<div class="text-right font-black text-emerald-700 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100 mt-2 shadow-sm">Grand Total: Rp ${grandTotal.toLocaleString('id-ID')}</div>`;
                itemsHtmlTable += gtHtml; itemsHtmlCard += gtHtml;
            }
            
            itemsHtmlTable += '</div>'; itemsHtmlCard += '</div>';

            htmlArrayTable.push(`<tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 align-top"><div class="font-bold text-xs text-teal-700">${r.gr_id}</div><div class="text-[9px] text-slate-400 font-mono mt-0.5">${formatDt(r.created_at)}</div></td>
                <td class="px-6 py-4 align-top"><div class="font-black text-xs text-teal-700 bg-teal-50 px-2.5 py-1.5 rounded-lg border border-teal-100 inline-block mt-0.5">${r.erp_gr_no || '-'}</div></td>
                <td class="px-6 py-4 align-top"><div class="font-bold text-xs text-slate-700">${r.fullname}</div><div class="text-[10px] text-slate-500" data-translate="true">Warehouse Admin</div></td>
                <td class="px-6 py-4 align-top"><div class="text-xs text-slate-600 font-medium italic bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-2">"${r.remarks}"</div>${r.gr_photo && r.gr_photo !== '0' ? `<button onclick="viewPhoto('${r.gr_photo}')" class="text-[10px] bg-teal-100 text-teal-700 px-2 py-1 rounded shadow-sm hover:bg-teal-200 transition btn-animated"><i class="fas fa-camera mr-1"></i> <span data-translate="true">View Proof</span></button>` : ''}</td>
                <td class="px-6 py-4 align-top min-w-[300px] whitespace-normal">${itemsHtmlTable}</td>
            </tr>`);

            htmlArrayCard.push(`<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-1 transition-all hover:shadow-md"><div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3"><div><div class="font-black text-sm text-teal-700 mb-0.5">${r.gr_id}</div><div class="text-[10px] text-slate-400 font-mono">${formatDt(r.created_at)}</div></div><div class="text-right"><div class="font-bold text-xs text-slate-800">${r.fullname}</div><div class="text-[9px] text-slate-500 uppercase font-bold bg-slate-100 px-1.5 py-0.5 rounded mt-1 inline-block" data-translate="true">Warehouse Admin</div></div></div><div class="mb-4 flex justify-between items-center bg-teal-50/50 p-2.5 rounded-xl border border-teal-100"><span class="text-[10px] text-teal-600 font-bold uppercase tracking-wider" data-translate="true">No GR ERP:</span><div class="font-black text-xs text-teal-800">${r.erp_gr_no || '-'}</div></div><div class="mb-4"><div class="flex justify-between items-end mb-1.5"><div class="text-[10px] text-slate-400 uppercase font-bold" data-translate="true">Remarks / Supplier:</div>${r.gr_photo && r.gr_photo !== '0' ? `<button onclick="viewPhoto('${r.gr_photo}')" class="text-[10px] bg-teal-100 text-teal-700 px-2 py-1 rounded shadow-sm hover:bg-teal-200 transition btn-animated"><i class="fas fa-camera mr-1"></i> <span data-translate="true">Proof</span></button>` : ''}</div><div class="text-xs font-medium italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">"${r.remarks}"</div></div><div><div class="text-[10px] text-slate-400 uppercase font-bold mb-1.5" data-translate="true">Items Received:</div>${itemsHtmlCard}</div></div>`);
        });
        tb.innerHTML = htmlArrayTable.join(''); cardContainer.innerHTML = htmlArrayCard.join(''); AutoTranslator.processDOM();
    }

    function openGrModal() {
        document.getElementById('gr-erp-no').value = ''; document.getElementById('gr-remarks').value = ''; document.getElementById('gr-items-container').innerHTML = '';
        document.getElementById('input-gr-photo').value = ''; document.getElementById('gr-file-name').innerText = 'Click to upload image'; toggleGrPhotoSource('file');
        document.getElementById('gr-grand-total').classList.add('hidden');
        grRowCount = 0; addGrRow(); openModal('modal-gr'); AutoTranslator.processDOM();
    }

    // Helper Cek Hak Akses Edit Harga
    function canEditPriceHelper() {
        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        const isAppAdmin = currentUser.role === 'Administrator';
        return isAppAdmin || accRights.includes('edit_price');
    }

    function addGrRow() {
        grRowCount++;
        const d = document.createElement('div');
        d.className = "bg-white p-4 rounded-xl border border-teal-200 shadow-sm relative transition hover:border-teal-400";
        d.id = `gr-row-${grRowCount}`;
        
        const canEditPrice = canEditPriceHelper();
        
        let priceHtml = `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 pt-3 border-t border-teal-50">
            <div><label class="block text-[9px] font-bold text-slate-400 uppercase mb-1.5" data-translate="true" data-i18n="th_price">Harga Input</label><input type="number" class="w-full border border-slate-300 rounded-xl p-3 text-xs focus:ring-2 focus:ring-teal-500 outline-none font-medium transition gr-price ${!canEditPrice ? 'bg-slate-100 cursor-not-allowed' : ''}" placeholder="Harga Beli" oninput="calculateGrTotal()" ${!canEditPrice ? 'readonly' : ''}></div>
            <div><label class="block text-[9px] font-bold text-slate-400 uppercase mb-1.5" data-translate="true" data-i18n="total_price">Total</label><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs font-black text-teal-800 gr-total-display" placeholder="Rp 0" readonly tabindex="-1"></div>
        </div>`;

        d.innerHTML = `
            <button type="button" onclick="document.getElementById('${d.id}').remove(); calculateGrTotal();" class="absolute -top-2.5 -right-2.5 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow-md btn-animated z-10"><i class="fas fa-times text-[10px]"></i></button>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-5"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true">Item</label><div class="relative w-full"><input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs gr-item-display focus:ring-2 focus:ring-teal-500 outline-none cursor-pointer bg-slate-50 font-medium transition" data-translate-ph="true" data-i18n-ph="ph_search_item" placeholder="Search Item..." onfocus="showDropdown(this, 'gr')" onkeyup="filterDropdown(this, 'gr')" autocomplete="off" required><input type="hidden" class="gr-item-code"><input type="hidden" class="gr-item-name"><i class="fas fa-search absolute right-3 top-2.5 text-slate-400 pointer-events-none text-[12px]"></i><div class="dropdown-list hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-2xl mt-1.5 max-h-48 overflow-y-auto dropdown-scroll left-0"></div></div></div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true" data-i18n="curr_stk_short">Curr. Stock</label><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gr-stock text-slate-500 font-bold" placeholder="0" readonly tabindex="-1"></div>
                <div class="sm:col-span-3"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true">Qty Masuk</label><input type="number" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gr-qty focus:ring-2 focus:ring-teal-500 outline-none font-black text-slate-700 transition" placeholder="Qty Masuk" required min="1" oninput="calculateGrTotal()"></div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">UoM</label><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gr-uom text-slate-500 font-bold" placeholder="UoM" readonly tabindex="-1"></div>
            </div>
            ${priceHtml}
        `;
        document.getElementById('gr-items-container').appendChild(d); AutoTranslator.processDOM();
    }

    function calculateGrTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#gr-items-container > div').forEach(r => {
            const qty = parseFloat(r.querySelector('.gr-qty').value) || 0;
            const price = parseFloat(r.querySelector('.gr-price').value) || 0;
            const total = qty * price;
            grandTotal += total;
            
            const totalEl = r.querySelector('.gr-total-display');
            if(totalEl) totalEl.value = 'Rp ' + total.toLocaleString('id-ID');
        });
        const gtEl = document.getElementById('gr-grand-total');
        if(gtEl) {
            if(grandTotal > 0) { gtEl.innerText = 'Grand Total: Rp ' + grandTotal.toLocaleString('id-ID'); gtEl.classList.remove('hidden'); }
            else { gtEl.classList.add('hidden'); }
        }
    }

    async function submitGr() {
        const erpGrNo = document.getElementById('gr-erp-no').value; const remarks = document.getElementById('gr-remarks').value;
        if(!erpGrNo) { showCustomAlert("Info", "Harap isi Nomor GR ERP."); return; }
        if(!remarks) { showCustomAlert("Info", "Harap isi Remarks / Supplier."); return; }

        let items = [];
        document.querySelectorAll('#gr-items-container > div').forEach(r => {
            const code = r.querySelector('.gr-item-code').value; const name = r.querySelector('.gr-item-name').value;
            const qty = r.querySelector('.gr-qty').value; const uom = r.querySelector('.gr-uom').value;
            const price = r.querySelector('.gr-price') ? r.querySelector('.gr-price').value : 0;
            if(code && qty > 0) items.push({ code: code, name: name, qty: qty, uom: uom, price: price });
        });
        if(items.length === 0) { showCustomAlert("Info", "Minimal 1 barang masuk harus diisi."); return; }
        
        let base64Data = null;
        if (activeSourceGr === 'camera' && capturedGrBase64) { base64Data = capturedGrBase64; } 
        else { const fileInput = document.getElementById('input-gr-photo'); if (fileInput.files.length > 0) { base64Data = await new Promise((resolve) => { const reader = new FileReader(); reader.onload = (e) => resolve(e.target.result); reader.readAsDataURL(fileInput.files[0]); }); } }
        if (!base64Data) { showCustomAlert("Error", "Harap lampirkan foto bukti penerimaan (GR) terlebih dahulu!"); return; }

        showCustomConfirm("Konfirmasi", "Yakin memproses penerimaan ini? Stok di Inventory akan bertambah otomatis.", async () => {
            const btn = document.getElementById('btn-submit-gr'); const orgTxt = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1.5"></i> <span data-translate="true">Menyimpan...</span>`; AutoTranslator.processDOM();
            try {
                const compressedBase64 = await compressImage(base64Data);
                fetch('api/gis.php', {method:'POST', body:JSON.stringify({ action: 'submitGR', role: currentUser.role, department: currentUser.department, username: currentUser.username, fullname: currentUser.fullname, erp_gr_no: erpGrNo, remarks: remarks, items: items, photoBase64: compressedBase64 })})
                .then(r=>r.json()).then(res => { 
                    btn.disabled = false; btn.innerHTML = orgTxt; if(res.code===401){logoutAction();return;}
                    if(res.success){ closeModal('modal-gr'); loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); } 
                }).catch(e => { btn.disabled = false; btn.innerHTML = orgTxt; showCustomAlert("Error", t('err_conn')); });
            } catch (err) { btn.disabled = false; btn.innerHTML = orgTxt; showCustomAlert("Error", "Gagal memproses foto."); }
        });
    }

    function toggleGrPhotoSource(source) {
        activeSourceGr = source; const btnFile = document.getElementById('btn-gr-file'), btnCam = document.getElementById('btn-gr-cam'), contFile = document.getElementById('source-gr-file'), contCam = document.getElementById('source-gr-camera');
        if(source === 'camera') { btnCam.classList.replace('bg-slate-100','bg-teal-600'); btnCam.classList.replace('text-slate-600','text-white'); btnFile.classList.replace('bg-teal-600','bg-slate-100'); btnFile.classList.replace('text-white','text-slate-600'); contFile.classList.add('hidden'); contCam.classList.remove('hidden'); startGrCamera(); } 
        else { btnFile.classList.replace('bg-slate-100','bg-teal-600'); btnFile.classList.replace('text-slate-600','text-white'); btnCam.classList.replace('bg-teal-600','bg-slate-100'); btnCam.classList.replace('text-white','text-slate-600'); contCam.classList.add('hidden'); contFile.classList.remove('hidden'); stopGrCamera(); }
    }
    async function startGrCamera() {
        const video = document.getElementById('camera-stream-gr'), preview = document.getElementById('camera-preview-gr'); preview.classList.add('hidden'); video.classList.remove('hidden'); document.getElementById('btn-capture-gr').classList.remove('hidden'); document.getElementById('btn-retake-gr').classList.add('hidden'); capturedGrBase64 = null;
        try { const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }); video.srcObject = stream; videoStreamGr = stream; } catch (err) { showCustomAlert("Camera Error", "Kamera tidak bisa diakses."); toggleGrPhotoSource('file'); }
    }
    function stopGrCamera() { if (videoStreamGr) { videoStreamGr.getTracks().forEach(track => track.stop()); videoStreamGr = null; } }
    function takeGrSnapshot() { const video = document.getElementById('camera-stream-gr'), canvas = document.getElementById('camera-canvas-gr'), preview = document.getElementById('camera-preview-gr'); if (video.readyState === video.HAVE_ENOUGH_DATA) { canvas.width = video.videoWidth; canvas.height = video.videoHeight; canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height); capturedGrBase64 = canvas.toDataURL('image/jpeg', 0.8); preview.src = capturedGrBase64; preview.classList.remove('hidden'); video.classList.add('hidden'); document.getElementById('btn-capture-gr').classList.add('hidden'); document.getElementById('btn-retake-gr').classList.remove('hidden'); } }
    function retakeGrPhoto() { capturedGrBase64 = null; document.getElementById('camera-preview-gr').classList.add('hidden'); document.getElementById('camera-stream-gr').classList.remove('hidden'); document.getElementById('btn-capture-gr').classList.remove('hidden'); document.getElementById('btn-retake-gr').classList.add('hidden'); }
</script>