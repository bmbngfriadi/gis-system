<?php include 'includes/header.php'; ?>

<div id="view-gi" class="space-y-6 animate-slide-up">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
        <div id="card-gi-all" onclick="setGiFilter('All')" class="card-filter card-filter-active bg-gradient-to-br from-blue-500 to-indigo-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-list"></i></div>
            <div class="z-10"><div class="text-[8px] font-bold text-blue-100 uppercase tracking-wider mb-0.5" data-translate="true" data-i18n="stat_tot_gi">Total Request</div><div class="text-xl font-black" id="stat-total">0</div></div>
        </div>
        <div id="card-gi-pending" onclick="setGiFilter('Pending Head')" class="card-filter bg-gradient-to-br from-amber-400 to-orange-500 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white bg-live-gradient">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-user-clock"></i></div>
            <div class="z-10"><div class="text-[8px] font-bold text-amber-100 uppercase tracking-wider mb-0.5" data-translate="true" data-i18n="stat_pend_head">Pending Head</div><div class="text-xl font-black" id="stat-pending-head">0</div></div>
        </div>
        <div id="card-gi-wh" onclick="setGiFilter('Pending Warehouse')" class="card-filter bg-gradient-to-br from-pink-500 to-rose-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-truck-loading"></i></div>
            <div class="z-10"><div class="text-[8px] font-bold text-pink-100 uppercase tracking-wider mb-0.5" data-translate="true" data-i18n="stat_pend_wh">Pending WH</div><div class="text-xl font-black" id="stat-pending-wh">0</div></div>
        </div>
        <div id="card-gi-receive" onclick="setGiFilter('Pending Receive')" class="card-filter bg-gradient-to-br from-cyan-500 to-blue-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-people-carry-box"></i></div>
            <div class="z-10"><div class="text-[8px] font-bold text-cyan-100 uppercase tracking-wider mb-0.5" data-translate="true" data-i18n="stat_pend_recv">Pending Receive</div><div class="text-xl font-black" id="stat-pending-recv">0</div></div>
        </div>
        <div id="card-gi-done" onclick="setGiFilter('Completed')" class="card-filter bg-gradient-to-br from-emerald-500 to-teal-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-check-double"></i></div>
            <div class="z-10"><div class="text-[8px] font-bold text-emerald-100 uppercase tracking-wider mb-0.5" data-translate="true" data-i18n="stat_comp">Completed</div><div class="text-xl font-black" id="stat-done">0</div></div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-700" data-translate="true" data-i18n="hist_gi">Good Issue History</h2>
            <p class="text-xs text-slate-500" data-translate="true" data-i18n="click_filter_info">Klik kartu di atas untuk memfilter data.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                <input type="text" id="search-gi" onkeyup="filterGI()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition" data-translate-ph="true" data-i18n-ph="ph_search_gi" placeholder="Search GI...">
            </div>
            <?php if($isAdmin || in_array('gi_submit', $rights)): ?>
            <button id="btn-create-gi" onclick="openGiModal()" class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 btn-animated flex items-center justify-center whitespace-nowrap"><i class="fas fa-plus mr-2"></i> <span data-translate="true" data-i18n="btn_new_gi">New GI Form</span></button>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
        <div id="gi-card-container" class="md:hidden flex flex-col gap-4"></div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                    <tr>
                        <th class="px-6 py-4 align-top" data-translate="true" data-i18n="th_id">NO GIF & Date</th>
                        <th class="px-6 py-4 align-top" data-translate="true">NOMOR GI ERP</th>
                        <th class="px-6 py-4 align-top" data-translate="true" data-i18n="th_req">Requestor Info</th>
                        <th class="px-6 py-4 align-top min-w-[350px] max-w-[450px]" data-translate="true" data-i18n="th_items">Items & Activities Description</th>
                        <th class="px-6 py-4 align-top text-center w-[180px]" data-translate="true" data-i18n="th_stat">Status</th>
                        <th class="px-6 py-4 align-top text-right w-[140px]" data-translate="true" data-i18n="th_act">Action</th>
                    </tr>
                </thead>
                <tbody id="gi-table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-gi" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeAllDropdowns(event)">
    <div class="bg-white rounded-3xl w-full max-w-5xl shadow-2xl flex flex-col max-h-[95vh] animate-slide-up">
        <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center flex-none rounded-t-3xl">
            <h3 class="font-bold text-slate-800 tracking-tight"><i class="fas fa-file-invoice text-indigo-600 mr-2 text-lg"></i> <span id="modal-gi-title" data-translate="true" data-i18n="form_gi">Form Good Issue</span></h3>
            <button onclick="closeModal('modal-gi')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 custom-scroll" id="gi-scroll-container">
            <form id="form-gi">
                <input type="hidden" id="gi-action-type" value="submit">
                <input type="hidden" id="gi-edit-id" value="">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 shadow-inner">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="req_dept">Requestor / Dept</label>
                        <div class="font-black text-slate-700" id="disp-req-name">-</div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="sec_req">Section Requestor</label>
                        <input type="text" id="gi-section" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" required data-translate-ph="true" data-i18n-ph="ph_sec_req" placeholder="Ex: Maintenance / Produksi">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="act_desc">Activities Description</label>
                    <textarea id="gi-purpose" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" rows="2" required data-translate-ph="true" data-i18n-ph="ph_act_desc" placeholder="Jelaskan aktivitas atau keperluan..."></textarea>
                </div>
                
                <div class="border-t border-slate-200 pt-5">
                    <div class="mb-4">
                        <label class="text-sm font-black text-indigo-800 uppercase tracking-wide"><i class="fas fa-box-open mr-1.5"></i> <span data-translate="true" data-i18n="item_list">Item List</span></label>
                    </div>
                    
                    <div id="gi-items-container" class="space-y-3"></div>

                    <div class="flex justify-between items-center mt-4 pb-20">
                        <button type="button" onclick="addGiRow()" class="bg-indigo-100 text-indigo-700 font-bold text-xs px-4 py-2 rounded-xl shadow-sm hover:bg-indigo-200 btn-animated">
                            <i class="fas fa-plus mr-1"></i> <span data-translate="true" data-i18n="add_row">Add Item Row</span>
                        </button>
                        <div id="gi-grand-total" class="hidden text-right font-black text-lg text-indigo-700 bg-indigo-50 px-4 py-2 rounded-xl border border-indigo-100 shadow-sm">Grand Total: Rp 0</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
            <button type="button" onclick="closeModal('modal-gi')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-translate="true" data-i18n="btn_cancel">Cancel</button>
            <button type="button" onclick="submitGi()" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700"><i class="fas fa-paper-plane mr-1.5"></i> <span id="btn-submit-gi-text" data-translate="true" data-i18n="btn_submit_form">Submit Form</span></button>
        </div>
    </div>
