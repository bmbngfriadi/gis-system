<?php include 'includes/header.php'; ?>

<div id="view-inv" class="space-y-6 animate-slide-up">

    <div id="inv-insights" class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-2 hidden">
        <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden shine-effect flex flex-col justify-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-2xl hover:scale-[1.02] cursor-default">
            <div class="text-[10px] uppercase font-bold opacity-80 mb-1 transition-opacity duration-300 group-hover:opacity-100" data-translate="true">Total Asset Value</div>
            <div class="text-2xl font-black truncate" id="inv-stat-value">Rp 0</div>
            <i class="fas fa-coins absolute -right-3 -bottom-4 text-7xl opacity-20 transition-transform duration-500 ease-in-out group-hover:scale-125 group-hover:-rotate-12"></i>
        </div>
        <div class="group bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden shine-effect flex flex-col justify-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-2xl hover:scale-[1.02] cursor-default">
            <div class="text-[10px] uppercase font-bold opacity-80 mb-1 transition-opacity duration-300 group-hover:opacity-100" data-translate="true">Total Master Items</div>
            <div class="text-2xl font-black truncate" id="inv-stat-items">0 Items</div>
            <i class="fas fa-boxes absolute -right-3 -bottom-4 text-7xl opacity-20 transition-transform duration-500 ease-in-out group-hover:scale-125 group-hover:rotate-12"></i>
        </div>
        <div class="group bg-gradient-to-br from-rose-500 to-red-600 rounded-2xl p-5 text-white shadow-lg relative overflow-hidden shine-effect flex flex-col justify-center transition-all duration-300 ease-in-out hover:-translate-y-2 hover:shadow-2xl hover:scale-[1.02] cursor-default">
            <div class="text-[10px] uppercase font-bold opacity-80 mb-1 transition-opacity duration-300 group-hover:opacity-100" data-translate="true">Low Stock Alert (<10)</div>
            <div class="text-2xl font-black truncate animate-pulse group-hover:animate-none" id="inv-stat-low">0 Items</div>
            <i class="fas fa-exclamation-triangle absolute -right-3 -bottom-4 text-7xl opacity-20 transition-transform duration-500 ease-in-out group-hover:scale-125 group-hover:rotate-12"></i>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mt-4">
        <div>
            <h2 class="text-lg font-bold text-slate-700" data-translate="true" data-i18n="mast_inv">Master Inventory</h2>
            <p class="text-xs text-slate-500" data-translate="true" data-i18n="desc_inv">Manage warehouse items and stock.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                <input type="text" id="search-inv" onkeyup="filterInventory()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition" data-translate-ph="true" data-i18n-ph="ph_search_inv" placeholder="Search Item (Code/Name)...">
            </div>

            <div class="grid grid-cols-3 sm:flex gap-2 w-full sm:w-auto">
                <?php if($isAdmin): ?>
                <button id="btn-tpl-item" onclick="downloadItemTemplate()" class="w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-slate-50 btn-animated flex justify-center items-center" title="Template"><i class="fas fa-download"></i> <span class="hidden sm:inline ml-1" data-translate="true" data-i18n="btn_template">Template</span></button>
                <button id="btn-imp-item" onclick="document.getElementById('import-item-file').click()" class="w-full sm:w-auto bg-blue-600 text-white px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-blue-700 btn-animated flex justify-center items-center" title="Import"><i class="fas fa-file-import"></i> <span class="hidden sm:inline ml-1" data-translate="true" data-i18n="btn_import_items">Import</span></button>
                <input type="file" id="import-item-file" accept=".xlsx, .xls" class="hidden" onchange="handleImportItems(event)">
                <button id="btn-exp-item" onclick="exportItems()" class="w-full sm:w-auto bg-indigo-600 text-white px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-700 btn-animated flex justify-center items-center" title="Export"><i class="fas fa-file-export"></i> <span class="hidden sm:inline ml-1" data-translate="true" data-i18n="btn_export_items">Export</span></button>
                <?php endif; ?>
            </div>
            
            <?php if($isAdmin || in_array('item_add', $rights)): ?>
            <button id="btn-add-item" onclick="openItemModal()" class="w-full sm:w-auto bg-emerald-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-emerald-700 btn-animated flex justify-center items-center whitespace-nowrap"><i class="fas fa-box-open mr-2"></i> <span data-translate="true" data-i18n="btn_add_item">Add Item Master</span></button>
            <?php endif; ?>
        </div>
    </div>

    <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
        <div id="inv-card-container" class="md:hidden flex flex-col gap-4"></div>
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold" id="inv-th-row">
                    <tr>
                        <th class="px-6 py-4" data-translate="true" data-i18n="th_it_code">Item Code</th>
                        <th class="px-6 py-4" data-translate="true" data-i18n="th_it_name">Item Name</th>
                        <th class="px-6 py-4" data-translate="true" data-i18n="th_it_cat">Category</th>
                        <th class="px-6 py-4 text-center" data-translate="true" data-i18n="th_it_stock">Stock / UoM</th>
                        <th class="px-6 py-4 text-right" data-translate="true" data-i18n="th_price">Harga</th>
                        
                        <?php if($isAdmin || in_array('item_edit', $rights) || in_array('stock_edit', $rights) || in_array('item_delete', $rights)): ?>
                        <th class="px-6 py-4 text-right" id="th-inv-act" data-translate="true" data-i18n="th_act">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="inv-table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-item" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
        <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 tracking-tight" data-translate="true" data-i18n="master_item">Master Item</h3>
            <button onclick="closeModal('modal-item')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <form onsubmit="event.preventDefault(); saveItem();" class="p-6">
            <input type="hidden" id="is-edit-mode" value="0">
            <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="it_code">Item No (Code)</label><input type="text" id="it-code" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-50 bg-slate-50 transition" required></div>
            <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="it_name">Item Name</label><input type="text" id="it-name" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required></div>
            <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="it_spec">Item Specification</label><input type="text" id="it-spec" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="-"></div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="cat">Category</label><input type="text" id="it-cat" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" value="General"></div>
                <div><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">UoM</label><input type="text" id="it-uom" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required placeholder="Pcs/Set"></div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="curr_stk">Current Stock</label><input type="number" id="it-stock" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required></div>
                <div id="wrap-it-price"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="th_price">Harga</label><input type="number" id="it-price" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="0"></div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-emerald-600 text-white rounded-xl font-bold shadow-md hover:bg-emerald-700 btn-animated" data-translate="true" data-i18n="btn_save_item">Save Item</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    window.onload = () => {
        applyLanguage();
        loadData();
    };

    function loadData() {
        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getInventory'})})
        .then(r => r.json()).then(d => { 
            if(d.code === 401) { logoutAction(); return; }
            inventoryData = Array.isArray(d) ? d : (d.data || []); 
            renderInventory(inventoryData); 
        }).catch(e => { console.error(e); renderInventory([]); });
    }

    function filterInventory() {
        const term = document.getElementById('search-inv').value.toLowerCase();
        const filtered = inventoryData.filter(r => (r.item_code && r.item_code.toLowerCase().includes(term)) || (r.item_name && r.item_name.toLowerCase().includes(term)) || (r.item_spec && r.item_spec.toLowerCase().includes(term)) || (r.category && r.category.toLowerCase().includes(term)));
        renderInventory(filtered);
    }

    function renderInventory(data = inventoryData) {
        const tb = document.getElementById('inv-table-body'); 
        const cardContainer = document.getElementById('inv-card-container');
        
        // Render Insight Cards
        let totalVal = 0; let lowStock = 0;
        data.forEach(i => {
            totalVal += (parseFloat(i.price || 0) * parseInt(i.stock || 0));
            if(parseInt(i.stock || 0) < 10) lowStock++;
        });

        document.getElementById('inv-insights').classList.remove('hidden');
        document.getElementById('inv-stat-items').innerText = data.length + " Items";
        document.getElementById('inv-stat-low').innerText = lowStock + " Items";
        document.getElementById('inv-stat-value').innerText = 'Rp ' + totalVal.toLocaleString('id-ID');

        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        const isAppAdmin = currentUser.role === 'Administrator';
        const canEditInfo = isAppAdmin || accRights.includes('item_edit');
        const canEditStock = isAppAdmin || accRights.includes('stock_edit');
        const canDelete = isAppAdmin || accRights.includes('item_delete');
        const canEditAny = canEditInfo || canEditStock;

        if (!data || data.length === 0) {
            tb.innerHTML = `<tr><td colspan="6" class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</td></tr>`;
            cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic" data-translate="true" data-i18n="no_data">No data found.</div>`;
            AutoTranslator.processDOM(); return;
        }

        const displayData = data.slice(0, 300);
        let htmlArrayTable = []; let htmlArrayCard = [];

        displayData.forEach(r => {
            let priceHtmlTable = `<td class="px-6 py-4 text-right font-bold text-emerald-700">Rp ${parseFloat(r.price||0).toLocaleString('id-ID')}</td>`;
            let priceHtmlCard = `<div class="text-right mb-4 bg-emerald-50 p-2.5 rounded-xl border border-emerald-100"><span class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider block mb-0.5">Harga</span><span class="font-black text-emerald-800 text-sm">Rp ${parseFloat(r.price||0).toLocaleString('id-ID')}</span></div>`;

            let actionButtons = "";
            if (canEditAny) {
                actionButtons += `<button onclick="openEditItem('${(r.item_code||'').replace(/'/g, "\\'")}','${(r.item_name||'').replace(/'/g, "\\'")}','${(r.item_spec||'').replace(/'/g, "\\'")}','${(r.category||'').replace(/'/g, "\\'")}','${(r.uom||'').replace(/'/g, "\\'")}','${r.stock}','${r.price}')" class="text-blue-600 hover:text-blue-800 bg-blue-50 p-2 rounded-lg shadow-sm transition btn-animated"><i class="fas fa-edit"></i></button>`;
            }
            if (canDelete) {
                actionButtons += `<button onclick="deleteMasterItem('${(r.item_code||'').replace(/'/g, "\\'")}')" class="text-red-500 hover:text-white hover:bg-red-600 bg-red-50 p-2 ml-2 rounded-lg shadow-sm transition btn-animated"><i class="fas fa-trash"></i></button>`;
            }

            let actTable = (canEditAny || canDelete) ? `<td class="px-6 py-4 text-right whitespace-nowrap">${actionButtons}</td>` : `<td class="px-6 py-4 text-right hidden" id="th-inv-act"></td>`;
            
            htmlArrayTable.push(`<tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors duration-200"><td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">${r.item_code}</td><td class="px-6 py-4"><div class="font-bold text-slate-700">${r.item_name}</div><div class="text-[10px] text-slate-500 italic mt-0.5">${r.item_spec || '-'}</div></td><td class="px-6 py-4 text-xs text-slate-500"><span class="bg-slate-100 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase border border-slate-200">${r.category}</span></td><td class="px-6 py-4 text-center"><span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm">${r.stock} <span class="font-normal text-[10px] ml-1">${r.uom}</span></span></td>${priceHtmlTable}${actTable}</tr>`);

            let actCard = (canEditAny || canDelete) ? `${actionButtons}` : ``;
            htmlArrayCard.push(`<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-1 transition-all hover:shadow-md"><div class="flex justify-between items-start mb-3"><div class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">${r.item_code}</div><span class="bg-slate-100 px-2 py-1 rounded-md text-[9px] font-bold uppercase border border-slate-200 text-slate-600">${r.category}</span></div><div class="mb-4"><div class="font-bold text-sm text-slate-800">${r.item_name}</div><div class="text-xs text-slate-500 italic mt-1 leading-snug">${r.item_spec || '-'}</div></div>${priceHtmlCard}<div class="flex justify-between items-center border-t border-slate-100 pt-4"><div><span class="text-[9px] text-slate-400 uppercase font-bold block mb-1" data-translate="true">Current Stock</span><span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm text-sm">${r.stock} <span class="font-normal text-[10px] ml-1">${r.uom}</span></span></div><div>${actCard}</div></div></div>`);
        });

        if(data.length > 300) { htmlArrayTable.push(`<tr><td colspan="6" class="text-center py-4 text-slate-400 text-xs italic" data-translate="true">Menampilkan 300 data pertama.</td></tr>`); htmlArrayCard.push(`<div class="text-center py-4 text-slate-400 text-xs italic" data-translate="true">Menampilkan 300 data pertama.</div>`); }

        tb.innerHTML = htmlArrayTable.join(''); cardContainer.innerHTML = htmlArrayCard.join(''); AutoTranslator.processDOM();
    }

    // Helper Hak Akses
    function canAddPriceHelper() {
        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        return currentUser.role === 'Administrator' || accRights.includes('price_add');
    }
    function canEditPriceHelper() {
        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        return currentUser.role === 'Administrator' || accRights.includes('price_edit');
    }

    function openItemModal() {
        document.getElementById('it-code').value = ''; document.getElementById('it-code').disabled = false;
        document.getElementById('it-name').value = ''; document.getElementById('it-name').disabled = false;
        document.getElementById('it-spec').value = ''; document.getElementById('it-spec').disabled = false;
        document.getElementById('it-cat').value = 'General'; document.getElementById('it-cat').disabled = false;
        document.getElementById('it-uom').value = ''; document.getElementById('it-uom').disabled = false;
        document.getElementById('it-stock').value = ''; document.getElementById('it-stock').disabled = false;
        document.getElementById('it-price').value = ''; 
        
        // Cek Hak Akses Tambah Harga Baru (price_add)
        if(!canAddPriceHelper()) { 
            document.getElementById('it-price').readOnly = true;
            document.getElementById('it-price').classList.add('bg-slate-100', 'cursor-not-allowed');
        } else { 
            document.getElementById('it-price').readOnly = false;
            document.getElementById('it-price').classList.remove('bg-slate-100', 'cursor-not-allowed');
        }

        document.getElementById('is-edit-mode').value = '0';
        openModal('modal-item');
    }

    function openEditItem(c, n, spec, cat, u, s, p) {
        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        const isAppAdmin = currentUser.role === 'Administrator';
        const canEditInfo = isAppAdmin || accRights.includes('item_edit');
        const canEditStock = isAppAdmin || accRights.includes('stock_edit');
        
        document.getElementById('it-code').value = c; document.getElementById('it-code').disabled = true;
        document.getElementById('it-name').value = n; document.getElementById('it-name').disabled = !canEditInfo;
        document.getElementById('it-spec').value = spec; document.getElementById('it-spec').disabled = !canEditInfo;
        document.getElementById('it-cat').value = cat; document.getElementById('it-cat').disabled = !canEditInfo;
        document.getElementById('it-uom').value = u; document.getElementById('it-uom').disabled = !canEditInfo;
        document.getElementById('it-stock').value = s; document.getElementById('it-stock').disabled = !canEditStock;
        
        document.getElementById('it-price').value = p || 0;
        
        // Cek Hak Akses Edit Harga Exist (price_edit)
        if(!canEditPriceHelper()) { 
            document.getElementById('it-price').readOnly = true;
            document.getElementById('it-price').classList.add('bg-slate-100', 'cursor-not-allowed');
        } else { 
            document.getElementById('it-price').readOnly = false;
            document.getElementById('it-price').classList.remove('bg-slate-100', 'cursor-not-allowed');
        }

        document.getElementById('is-edit-mode').value = '1';
        openModal('modal-item');
    }

    function saveItem() {
        const p = { 
            action: 'saveItem', role: currentUser.role, department: currentUser.department, username: currentUser.username, 
            is_edit: document.getElementById('is-edit-mode').value, item_code: document.getElementById('it-code').value, 
            item_name: document.getElementById('it-name').value, item_spec: document.getElementById('it-spec').value, 
            category: document.getElementById('it-cat').value, uom: document.getElementById('it-uom').value, 
            stock: document.getElementById('it-stock').value, price: document.getElementById('it-price').value 
        };
        fetch('api/gis.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
            if(res.code === 401) { logoutAction(); return; }
            if(res.success){ closeModal('modal-item'); loadData(); showCustomAlert("Success", "Item Saved."); } else showCustomAlert("Error", res.message); 
        });
    }

    function deleteMasterItem(code) {
        showCustomConfirm("Hapus Item", "Anda yakin ingin menghapus item " + code + "? Semua data stok terkait item ini akan hilang permanen.", () => {
            fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'deleteItem', item_code: code }) })
            .then(r => r.json()).then(res => {
                if(res.code === 401) { logoutAction(); return; }
                if(res.success) { showCustomAlert("Success", res.message); loadData(); }
                else { showCustomAlert("Error", res.message); }
            }).catch(e => showCustomAlert("Error", t('err_conn')));
        });
    }

    function downloadItemTemplate() {
        const wb = XLSX.utils.book_new(); 
        const ws = XLSX.utils.aoa_to_sheet([["Item_Code", "Item_Name", "Item_Specification", "Category", "UoM", "Stock", "Price"], ["ITM-001", "Bearing SKF 6205", "High Speed Bearing", "Sparepart", "Pcs", 50, 150000]]);
        XLSX.utils.book_append_sheet(wb, ws, "Template_Items"); XLSX.writeFile(wb, "Template_Master_Items.xlsx");
    }

    function exportItems() {
        if(inventoryData.length === 0) return showCustomAlert("Info", "No inventory data to export.");
        
        const wb = XLSX.utils.book_new(); 
        let header = ["Item_Code", "Item_Name", "Item_Specification", "Category", "UoM", "Stock", "Price", "Last_Updated"];
        const rows = [header];
        
        inventoryData.forEach(i => { 
            let r = [i.item_code, i.item_name, i.item_spec, i.category, i.uom, parseInt(i.stock), parseFloat(i.price||0), i.last_updated];
            rows.push(r); 
        });
        const ws = XLSX.utils.aoa_to_sheet(rows); XLSX.utils.book_append_sheet(wb, ws, "Master_Items"); XLSX.writeFile(wb, "GIS_Master_Items_" + new Date().getTime() + ".xlsx");
    }

    function handleImportItems(e) {
        const file = e.target.files[0]; if(!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            try {
                const data = new Uint8Array(evt.target.result); const workbook = XLSX.read(data, {type: 'array'});
                const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                const formatted = json.map(r => ({ 
                    item_code: String(r.Item_Code || r.item_code || ''), item_name: String(r.Item_Name || r.item_name || ''), 
                    item_spec: String(r.Item_Specification || r.item_specification || r.item_spec || ''), 
                    category: String(r.Category || r.category || 'General'), uom: String(r.UoM || r.uom || 'Pcs'), 
                    stock: parseInt(r.Stock || r.stock || 0), price: parseFloat(r.Price || r.price || r.Harga || 0) 
                })).filter(r => r.item_code && r.item_name);
                
                if(formatted.length === 0) { document.getElementById('import-item-file').value = ''; return showCustomAlert("Error", "Format tidak valid atau data kosong."); }
                fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'importItems', role: currentUser.role, data: formatted }) })
                .then(r=>r.json()).then(res => { document.getElementById('import-item-file').value = ''; if(res.code === 401) { logoutAction(); return; } if(res.success) { showCustomAlert("Success", res.message); loadData(); } else { showCustomAlert("Error", res.message); } });
            } catch(err) { document.getElementById('import-item-file').value = ''; showCustomAlert("Error", "Gagal parsing file Excel."); }
        }; reader.readAsArrayBuffer(file);
    }
</script>