</div>

<div id="modal-reject" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[80] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl animate-slide-up overflow-hidden">
        <div class="bg-red-50 px-6 py-5 border-b border-red-100 flex justify-between items-center">
            <h3 class="font-bold text-red-700 tracking-tight"><i class="fas fa-times-circle mr-2"></i> <span data-translate="true" data-i18n="rej_req">Reject Request</span></h3>
            <button onclick="closeModal('modal-reject')" class="text-red-400 hover:text-red-600 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6">
            <input type="hidden" id="rej-id">
            <div class="mb-2">
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-translate="true">Alasan Penolakan / Reason</label>
                <textarea id="rej-reason" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-red-500 outline-none transition" rows="3" data-translate-ph="true" data-i18n-ph="ph_rej" placeholder="Reason for rejection..." required></textarea>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
            <button type="button" onclick="closeModal('modal-reject')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-translate="true" data-i18n="btn_cancel">Cancel</button>
            <button type="button" onclick="executeReject()" class="px-6 py-2.5 bg-red-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-red-700" data-translate="true" data-i18n="btn_conf_rej">Confirm Reject</button>
        </div>
    </div>
</div>

<div id="modal-action-photo" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] animate-slide-up overflow-hidden">
        <div class="bg-slate-800 px-6 py-5 flex justify-between items-center flex-none">
            <h3 class="font-bold text-white tracking-wide"><i class="fas fa-camera mr-2"></i> <span id="action-photo-title" data-translate="true">Photo Proof</span></h3>
            <button onclick="closeModal('modal-action-photo')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 custom-scroll">
            <input type="hidden" id="action-photo-id">
            <input type="hidden" id="action-photo-type">
            <p class="text-xs text-slate-500 mb-4" id="action-photo-desc" data-translate="true">Silakan lampirkan foto sebagai bukti transaksi ini.</p>
            
            <div class="flex gap-2 mb-4">
                <button type="button" onclick="toggleActionPhotoSource('file')" id="btn-act-file" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-indigo-600 text-white shadow-md transition btn-animated"><i class="fas fa-file-upload mr-1.5"></i> <span data-translate="true">Upload</span></button>
                <button type="button" onclick="toggleActionPhotoSource('camera')" id="btn-act-cam" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition btn-animated"><i class="fas fa-camera mr-1.5"></i> <span data-translate="true">Camera</span></button>
            </div>

            <div id="source-act-file" class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition flex items-center justify-center h-48 bg-slate-50 cursor-pointer" onclick="document.getElementById('input-act-photo').click()">
                <div class="space-y-3 pointer-events-none">
                    <i class="fas fa-cloud-upload-alt text-4xl text-slate-300"></i>
                    <p class="text-sm font-bold text-slate-500" id="act-file-name" data-translate="true">Click to upload image</p>
                </div>
                <input type="file" id="input-act-photo" accept="image/*" class="hidden" onchange="document.getElementById('act-file-name').innerText = this.files[0] ? this.files[0].name : 'Click to upload image'">
            </div>

            <div id="source-act-camera" class="hidden border border-slate-200 rounded-2xl overflow-hidden bg-black relative h-56 shadow-inner">
                <video id="camera-stream-act" class="w-full h-full object-cover transform scale-x-[-1]" autoplay playsinline></video>
                <canvas id="camera-canvas-act" class="hidden"></canvas>
                <img id="camera-preview-act" class="hidden w-full h-full object-cover">
                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-4 z-20">
                    <button type="button" onclick="takeActionSnapshot()" id="btn-capture-act" class="bg-white/90 backdrop-blur rounded-full p-3 shadow-lg text-slate-800 hover:text-indigo-600 hover:scale-110 transition duration-200"><i class="fas fa-camera text-2xl"></i></button>
                    <button type="button" onclick="retakeActionPhoto()" id="btn-retake-act" class="hidden bg-white/90 backdrop-blur rounded-full p-3 shadow-lg text-red-600 hover:scale-110 transition duration-200"><i class="fas fa-redo text-2xl"></i></button>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none">
            <button onclick="closeModal('modal-action-photo')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-translate="true">Cancel</button>
            <button onclick="submitActionWithPhoto()" id="btn-submit-action-photo" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700" data-translate="true">Process</button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    let giRowCount = 0; 
    let giData = [];
    let activeGiFilter = 'All';
    let videoStreamAct = null, capturedActBase64 = null, activeSourceAct = 'file';

    window.onload = () => {
        applyLanguage();
        loadData();
    };

    function loadData() {
        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getInventory'})})
        .then(r => r.json()).then(d => { inventoryData = Array.isArray(d) ? d : (d.data || []); });

        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getRequests'})})
        .then(r => r.json()).then(d => { 
            if(d.code === 401) { logoutAction(); return; }
            giData = Array.isArray(d) ? d : (d.data || []); 
            applyGiFilters(); 
        });
    }

    function closeAllDropdowns(e) { if(!e.target.closest('.relative.w-full')) { document.querySelectorAll('.dropdown-list').forEach(el => el.classList.add('hidden')); } }

    function setGiFilter(filter) {
        activeGiFilter = filter;
        document.querySelectorAll('.card-filter').forEach(el => el.classList.remove('card-filter-active'));
        if(filter === 'All') document.getElementById('card-gi-all').classList.add('card-filter-active');
        else if(filter === 'Pending Head') document.getElementById('card-gi-pending').classList.add('card-filter-active');
        else if(filter === 'Pending Warehouse') document.getElementById('card-gi-wh').classList.add('card-filter-active');
        else if(filter === 'Pending Receive') document.getElementById('card-gi-receive').classList.add('card-filter-active');
        else if(filter === 'Completed') document.getElementById('card-gi-done').classList.add('card-filter-active');
        applyGiFilters();
    }

    function applyGiFilters() {
        const term = document.getElementById('search-gi').value.toLowerCase();
        let filtered = giData.filter(r => (r.req_id || '').toLowerCase().includes(term) || (r.fullname || '').toLowerCase().includes(term) || (r.department || '').toLowerCase().includes(term) || (r.purpose || '').toLowerCase().includes(term) || (r.status || '').toLowerCase().includes(term) || (r.erp_gi_no || '').toLowerCase().includes(term) );
        if(activeGiFilter === 'Completed') { filtered = filtered.filter(r => r.status === 'Completed' || r.status === 'Rejected' || r.status === 'Cancelled' || r.status === 'Pending No GI (ERP)'); } 
        else if (activeGiFilter !== 'All') { filtered = filtered.filter(r => r.status === activeGiFilter); }
        renderGI(filtered);
    }

    function renderGI(data) {
        const tb = document.getElementById('gi-table-body'); 
        const cardContainer = document.getElementById('gi-card-container');
        const formatDt = (dtStr) => { if(!dtStr || dtStr === '0000-00-00 00:00:00' || dtStr === '-') return '-'; const d = new Date(dtStr); if(isNaN(d)) return dtStr; return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}); };
        const isWH = (['Warehouse', 'Administrator'].includes(currentUser.role) || (currentUser.role === 'TeamLeader' && currentUser.department.toLowerCase() === 'warehouse'));

        let countHead = 0, countWh = 0, countRecv = 0, countDone = 0;
        giData.forEach(r => {
            if(r.status === 'Pending Head') countHead++;
            if(r.status === 'Pending Warehouse') countWh++;
            if(r.status === 'Pending Receive') countRecv++;
            if(r.status === 'Completed' || r.status === 'Rejected' || r.status === 'Cancelled' || r.status === 'Pending No GI (ERP)') countDone++;
        });
        document.getElementById('stat-total').innerText = giData.length;
        document.getElementById('stat-pending-head').innerText = countHead;
        document.getElementById('stat-pending-wh').innerText = countWh;
        document.getElementById('stat-pending-recv').innerText = countRecv;
        document.getElementById('stat-done').innerText = countDone;

        if(!data || data.length === 0) { tb.innerHTML = `<tr><td colspan="6" class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</td></tr>`; cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</div>`; AutoTranslator.processDOM(); return; }

        let htmlArrayTable = []; let htmlArrayCard = [];
        data.forEach(r => {
            let sColor = 'bg-amber-100 text-amber-800 border-amber-200'; if(r.status==='Completed') sColor='bg-emerald-100 text-emerald-800 border-emerald-200'; if(r.status==='Rejected') sColor='bg-red-100 text-red-800 border-red-200'; if(r.status==='Cancelled') sColor='bg-slate-200 text-slate-600 border-slate-300'; if(r.status==='Pending Receive') sColor='bg-blue-100 text-blue-800 border-blue-200 animate-pulse'; if(r.status==='Pending No GI (ERP)') sColor='bg-purple-100 text-purple-800 border-purple-200 animate-pulse';
            let appHeadStr = r.app_head || 'Pending'; let appWhStr = r.app_wh || 'Pending';
            let l1Color = appHeadStr.includes('Approved') ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (appHeadStr.includes('Rejected') ? 'text-red-700 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200'); let l1Icon = appHeadStr.includes('Approved') ? 'fa-check-circle' : (appHeadStr.includes('Rejected') ? 'fa-times-circle' : 'fa-clock');
            let whColor = appWhStr.includes('Issued') ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (appWhStr.includes('Rejected') ? 'text-red-700 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200'); let whIcon = appWhStr.includes('Issued') ? 'fa-check-circle' : (appWhStr.includes('Rejected') ? 'fa-times-circle' : 'fa-clock');
            let rcColor = r.received_by ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-400 bg-slate-50 border-slate-200'; let rcIcon = r.received_by ? 'fa-check-double' : 'fa-hourglass-start';
            if (r.status === 'Pending Receive') { rcColor = 'text-blue-600 bg-blue-50 border-blue-200'; rcIcon = 'fa-box-open'; }
            if (r.status === 'Rejected' || r.status === 'Cancelled') { rcColor = 'text-slate-300 bg-slate-50 border-slate-100'; rcIcon = 'fa-minus'; l1Color = 'text-slate-300 bg-slate-50 border-slate-100'; whColor = 'text-slate-300 bg-slate-50 border-slate-100'; }

            let transStatus = r.status; if(currentLang === 'id') { if(r.status === 'Completed') transStatus = 'Selesai'; if(r.status === 'Rejected') transStatus = 'Ditolak'; if(r.status === 'Cancelled') transStatus = 'Dibatalkan'; if(r.status === 'Pending Head') transStatus = 'Menunggu Head'; if(r.status === 'Pending Warehouse') transStatus = 'Menunggu Gudang'; if(r.status === 'Pending Receive') transStatus = 'Menunggu Diterima'; if(r.status === 'Pending No GI (ERP)') transStatus = 'Pending ERP No'; } else { if(r.status === 'Pending No GI (ERP)') transStatus = 'Pending ERP No'; }
            
            let erpHtmlTable = '-'; let erpHtmlCard = '-';
            if (r.erp_gi_no) { erpHtmlTable = `<span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm block w-max">${r.erp_gi_no}</span>`; erpHtmlCard = `<span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm text-sm">${r.erp_gi_no}</span>`; }
            else if (r.status === 'Pending No GI (ERP)') {
                if (isWH) {
                    erpHtmlTable = `<div class="flex flex-col gap-1.5 w-28"><input type="text" id="erp-input-${r.req_id}" class="border border-slate-300 rounded p-1.5 text-[10px] focus:ring-2 focus:ring-indigo-500 outline-none text-center font-bold text-slate-700" placeholder="Input ERP No"><button onclick="confirmErp('${r.req_id}')" class="bg-emerald-500 text-white px-2 py-1.5 rounded hover:bg-emerald-600 shadow-sm transition text-[10px] font-bold btn-animated"><i class="fas fa-check mr-1"></i> <span data-translate="true">CONFIRM</span></button></div>`;
                    erpHtmlCard = `<div class="flex items-center gap-2"><input type="text" id="erp-input-card-${r.req_id}" class="border border-slate-300 rounded p-1.5 text-[10px] w-24 focus:ring-2 focus:ring-indigo-500 outline-none text-center font-bold text-slate-700" placeholder="Input ERP No"><button onclick="confirmErpCard('${r.req_id}')" class="bg-emerald-500 text-white px-2.5 py-1.5 rounded hover:bg-emerald-600 shadow-sm transition text-[10px] font-bold"><i class="fas fa-check"></i></button></div>`;
                } else {
                    erpHtmlTable = `<span class="text-[9px] text-amber-500 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-200 uppercase tracking-wider block w-max"><i class="fas fa-clock mr-1"></i> <span data-translate="true">Tunggu WH</span></span>`;
                    erpHtmlCard = `<span class="text-[9px] text-amber-500 font-bold bg-amber-50 px-2 py-1 rounded border border-amber-200 uppercase tracking-wider"><i class="fas fa-clock mr-1"></i> <span data-translate="true">Tunggu WH</span></span>`;
                }
            }

            const l1Time = formatDt(r.head_time);
            const whTime = formatDt(r.wh_time);
            const rcTime = formatDt(r.receive_time);

            const statusHTMLTable = `
                <div class="flex flex-col items-center w-[180px] mx-auto">
                    <span class="status-badge border shadow-sm ${sColor} mb-3 w-full text-center py-2 px-2 leading-snug flex items-center justify-center min-h-[36px]" data-translate="true">${transStatus}</span>
                    <div class="w-full flex flex-col gap-2 text-[9px] text-left">
                        <div class="flex flex-col border p-2.5 rounded-xl ${l1Color} shadow-sm transition-all hover:shadow-md">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5"><span class="font-black uppercase opacity-75"><i class="fas ${l1Icon} mr-1"></i> <span data-translate="true">Dept Head</span></span></div>
                            <div class="font-semibold truncate mb-0.5" title="${appHeadStr}">${appHeadStr.replace('Approved by ', '').replace('Rejected by ', '')}</div>
                            ${l1Time !== '-' ? `<div class="text-[8px] opacity-75 font-mono mt-0.5 flex items-center gap-1"><i class="far fa-clock"></i> ${l1Time}</div>` : ''}
                        </div>
                        <div class="flex flex-col border p-2.5 rounded-xl ${whColor} shadow-sm transition-all hover:shadow-md">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5"><span class="font-black uppercase opacity-75"><i class="fas ${whIcon} mr-1"></i> <span data-translate="true">Warehouse</span></span>${r.issue_photo && r.issue_photo !== '0' ? `<button onclick="viewPhoto('${r.issue_photo}')" class="text-blue-600 bg-white border border-blue-200 px-1.5 py-0.5 rounded shadow-sm hover:bg-blue-50 transition"><i class="fas fa-camera"></i></button>` : ''}</div>
                            <div class="font-semibold truncate mb-0.5" title="${appWhStr}">${appWhStr.replace('Issued by ', '').replace('Rejected by ', '')}</div>
                            ${whTime !== '-' ? `<div class="text-[8px] opacity-75 font-mono mt-0.5 flex items-center gap-1"><i class="far fa-clock"></i> ${whTime}</div>` : ''}
                        </div>
                        <div class="flex flex-col border p-2.5 rounded-xl ${rcColor} shadow-sm transition-all hover:shadow-md">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5"><span class="font-black uppercase opacity-75"><i class="fas ${rcIcon} mr-1"></i> <span data-translate="true">User Rcv</span></span>${r.receive_photo && r.receive_photo !== '0' ? `<button onclick="viewPhoto('${r.receive_photo}')" class="text-emerald-600 bg-white border border-emerald-200 px-1.5 py-0.5 rounded shadow-sm hover:bg-emerald-50 transition"><i class="fas fa-camera"></i></button>` : ''}</div>
                            <div class="font-semibold truncate mb-0.5" title="${r.received_by || '-'}">${r.received_by ? r.received_by : (r.status === 'Pending Receive' ? 'Waiting...' : '-')}</div>
                            ${rcTime !== '-' ? `<div class="text-[8px] opacity-75 font-mono mt-0.5 flex items-center gap-1"><i class="far fa-clock"></i> ${rcTime}</div>` : ''}
                        </div>
                    </div>
                </div>`;

            let itemsHtmlTable = '<div class="flex flex-col gap-2 mt-2 pt-2 border-t border-slate-100">';
            let itemsHtmlCard = '<div class="flex flex-col gap-2 mt-2">';
            let grandTotal = 0;

            (r.items || []).forEach(i => {
                const itemPrice = parseFloat(i.price || 0);
                const itemTotal = itemPrice * parseInt(i.qty);
                grandTotal += itemTotal;
                
                let priceHtml = itemPrice > 0 ? `<div class="flex gap-2 text-[9px] text-slate-500 mt-1.5 pt-1.5 border-t border-indigo-50"><div class="flex-1"><span class="opacity-70">Harga:</span> <span class="font-bold text-indigo-800">Rp ${itemPrice.toLocaleString('id-ID')}</span></div><div class="flex-1 text-right"><span class="opacity-70">Total:</span> <span class="font-bold text-indigo-900">Rp ${itemTotal.toLocaleString('id-ID')}</span></div></div>` : '';

                const itemBlock = `<div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col hover:border-indigo-300 transition-colors"><div class="flex justify-between items-start mb-2"><span class="text-[11px] font-bold text-indigo-700 pr-2 leading-snug w-full break-words whitespace-normal">${i.code} - ${i.name}</span><span class="text-xs font-black text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg shadow-inner border border-slate-200 whitespace-nowrap ml-2">${i.qty} <span class="text-[9px] font-bold text-slate-500">${i.uom}</span></span></div><div class="flex gap-2 text-[9px] text-slate-500 uppercase mt-1"><div class="bg-slate-50 px-2.5 py-1.5 rounded-md shadow-sm border border-slate-100 flex-1 flex items-center justify-between"><span class="opacity-70">RC</span> <span class="font-bold text-slate-700">${i.reason_code || '-'}</span></div><div class="bg-slate-50 px-2.5 py-1.5 rounded-md shadow-sm border border-slate-100 flex-1 flex items-center justify-between"><span class="opacity-70">CC</span> <span class="font-bold text-slate-700">${i.cost_center || '-'}</span></div></div>${priceHtml}</div>`;
                itemsHtmlTable += itemBlock; itemsHtmlCard += itemBlock;
            });
            
            if(grandTotal > 0) {
                const gtHtml = `<div class="text-right font-black text-indigo-700 bg-indigo-50 px-3 py-2 rounded-xl border border-indigo-100 mt-2 shadow-sm">Grand Total: Rp ${grandTotal.toLocaleString('id-ID')}</div>`;
                itemsHtmlTable += gtHtml; itemsHtmlCard += gtHtml;
            }

            itemsHtmlTable += '</div>'; itemsHtmlCard += '</div>';

            let actionBtnsTable = []; let actionBtnsCard = [];
            if(r.status === 'Pending Head' && ['SectionHead','TeamLeader'].includes(currentUser.role)) {
                actionBtnsTable.push(`<button onclick="updateGI('${r.req_id}','approve')" class="text-xs bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mb-1.5"><i class="fas fa-check-circle"></i> <span data-translate="true" data-i18n="btn_appr">Approve</span></button>`, `<button onclick="openRej('${r.req_id}')" class="text-xs bg-white border border-red-300 text-red-600 px-4 py-2 rounded-xl shadow-sm hover:bg-red-50 transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-times-circle"></i> <span data-translate="true" data-i18n="btn_rej">Reject</span></button>`);
                actionBtnsCard.push(`<div class="grid grid-cols-2 gap-3 mt-3">`, actionBtnsTable[0], actionBtnsTable[1], `</div>`);
            } else if (r.status === 'Pending Warehouse' && isWH) {
                const btnIss = `<button onclick="openActionPhotoModal('${r.req_id}', 'issue')" class="text-xs bg-gradient-to-r from-indigo-500 to-blue-600 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-box-open text-lg"></i> <span data-translate="true" data-i18n="btn_iss">Issue Items</span></button>`;
                actionBtnsTable.push(btnIss); actionBtnsCard.push(`<div class="mt-3 w-full">`, btnIss, `</div>`);
            } else if (r.status === 'Pending Receive' && r.username === currentUser.username) {
                const btnRecv = `<button onclick="openActionPhotoModal('${r.req_id}', 'receive')" class="text-xs bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-hand-holding-box text-lg"></i> <span data-translate="true" data-i18n="btn_confirm_recv">Confirm Receive</span></button>`;
                actionBtnsTable.push(btnRecv); actionBtnsCard.push(`<div class="mt-3 w-full">`, btnRecv, `</div>`);
            }
            if (r.status === 'Pending Head' && r.username === currentUser.username) {
                actionBtnsTable.push(`<button onclick="openEditGiModal('${r.req_id}')" class="text-xs bg-gradient-to-r from-amber-400 to-orange-500 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mt-2"><i class="fas fa-edit"></i> <span data-translate="true">Edit</span></button>`, `<button onclick="cancelGI('${r.req_id}')" class="text-xs bg-gradient-to-r from-rose-500 to-red-600 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mt-2"><i class="fas fa-ban"></i> <span data-translate="true">Cancel</span></button>`);
                actionBtnsCard.push(`<div class="grid grid-cols-2 gap-2 mt-2 w-full">`, actionBtnsTable[actionBtnsTable.length-2], actionBtnsTable[actionBtnsTable.length-1], `</div>`);
            }

            let btnTable = actionBtnsTable.length > 0 ? `<div class="flex flex-col w-[120px] mx-auto">${actionBtnsTable.join('')}</div>` : '<span class="text-slate-300 text-center block">-</span>';
            let btnCard = actionBtnsCard.length > 0 ? actionBtnsCard.join('') : '';

            htmlArrayTable.push(`<tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-5 align-top"><div class="font-black text-xs text-indigo-700 mb-1">${r.req_id}</div><div class="text-[10px] text-slate-400 font-mono font-medium">${r.created_at}</div></td>
                <td class="px-6 py-5 align-top">${erpHtmlTable}</td>
                <td class="px-6 py-5 align-top"><div class="font-bold text-xs text-slate-800">${r.fullname}</div><div class="text-[10px] text-slate-500 font-medium mb-1.5">${r.department}</div><div class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md inline-block font-bold border border-slate-200 shadow-sm">Sec: ${r.section || '-'}</div></td>
                <td class="px-6 py-5 align-top min-w-[350px] max-w-[450px] whitespace-normal"><div class="text-xs text-slate-700 font-medium mb-3 bg-indigo-50/60 p-3 rounded-xl border border-indigo-100 shadow-sm"><span class="text-[9px] text-indigo-500 uppercase font-black tracking-wider block mb-1.5"><i class="fas fa-tasks mr-1"></i> <span data-translate="true">Act. Desc:</span></span> <span class="italic leading-relaxed text-indigo-900">"${r.purpose}"</span></div>${itemsHtmlTable}</td>
                <td class="px-6 py-5 align-top text-center">${statusHTMLTable}</td>
                <td class="px-6 py-5 align-top text-right">${btnTable}</td>
            </tr>`);

            htmlArrayCard.push(`<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 relative transition-all hover:shadow-md">
                <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-4"><div><div class="font-black text-sm text-indigo-700 mb-0.5">${r.req_id}</div><div class="text-[10px] text-slate-400 font-mono">${r.created_at}</div></div><div class="text-right"><div class="font-bold text-xs text-slate-800">${r.fullname}</div><div class="text-[10px] text-slate-500">${r.department} <span class="bg-slate-100 px-1 py-0.5 rounded ml-1">Sec: ${r.section || '-'}</span></div></div></div>
                <div class="mb-4 flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-100"><span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider" data-translate="true">No GI ERP:</span><div>${erpHtmlCard}</div></div>
                <div class="mb-5"><div class="text-[10px] text-indigo-500 uppercase font-black tracking-wider mb-2"><i class="fas fa-tasks mr-1"></i> <span data-translate="true">Act. Desc:</span></div><div class="text-xs italic text-indigo-900 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 shadow-inner">"${r.purpose}"</div></div>
                <div class="mb-5"><div class="text-[10px] text-slate-400 uppercase font-black tracking-wider mb-2"><i class="fas fa-box-open mr-1"></i> <span data-translate="true">Items Requested:</span></div>${itemsHtmlCard}</div>
                <div class="border-t border-slate-100 pt-4">
                    <div class="grid grid-cols-3 gap-2 text-[9px] mb-2">
                        <div class="flex flex-col border p-2 rounded-xl ${l1Color} shadow-sm justify-between"><div class="font-black uppercase opacity-75 mb-1"><i class="fas ${l1Icon}"></i> L1</div><div><div class="font-semibold truncate mb-0.5">${appHeadStr.replace('Approved by ', '').replace('Rejected by ', '')}</div>${l1Time !== '-' ? `<div class="text-[7.5px] opacity-75 font-mono">${l1Time}</div>` : ''}</div></div>
                        <div class="flex flex-col border p-2 rounded-xl ${whColor} shadow-sm justify-between"><div class="font-black uppercase opacity-75 mb-1"><i class="fas ${whIcon}"></i> WH</div><div><div class="font-semibold truncate mb-0.5">${appWhStr.replace('Issued by ', '').replace('Rejected by ', '')}</div>${whTime !== '-' ? `<div class="text-[7.5px] opacity-75 font-mono">${whTime}</div>` : ''}</div></div>
                        <div class="flex flex-col border p-2 rounded-xl ${rcColor} shadow-sm justify-between"><div class="font-black uppercase opacity-75 mb-1"><i class="fas ${rcIcon}"></i> RCV</div><div><div class="font-semibold truncate mb-0.5">${r.received_by || '-'}</div>${rcTime !== '-' ? `<div class="text-[7.5px] opacity-75 font-mono">${rcTime}</div>` : ''}</div></div>
                    </div>
                ${btnCard}</div></div>`);
        });

        tb.innerHTML = htmlArrayTable.join('');
        cardContainer.innerHTML = htmlArrayCard.join('');
        AutoTranslator.processDOM();
    }

    function confirmErp(reqId) {
        const erpNo = document.getElementById(`erp-input-${reqId}`).value.trim();
        if (!erpNo) { showCustomAlert("Warning", "Nomor GI ERP tidak boleh kosong."); return; }
        showCustomConfirm("Konfirmasi", "Yakin menyimpan Nomor GI ERP ini? Status akan menjadi Completed.", () => {
            fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'updateStatus', act: 'complete_erp', reqId: reqId, erp_gi_no: erpNo, role: currentUser.role, fullname: currentUser.fullname }) })
            .then(r => r.json()).then(res => { if(res.code===401){logoutAction();return;} if (res.success) { loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); } });
        });
    }

    function confirmErpCard(reqId) {
        const erpNo = document.getElementById(`erp-input-card-${reqId}`).value.trim();
        if (!erpNo) { showCustomAlert("Warning", "Nomor GI ERP tidak boleh kosong."); return; }
        showCustomConfirm("Konfirmasi", "Yakin menyimpan Nomor GI ERP ini? Status akan menjadi Completed.", () => {
            fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'updateStatus', act: 'complete_erp', reqId: reqId, erp_gi_no: erpNo, role: currentUser.role, fullname: currentUser.fullname }) })
            .then(r => r.json()).then(res => { if(res.code===401){logoutAction();return;} if (res.success) { loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); } });
        });
    }

    function filterGI() { applyGiFilters(); }

    function openGiModal() {
        document.getElementById('gi-action-type').value = 'submit';
        document.getElementById('gi-edit-id').value = '';
        document.getElementById('disp-req-name').innerText = currentUser.fullname + " / " + currentUser.department;
        document.getElementById('gi-section').value = '';
        document.getElementById('gi-purpose').value = '';
        document.getElementById('gi-items-container').innerHTML = '';
        document.getElementById('gi-grand-total').classList.add('hidden');
        giRowCount = 0; 
        document.getElementById('modal-gi-title').innerHTML = `<i class="fas fa-file-invoice text-indigo-600 mr-2 text-lg"></i> <span data-translate="true" data-i18n="form_gi">Form Good Issue</span>`;
        const submitBtn = document.getElementById('btn-submit-gi-text').parentNode;
        submitBtn.innerHTML = `<i class="fas fa-paper-plane mr-1.5"></i> <span id="btn-submit-gi-text" data-translate="true" data-i18n="btn_submit_form">Submit Form</span>`;
        submitBtn.className = "px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700";
        addGiRow();
        openModal('modal-gi');
        AutoTranslator.processDOM();
    }

    function openEditGiModal(reqId) {
        const req = giData.find(r => r.req_id === reqId);
        if(!req) return;
        document.getElementById('gi-action-type').value = 'edit';
        document.getElementById('gi-edit-id').value = reqId;
        document.getElementById('disp-req-name').innerText = currentUser.fullname + " / " + currentUser.department;
        document.getElementById('gi-section').value = req.section || '';
        document.getElementById('gi-purpose').value = req.purpose || '';
        document.getElementById('gi-items-container').innerHTML = '';
        document.getElementById('gi-grand-total').classList.add('hidden');
        giRowCount = 0;
        
        (req.items || []).forEach(it => {
            addGiRow();
            const row = document.getElementById(`gi-row-${giRowCount}`);
            row.querySelector('.gi-item-display').value = it.code + ' - ' + it.name; 
            row.querySelector('.gi-item-code').value = it.code;
            row.querySelector('.gi-item-name').value = it.name;
            row.querySelector('.gi-qty').value = it.qty;
            row.querySelector('.gi-uom').value = it.uom;
            row.querySelector('.gi-reason').value = it.reason_code || '';
            row.querySelector('.gi-cost').value = it.cost_center || '';
            
            const rawPriceEl = row.querySelector('.gi-price-raw');
            const dispPriceEl = row.querySelector('.gi-price-display');
            if(rawPriceEl) rawPriceEl.value = it.price || 0;
            if(dispPriceEl) dispPriceEl.value = 'Rp ' + parseFloat(it.price || 0).toLocaleString('id-ID');

            const invItem = inventoryData.find(inv => inv.item_code === it.code);
            if(invItem) { row.querySelector('.gi-qty').max = invItem.stock; row.querySelector('.gi-qty').title = "Max stock: " + invItem.stock; row.querySelector('.gi-stock').value = invItem.stock; } 
            else { row.querySelector('.gi-stock').value = 0; }
        });
        
        calculateGiTotal();

        document.getElementById('modal-gi-title').innerHTML = `<i class="fas fa-edit text-amber-500 mr-2 text-lg"></i> <span data-translate="true">Edit Good Issue Form</span>`;
        const submitBtn = document.getElementById('btn-submit-gi-text').parentNode;
        submitBtn.innerHTML = `<i class="fas fa-save mr-1.5"></i> <span id="btn-submit-gi-text" data-translate="true">Update Form</span>`;
        submitBtn.className = "px-8 py-2.5 bg-amber-500 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-amber-600";
        openModal('modal-gi'); AutoTranslator.processDOM();
    }

    function addGiRow() {
        giRowCount++;
        const d = document.createElement('div');
        d.className = "bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative transition hover:border-indigo-200";
        d.id = `gi-row-${giRowCount}`;
        
        let priceHtml = `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3 pt-3 border-t border-slate-100">
            <div><label class="block text-[9px] font-bold text-slate-400 uppercase mb-1.5" data-translate="true" data-i18n="th_price">Harga</label><input type="hidden" class="gi-price-raw" value="0"><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs gi-price-display text-slate-500 font-bold" placeholder="0" readonly tabindex="-1"></div>
            <div><label class="block text-[9px] font-bold text-slate-400 uppercase mb-1.5" data-translate="true" data-i18n="total_price">Total</label><input type="text" class="w-full bg-indigo-50 border border-indigo-100 rounded-xl p-3 text-xs font-black text-indigo-700 gi-total-display" placeholder="Rp 0" readonly tabindex="-1"></div>
        </div>`;

        d.innerHTML = `
            <button type="button" onclick="document.getElementById('${d.id}').remove(); calculateGiTotal();" class="absolute -top-2.5 -right-2.5 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow-md btn-animated z-10"><i class="fas fa-times text-[10px]"></i></button>
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-3">
                    <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true">Item</label>
                    <div class="relative w-full">
                        <input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs gi-item-display focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer bg-slate-50 font-medium transition" data-translate-ph="true" data-i18n-ph="ph_search_item" placeholder="Search Item..." onfocus="showDropdown(this, 'gi')" onkeyup="filterDropdown(this, 'gi')" autocomplete="off" required>
                        <input type="hidden" class="gi-item-code">
                        <input type="hidden" class="gi-item-name">
                        <i class="fas fa-search absolute right-3 top-2.5 text-slate-400 pointer-events-none text-[12px]"></i>
                        <div class="dropdown-list hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-2xl mt-1.5 max-h-48 overflow-y-auto dropdown-scroll left-0"></div>
                    </div>
                </div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true" data-i18n="curr_stk_short">Curr. Stock</label><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gi-stock text-slate-500 font-bold" placeholder="0" readonly tabindex="-1"></div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true" data-i18n="req_qty">Req Qty</label><input type="number" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-qty focus:ring-2 focus:ring-indigo-500 outline-none font-black text-slate-700 transition" placeholder="Qty" required oninput="calculateGiTotal()"></div>
                <div class="sm:col-span-1"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">UoM</label><input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gi-uom text-slate-500 font-bold" placeholder="UoM" readonly tabindex="-1"></div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-translate="true">Reason Code</label><input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-reason focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition" data-translate-ph="true" placeholder="Code"></div>
                <div class="sm:col-span-2"><label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5 text-rose-500" data-translate="true" data-i18n="cost_ctr">Cost Center *</label><input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-cost focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition" data-translate-ph="true" placeholder="Cost Ctr" required></div>
            </div>
            ${priceHtml}
        `;
        document.getElementById('gi-items-container').appendChild(d);
        AutoTranslator.processDOM();
    }

    function calculateGiTotal() {
        let grandTotal = 0;
        document.querySelectorAll('#gi-items-container > div').forEach(r => {
            const qty = parseFloat(r.querySelector('.gi-qty').value) || 0;
            const priceEl = r.querySelector('.gi-price-raw');
            const price = priceEl ? parseFloat(priceEl.value) || 0 : 0;
            const total = qty * price;
            grandTotal += total;
            
            const totalEl = r.querySelector('.gi-total-display');
            if(totalEl) totalEl.value = 'Rp ' + total.toLocaleString('id-ID');
        });
        const gtEl = document.getElementById('gi-grand-total');
        if(gtEl) {
            if(grandTotal > 0) { gtEl.innerText = 'Grand Total: Rp ' + grandTotal.toLocaleString('id-ID'); gtEl.classList.remove('hidden'); }
            else { gtEl.classList.add('hidden'); }
        }
    }

    function submitGi() {
        const section = document.getElementById('gi-section').value;
        const purpose = document.getElementById('gi-purpose').value;
        if(!section || !purpose) { showCustomAlert("Info", t('err_req')); return; }

        const rows = document.querySelectorAll('#gi-items-container > div');
        let items = []; let valid = true;
        
        rows.forEach(r => {
            const code = r.querySelector('.gi-item-code').value; const name = r.querySelector('.gi-item-name').value;
            const qty = r.querySelector('.gi-qty').value; const uom = r.querySelector('.gi-uom').value;
            const reason = r.querySelector('.gi-reason').value; const cost = r.querySelector('.gi-cost').value.trim();
            const max = r.querySelector('.gi-qty').max;
            const price = r.querySelector('.gi-price-raw') ? r.querySelector('.gi-price-raw').value : 0;

            if(code && qty > 0) {
                if(parseInt(qty) > parseInt(max)) { showCustomAlert("Error", "Jumlah melebihi stok untuk " + name); valid = false; }
                if(cost === '') { showCustomAlert("Warning", "Cost Center wajib diisi"); valid = false; }
                items.push({ code: code, name: name, qty: qty, uom: uom, reason_code: reason, cost_center: cost, price: price });
            }
        });
        
        if(!valid) return;
        if(items.length === 0) { showCustomAlert("Error", "Minimal 1 barang harus diisi dengan benar."); return; }
        
        const actionType = document.getElementById('gi-action-type').value;
        const reqId = document.getElementById('gi-edit-id').value;
        const btn = document.getElementById('btn-submit-gi-text').parentNode;
        const orgTxt = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1.5"></i> <span data-translate="true">Memproses...</span>`; AutoTranslator.processDOM();

        fetch('api/gis.php', { method:'POST', body:JSON.stringify({action: actionType === 'edit' ? 'editRequest' : 'submitRequest', reqId: reqId, username: currentUser.username, fullname: currentUser.fullname, department: currentUser.department, section: section, purpose: purpose, items: items})})
        .then(r=>r.json()).then(res => { 
            btn.disabled = false; btn.innerHTML = orgTxt;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success){ closeModal('modal-gi'); loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); } 
        }).catch(e => { btn.disabled = false; btn.innerHTML = orgTxt; showCustomAlert("Error", t('err_conn')); });
    }

    function cancelGI(reqId) {
        showCustomConfirm("Cancel Request", "Anda yakin ingin membatalkan permintaan ini?", () => {
            fetch('api/gis.php', {method:'POST', body:JSON.stringify({ action: 'cancelRequest', reqId: reqId, username: currentUser.username, fullname: currentUser.fullname })}).then(r=>r.json()).then(res => { 
                if(res.code === 401) { logoutAction(); return; }
                if(res.success) { loadData(); showCustomAlert("Success", res.message); } else showCustomAlert("Error", res.message); 
            });
        });
    }

    function updateGI(id, act, reason='') {
        if(act==='approve') {
            showCustomConfirm("Approve Request", "Approve request ini?", () => {
                fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'updateStatus', reqId:id, act:act, role:currentUser.role, fullname:currentUser.fullname, reason:reason})}).then(r=>r.json()).then(res => { if(res.success) loadData(); else showCustomAlert("Error", res.message); });
            });
        } else {
            fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'updateStatus', reqId:id, act:act, role:currentUser.role, fullname:currentUser.fullname, reason:reason})}).then(r=>r.json()).then(res => { if(res.success) loadData(); else showCustomAlert("Error", res.message); });
        }
    }

    function openRej(id) { document.getElementById('rej-id').value = id; document.getElementById('rej-reason').value=''; openModal('modal-reject'); AutoTranslator.processDOM(); }
    function executeReject() { const id = document.getElementById('rej-id').value; const r = document.getElementById('rej-reason').value; if(r){ closeModal('modal-reject'); updateGI(id, 'reject', r); } else showCustomAlert('Error', 'Reason required'); }

    function toggleActionPhotoSource(source) {
        activeSourceAct = source;
        const btnFile = document.getElementById('btn-act-file'), btnCam = document.getElementById('btn-act-cam');
        const contFile = document.getElementById('source-act-file'), contCam = document.getElementById('source-act-camera');
        if(source === 'camera') {
            btnCam.classList.replace('bg-slate-100','bg-indigo-600'); btnCam.classList.replace('text-slate-600','text-white');
            btnFile.classList.replace('bg-indigo-600','bg-slate-100'); btnFile.classList.replace('text-white','text-slate-600');
            contFile.classList.add('hidden'); contCam.classList.remove('hidden');
            startCamera();
        } else {
            btnFile.classList.replace('bg-slate-100','bg-indigo-600'); btnFile.classList.replace('text-slate-600','text-white');
            btnCam.classList.replace('bg-indigo-600','bg-slate-100'); btnCam.classList.replace('text-white','text-slate-600');
            contCam.classList.add('hidden'); contFile.classList.remove('hidden');
            stopCamera();
        }
    }
    function openActionPhotoModal(id, actType) {
        document.getElementById('action-photo-id').value = id; document.getElementById('action-photo-type').value = actType;
        document.getElementById('input-act-photo').value = ''; document.getElementById('act-file-name').innerText = 'Click to upload image';
        if (actType === 'issue') {
            document.getElementById('action-photo-title').innerHTML = '<i class="fas fa-box-open mr-2"></i> <span data-translate="true">Issue Items (Warehouse)</span>';
            document.getElementById('action-photo-desc').innerHTML = '<span data-translate="true">Harap lampirkan bukti foto barang fisik yang disiapkan/dikeluarkan dari gudang.</span>';
        } else {
            document.getElementById('action-photo-title').innerHTML = '<i class="fas fa-hand-holding-box mr-2"></i> <span data-translate="true">Confirm Receive (User)</span>';
            document.getElementById('action-photo-desc').innerHTML = '<span data-translate="true">Harap lampirkan bukti foto barang telah diterima dengan baik.</span>';
        }
        toggleActionPhotoSource('file'); openModal('modal-action-photo'); AutoTranslator.processDOM();
    }
    async function startCamera() {
        const video = document.getElementById('camera-stream-act'); const preview = document.getElementById('camera-preview-act');
        preview.classList.add('hidden'); video.classList.remove('hidden');
        document.getElementById('btn-capture-act').classList.remove('hidden'); document.getElementById('btn-retake-act').classList.add('hidden');
        capturedActBase64 = null;
        try { const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }); video.srcObject = stream; videoStreamAct = stream; } 
        catch (err) { showCustomAlert("Camera Error", "Kamera tidak bisa diakses. Gunakan fitur Upload File."); toggleActionPhotoSource('file'); }
    }
    function stopCamera() { if (videoStreamAct) { videoStreamAct.getTracks().forEach(track => track.stop()); videoStreamAct = null; } }
    function takeActionSnapshot() {
        const video = document.getElementById('camera-stream-act'); const canvas = document.getElementById('camera-canvas-act'); const preview = document.getElementById('camera-preview-act');
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            capturedActBase64 = canvas.toDataURL('image/jpeg', 0.8);
            preview.src = capturedActBase64; preview.classList.remove('hidden'); video.classList.add('hidden');
            document.getElementById('btn-capture-act').classList.add('hidden'); document.getElementById('btn-retake-act').classList.remove('hidden');
        }
    }
    function retakeActionPhoto() {
        capturedActBase64 = null; document.getElementById('camera-preview-act').classList.add('hidden');
        document.getElementById('camera-stream-act').classList.remove('hidden');
        document.getElementById('btn-capture-act').classList.remove('hidden'); document.getElementById('btn-retake-act').classList.add('hidden');
    }
    async function submitActionWithPhoto() {
        const reqId = document.getElementById('action-photo-id').value; const actType = document.getElementById('action-photo-type').value;
        const btn = document.getElementById('btn-submit-action-photo'); const orgHtml = btn.innerHTML;
        let base64Data = null;
        if (activeSourceAct === 'camera' && capturedActBase64) { base64Data = capturedActBase64; } 
        else { const fileInput = document.getElementById('input-act-photo'); if (fileInput.files.length > 0) { base64Data = await new Promise((resolve) => { const reader = new FileReader(); reader.onload = (e) => resolve(e.target.result); reader.readAsDataURL(fileInput.files[0]); }); } }
        if (!base64Data) { showCustomAlert("Error", "Harap lampirkan foto bukti terlebih dahulu!"); return; }

        btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> <span data-translate="true">Memproses...</span>`; AutoTranslator.processDOM();
        try {
            const compressedBase64 = await compressImage(base64Data);
            const response = await fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'updateStatus', reqId: reqId, act: actType, role: currentUser.role, department: currentUser.department, username: currentUser.username, fullname: currentUser.fullname, photoBase64: compressedBase64 }) });
            const res = await response.json();
            btn.disabled = false; btn.innerHTML = orgHtml;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) { closeModal('modal-action-photo'); loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); }
        } catch (err) { btn.disabled = false; btn.innerHTML = orgHtml; showCustomAlert("Error", "Gagal memproses foto atau jaringan bermasalah."); }
    }
</script>