</main>

  <div id="modal-export" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl animate-slide-up overflow-hidden">
          <div class="bg-slate-800 px-6 py-5 border-b border-slate-700 flex justify-between items-center text-white">
              <h3 class="font-bold text-lg tracking-tight"><i class="fas fa-print text-indigo-400 mr-2"></i> <span data-translate="true" data-i18n="export_data_title">Export Report</span></h3>
              <button onclick="closeModal('modal-export')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
          </div>
          <div class="p-8">
              <div class="mb-5">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-translate="true" data-i18n="export_type">Data Type</label>
                  <select id="exp-type" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium" onchange="toggleExpDates()">
                      <option value="GI" data-translate="true">Good Issue History</option>
                      <option value="GR" data-translate="true">Good Receive History</option>
                      <option value="INV" data-translate="true">Master Inventory (Current)</option>
                  </select>
              </div>
              <div id="exp-date-group" class="grid grid-cols-2 gap-4 mb-8">
                  <div>
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-translate="true" data-i18n="start_date">Start Date</label>
                      <input type="date" id="exp-start" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
                  </div>
                  <div>
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-translate="true" data-i18n="end_date">End Date</label>
                      <input type="date" id="exp-end" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
                  </div>
              </div>
              <div class="flex gap-3">
                  <button onclick="processExport('excel')" class="flex-1 py-3.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-md transition btn-animated"><i class="fas fa-file-excel mr-1.5"></i> Excel</button>
                  <button onclick="processExport('pdf')" class="flex-1 py-3.5 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 shadow-md transition btn-animated"><i class="fas fa-file-pdf mr-1.5"></i> PDF</button>
              </div>
              <div id="exp-loading" class="hidden text-center mt-5 text-xs font-bold text-indigo-600 animate-pulse"><i class="fas fa-spinner fa-spin mr-2"></i> <span data-translate="true">Processing Data...</span></div>
          </div>
      </div>
  </div>

  <div id="modal-alert-custom" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="p-8 text-center">
              <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-5 text-indigo-600 shadow-inner"><i class="fas fa-info text-2xl"></i></div>
              <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight" id="alert-custom-title" data-translate="true">Information</h3>
              <p class="text-sm text-slate-500 mb-8 leading-relaxed font-medium" id="alert-custom-msg" data-translate="true">Message</p>
              <button onclick="closeModal('modal-alert-custom')" class="w-full py-3.5 bg-slate-800 text-white rounded-xl font-bold text-sm hover:bg-slate-900 shadow-md transition btn-animated" data-translate="true" data-i18n="btn_ok">OK</button>
          </div>
      </div>
  </div>

  <div id="modal-confirm-custom" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="p-8 text-center">
              <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5 text-blue-600 shadow-inner"><i class="fas fa-question text-2xl"></i></div>
              <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight" id="confirm-custom-title" data-translate="true">Confirm</h3>
              <p class="text-sm text-slate-500 mb-8 leading-relaxed font-medium" id="confirm-custom-msg" data-translate="true">Are you sure?</p>
              <div class="flex gap-3">
                  <button onclick="closeModal('modal-confirm-custom')" class="flex-1 py-3.5 border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition btn-animated" data-translate="true" data-i18n="btn_cancel">Cancel</button>
                  <button onclick="executeCustomConfirm()" class="flex-1 py-3.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition btn-animated" data-translate="true" data-i18n="btn_yes">Yes, Proceed</button>
              </div>
          </div>
      </div>
  </div>

  <div id="modal-profile" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] animate-slide-up overflow-hidden">
          <div class="bg-indigo-600 px-6 py-5 flex justify-between items-center flex-none">
              <h3 class="font-bold text-white tracking-wide"><i class="fas fa-user-edit mr-2"></i> <span data-translate="true" data-i18n="my_profile">My Profile</span></h3>
              <button onclick="closeModal('modal-profile')" class="text-indigo-200 hover:text-white"><i class="fas fa-times text-lg"></i></button>
          </div>
          <div class="p-6 overflow-y-auto flex-1 custom-scroll">
              <div class="grid grid-cols-2 gap-5 mb-5">
                  <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK</label><input type="text" id="prof-nik" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled></div>
                  <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="dept">Department</label><input type="text" id="prof-dept" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled></div>
                  <div class="col-span-2"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true" data-i18n="fullname">Fullname</label><input type="text" id="prof-name" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled></div>
              </div>
              <div class="border-t border-slate-200 pt-5 mt-2">
                  <div class="mb-5"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5"><span data-translate="true" data-i18n="wa_phone">No WhatsApp</span> <span class="text-blue-500 lowercase font-medium italic" data-translate="true" data-i18n="editable">(Dapat Diubah)</span></label><input type="text" id="prof-phone" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Contoh: 0812345678"></div>
                  <div class="mb-2"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5"><span data-translate="true" data-i18n="new_pass">Password Baru</span> <span class="text-slate-400 lowercase font-medium italic" data-translate="true" data-i18n="pass_note">(Kosongkan jika tidak diubah)</span></label><input type="password" id="prof-pass" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="******"></div>
              </div>
          </div>
          <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
              <button onclick="closeModal('modal-profile')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-translate="true" data-i18n="btn_cancel">Cancel</button>
              <button onclick="saveProfile()" id="btn-save-profile" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700" data-translate="true" data-i18n="btn_update_prof">Update Profile</button>
          </div>
      </div>
  </div>

  <div id="modal-image-viewer" class="hidden fixed inset-0 bg-slate-900/95 backdrop-blur-md z-[110] flex items-center justify-center p-4 cursor-pointer" onclick="closeModal('modal-image-viewer')">
      <div class="relative w-full max-w-4xl flex justify-center items-center animate-slide-up" onclick="event.stopPropagation()">
          <button onclick="closeModal('modal-image-viewer')" class="absolute -top-12 right-0 text-white/70 hover:text-white text-4xl transition hover:scale-110">&times;</button>
          <img id="img-viewer-src" src="" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl object-contain border-2 border-white/10 bg-slate-800">
      </div>
  </div>

  <div id="modal-users" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl flex flex-col h-[85vh] animate-slide-up overflow-hidden">
        <div class="bg-slate-800 px-6 py-5 flex justify-between items-center flex-none">
            <h3 class="font-bold text-white tracking-wide"><i class="fas fa-users-cog text-indigo-400 mr-2"></i> <span data-translate="true">Manage Users & Permissions</span></h3>
            <button onclick="closeModal('modal-users')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="flex flex-1 overflow-hidden">
            <div class="w-1/3 border-r border-slate-200 flex flex-col bg-slate-50">
                <div class="p-5 border-b border-slate-200">
                    <button onclick="resetUserForm()" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-bold text-xs mb-4 shadow-md hover:bg-indigo-700 btn-animated"><i class="fas fa-plus mr-1"></i> <span data-translate="true">New User</span></button>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                        <input type="text" id="search-user" onkeyup="filterUsers()" class="w-full border border-slate-300 rounded-xl p-2.5 pl-9 text-xs outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition" data-translate-ph="true" placeholder="Search user...">
                    </div>
                </div>
                <div id="user-list" class="flex-1 overflow-y-auto custom-scroll p-3 space-y-2"></div>
            </div>
            <div class="w-2/3 p-8 overflow-y-auto custom-scroll bg-white">
                <h4 class="font-black text-lg mb-6 text-slate-800 tracking-tight border-b pb-3" id="form-title" data-translate="true">Create User</h4>
                <form id="user-form" onsubmit="event.preventDefault(); saveUser();">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true">Username</label><input type="text" id="u-user" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true">Password</label><input type="password" id="u-pass" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required placeholder="******"></div>
                        <div class="col-span-2"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true">Fullname</label><input type="text" id="u-name" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK</label><input type="text" id="u-nik" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Employee ID"></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-translate="true">Department</label><input type="text" id="u-dept" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                            <select id="u-role" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition" onchange="handleRoleChange(this)">
                                <option value="User">User</option><option value="SectionHead">Section Head</option>
                                <option value="TeamLeader">Team Leader</option><option value="Warehouse">Warehouse</option>
                                <option value="PlantHead">Plant Head</option><option value="Administrator">Administrator</option>
                            </select>
                        </div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone (WA)</label><input type="text" id="u-phone" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition"></div>
                        
                        <div class="col-span-2 border-t border-slate-200 pt-5 mt-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3"><i class="fas fa-shield-alt text-indigo-500 mr-1"></i> <span data-translate="true">Access Rights (Permissions)</span></label>
                            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-gi-submit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="gi_submit"> <span data-translate="true">Submit Good Issue</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-gr-submit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="gr_submit"> <span data-translate="true">Submit Good Receive</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-item-add" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="item_add"> <span data-translate="true">Add Item Master</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-item-edit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="item_edit"> <span data-translate="true">Edit Item Info</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-stock-edit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="stock_edit"> <span data-translate="true">Edit/Adjust Stock</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-export-data" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="export_data"> <span data-translate="true">Export Data (PDF/Excel)</span></label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-edit-price" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="edit_price"> <span data-translate="true" data-i18n="edit_price">Edit Price</span></label>
                            </div>
                        </div>

                        <div class="col-span-2 mt-2 flex justify-end gap-3 pt-5 border-t border-slate-100">
                            <button type="button" id="btn-del-user" onclick="deleteUser()" class="hidden bg-red-100 text-red-600 px-5 py-2.5 rounded-xl text-sm font-bold mr-auto hover:bg-red-200 btn-animated"><i class="fas fa-trash"></i></button>
                            <button type="submit" class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl shadow-md font-bold text-sm btn-animated hover:bg-indigo-700" data-translate="true">Save User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>

  <script>
    const currentUser = <?= json_encode($currentUser) ?>;
    let inventoryData = [];
    let globalConfirmCallback = null;
    let allUsers = []; // Variabel global untuk user management
    let currentLang = localStorage.getItem('portal_lang') || 'en';

    const i18n = {
        en: {
            app_desc: "Good Issue & Inventory System", forgot_pass: "Forgot Password?", login: "Login", tab_gi: "Good Issue (GI)", tab_gr: "Good Receive (GR)", tab_inv: "Inventory", stat_tot_gi: "Total Request", stat_pend: "Pending Approval", stat_comp: "Completed", stat_pend_head: "Pending Head", stat_pend_wh: "Pending WH", stat_pend_recv: "Pending Receive", hist_gi: "Good Issue History", click_filter_info: "Click statistics cards above to filter data.", ph_search_gi: "Search GI (ID/Name/Dept/Desc)...", btn_new_gi: "New GI Form", th_id: "NO GIF & Date", th_req: "Requestor Info", th_items: "Items & Activities Description", th_stat: "Status", th_act: "Action", hist_gr: "Good Receive History", desc_gr: "Log of incoming items to warehouse.", ph_search_gr: "Search GR (ID/Name/Supplier)...", btn_new_gr: "New GR Form", th_gr_id: "GR ID & Date", th_gr_by: "Received By", th_gr_rem: "Remarks / Supplier", th_gr_items: "Items Received", mast_inv: "Master Inventory", desc_inv: "Manage warehouse items and stock.", btn_add_item: "Add Item Master", th_it_code: "Item Code", th_it_name: "Item Name", th_it_spec: "Specification", th_it_cat: "Category", th_it_stock: "Stock / UoM", btn_ok: "OK", btn_cancel: "Cancel", btn_yes: "Yes, Proceed", reset_pass: "Reset Password", reset_info: "Reset link will be sent to your WhatsApp number.", btn_send_wa: "Send WhatsApp Link", my_profile: "My Profile", dept: "Department", fullname: "Fullname", wa_phone: "WhatsApp No.", editable: "(Editable)", new_pass: "New Password", pass_note: "(Leave blank if unchanged)", btn_update_prof: "Update Profile", form_gi: "Form Good Issue", req_dept: "Requestor / Dept", sec_req: "Section Requestor", ph_sec_req: "Ex: Maintenance / Production", act_desc: "Activities Description", ph_act_desc: "Explain activities or reason...", item_list: "Item List", add_row: "Add Item Row", curr_stk_short: "Curr. Stock", req_qty: "Requested Qty", rsn_code: "Reason Code", cost_ctr: "Cost Center *", btn_submit_form: "Submit Form", form_gr: "Form Good Receive (GR)", info_gr: "Input incoming items. Data will automatically update Master Inventory stock.", rem_supp: "Remarks / Supplier / PO Number", inc_items: "Incoming Items", qty_recv: "Qty Received", btn_save_stk: "Save & Update Stock", master_item: "Master Item", it_code: "Item No (Code)", it_name: "Item Name", it_spec: "Item Specification", cat: "Category", curr_stk: "Current Stock", btn_save_item: "Save Item", rej_req: "Reject Request", ph_rej: "Reason for rejection...", btn_conf_rej: "Confirm Reject", no_data: "No data found.", btn_appr: "Approve", btn_rej: "Reject", btn_iss: "Issue Items", err_conn: "Connection Error.", err_req: "Please fill all required fields.", ph_search_item: "Search Item...", ph_search_inv: "Search Item (Code/Name)...", btn_export_data: "Export Report", export_data_title: "Export Report", export_type: "Data Type", start_date: "Start Date", end_date: "End Date", btn_export_items: "Export", btn_import_items: "Import", btn_template: "Template", btn_confirm_recv: "Confirm Receive",
            th_price: "Price", total_price: "Total Price", edit_price: "Edit Price", grand_total: "Grand Total"
        },
        id: {
            app_desc: "Sistem Pengeluaran & Inventaris", forgot_pass: "Lupa Password?", login: "Masuk", tab_gi: "Pengeluaran (GI)", tab_gr: "Penerimaan (GR)", tab_inv: "Inventaris", stat_tot_gi: "Total Permintaan", stat_pend: "Menunggu Persetujuan", stat_comp: "Selesai", stat_pend_head: "Menunggu Head", stat_pend_wh: "Menunggu Gudang", stat_pend_recv: "Menunggu Diterima", hist_gi: "Riwayat Pengeluaran (GI)", click_filter_info: "Klik kartu statistik di atas untuk memfilter data.", ph_search_gi: "Cari GI (ID/Nama/Dept/Desk)...", btn_new_gi: "Form GI Baru", th_id: "NO GIF & Tanggal", th_req: "Info Pemohon", th_items: "Barang & Deskripsi Aktivitas", th_stat: "Status", th_act: "Aksi", hist_gr: "Riwayat Penerimaan (GR)", desc_gr: "Catatan barang masuk ke gudang.", ph_search_gr: "Cari GR (ID/Nama/Suplier)...", btn_new_gr: "Form GR Baru", th_gr_id: "ID GR & Tanggal", th_gr_by: "Diterima Oleh", th_gr_rem: "Catatan / Suplier", th_gr_items: "Barang Diterima", mast_inv: "Master Inventaris", desc_inv: "Kelola stok dan barang gudang.", btn_add_item: "Tambah Master Barang", th_it_code: "Kode Barang", th_it_name: "Nama Barang", th_it_spec: "Spesifikasi", th_it_cat: "Kategori", th_it_stock: "Stok / Satuan", btn_ok: "OK", btn_cancel: "Batal", btn_yes: "Ya, Lanjutkan", reset_pass: "Reset Kata Sandi", reset_info: "Tautan reset akan dikirim ke nomor WhatsApp Anda.", btn_send_wa: "Kirim Tautan WA", my_profile: "Profil Saya", dept: "Departemen", fullname: "Nama Lengkap", wa_phone: "No. WhatsApp", editable: "(Dapat Diubah)", new_pass: "Kata Sandi Baru", pass_note: "(Kosongkan jika tidak diubah)", btn_update_prof: "Perbarui Profil", form_gi: "Formulir Pengeluaran (GI)", req_dept: "Pemohon / Dept", sec_req: "Seksi Pemohon", ph_sec_req: "Cth: Maintenance / Produksi", act_desc: "Deskripsi Aktivitas", ph_act_desc: "Jelaskan aktivitas atau alasan...", item_list: "Daftar Barang", add_row: "Tambah Baris", curr_stk_short: "Sisa Stok", req_qty: "Jumlah Diminta", rsn_code: "Kode Alasan", cost_ctr: "Pusat Biaya *", btn_submit_form: "Kirim Formulir", form_gr: "Formulir Penerimaan (GR)", info_gr: "Input barang masuk. Data akan otomatis menambah stok Master Inventaris.", rem_supp: "Catatan / Suplier / No. PO", inc_items: "Barang Masuk", qty_recv: "Jml Diterima", btn_save_stk: "Simpan & Perbarui Stok", master_item: "Master Barang", it_code: "No/Kode Barang", it_name: "Nama Barang", it_spec: "Spesifikasi Barang", cat: "Kategori", curr_stk: "Stok Saat Ini", btn_save_item: "Simpan Barang", rej_req: "Tolak Permintaan", ph_rej: "Alasan penolakan...", btn_conf_rej: "Konfirmasi Tolak", no_data: "Tidak ada data.", btn_appr: "Setujui", btn_rej: "Tolak", btn_iss: "Keluarkan Barang", err_conn: "Koneksi Gagal.", err_req: "Harap isi semua kolom wajib.", ph_search_item: "Cari Barang...", ph_search_inv: "Cari Barang (Kode/Nama)...", btn_export_data: "Ekspor Laporan", export_data_title: "Ekspor Laporan", export_type: "Tipe Data", start_date: "Tanggal Mulai", end_date: "Tanggal Akhir", btn_export_items: "Ekspor", btn_import_items: "Impor", btn_template: "Template", btn_confirm_recv: "Konfirmasi Terima",
            th_price: "Harga", total_price: "Total Harga", edit_price: "Edit Harga", grand_total: "Total Keseluruhan"
        }
    };
    const t = (key) => i18n[currentLang][key] || key;

    const AutoTranslator = {
        cache: JSON.parse(localStorage.getItem('portal_auto_trans_cache')) || {},
        async translate(text, targetLang) {
            if (!text || !text.trim()) return text;
            const key = text.trim() + "_" + targetLang;
            if (this.cache[key]) return this.cache[key];
            try {
                const res = await fetch(`https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(text.trim())}`);
                const data = await res.json();
                const translated = data[0].map(x => x[0]).join('');
                this.cache[key] = translated;
                localStorage.setItem('portal_auto_trans_cache', JSON.stringify(this.cache));
                return translated;
            } catch(e) { return text; }
        },
        async processDOM() {
            const elements = document.querySelectorAll('[data-translate="true"]');
            for(let el of elements) {
                let originalText = el.getAttribute('data-orig-text');
                if(!originalText) {
                    originalText = el.innerText.trim();
                    el.setAttribute('data-orig-text', originalText);
                }
                if(!originalText) continue;
                const i18nKey = el.getAttribute('data-i18n');
                if(i18nKey && i18n[currentLang] && i18n[currentLang][i18nKey]) {
                    el.innerHTML = i18n[currentLang][i18nKey]; continue;
                }
                const translated = await this.translate(originalText, currentLang);
                if(el.innerText !== translated) el.innerText = translated;
            }

            const inputs = document.querySelectorAll('[data-translate-ph="true"]');
            for(let el of inputs) {
                let originalPh = el.getAttribute('data-orig-ph');
                if(!originalPh) {
                    originalPh = el.placeholder.trim();
                    el.setAttribute('data-orig-ph', originalPh);
                }
                if(!originalPh) continue;
                const i18nKey = el.getAttribute('data-i18n-ph');
                if(i18nKey && i18n[currentLang] && i18n[currentLang][i18nKey]) {
                    el.placeholder = i18n[currentLang][i18nKey]; continue;
                }
                const translated = await this.translate(originalPh, currentLang);
                if(el.placeholder !== translated) el.placeholder = translated;
            }
        }
    };

    async function applyLanguage() {
        document.getElementById('lang-label').innerText = currentLang.toUpperCase();
        document.querySelectorAll('[data-i18n]:not([data-translate="true"])').forEach(el => {
            const k = el.getAttribute('data-i18n');
            if(i18n[currentLang] && i18n[currentLang][k]) el.innerHTML = i18n[currentLang][k];
        });
        document.querySelectorAll('[data-i18n-ph]:not([data-translate-ph="true"])').forEach(el => {
            const k = el.getAttribute('data-i18n-ph');
            if(i18n[currentLang] && i18n[currentLang][k]) el.placeholder = i18n[currentLang][k];
        });
        await AutoTranslator.processDOM();
    }

    function toggleLanguage() {
        currentLang = (currentLang === 'en') ? 'id' : 'en';
        localStorage.setItem('portal_lang', currentLang);
        window.location.reload(); 
    }

    function logoutAction() {
        fetch('api/auth.php', { method:'POST', body:JSON.stringify({action:'logout'}) })
        .then(() => { window.location.href = 'login.php'; })
        .catch(() => { window.location.href = 'login.php'; });
    }

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        if(id === 'modal-action-photo' && typeof stopCamera === 'function') stopCamera();
        if(id === 'modal-gr' && typeof stopGrCamera === 'function') stopGrCamera();
        if(id === 'modal-image-viewer') { setTimeout(()=> {document.getElementById('img-viewer-src').src = '';}, 300); }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            const alertModal = document.getElementById('modal-alert-custom');
            const confirmModal = document.getElementById('modal-confirm-custom');
            const imgViewer = document.getElementById('modal-image-viewer');
            
            if (alertModal && !alertModal.classList.contains('hidden')) { closeModal('modal-alert-custom'); return; }
            if (confirmModal && !confirmModal.classList.contains('hidden')) { closeModal('modal-confirm-custom'); return; }
            if (imgViewer && !imgViewer.classList.contains('hidden')) { closeModal('modal-image-viewer'); return; }

            const mainModals = ['modal-export', 'modal-profile', 'modal-gi', 'modal-reject', 'modal-action-photo', 'modal-gr', 'modal-item', 'modal-users'];
            mainModals.forEach(id => {
                const m = document.getElementById(id);
                if (m && !m.classList.contains('hidden')) { closeModal(id); }
            });
        }
    });

    function showCustomAlert(title, message) {
        const titleEl = document.getElementById('alert-custom-title');
        const msgEl = document.getElementById('alert-custom-msg');
        titleEl.innerText = title; msgEl.innerText = message;
        titleEl.setAttribute('data-orig-text', title); msgEl.setAttribute('data-orig-text', message);
        openModal('modal-alert-custom'); AutoTranslator.processDOM();
    }

    function showCustomConfirm(title, message, callback) {
        const titleEl = document.getElementById('confirm-custom-title');
        const msgEl = document.getElementById('confirm-custom-msg');
        titleEl.innerText = title; titleEl.setAttribute('data-orig-text', title);
        msgEl.innerText = message; msgEl.setAttribute('data-orig-text', message);
        globalConfirmCallback = callback; openModal('modal-confirm-custom'); AutoTranslator.processDOM();
    }

    function executeCustomConfirm() {
        if(globalConfirmCallback) globalConfirmCallback();
        closeModal('modal-confirm-custom'); globalConfirmCallback = null;
    }

    function openProfileModal() {
        document.getElementById('prof-nik').value = currentUser.nik || '-';
        document.getElementById('prof-name').value = currentUser.fullname;
        document.getElementById('prof-dept').value = currentUser.department;
        document.getElementById('prof-phone').value = currentUser.phone || '';
        document.getElementById('prof-pass').value = '';
        openModal('modal-profile');
    }

    function saveProfile() {
        const phone = document.getElementById('prof-phone').value;
        const pass = document.getElementById('prof-pass').value;
        const btn = document.getElementById('btn-save-profile');
        const orgHtml = btn.innerHTML;
        
        btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> <span data-translate="true">Memproses...</span>`;
        AutoTranslator.processDOM();

        fetch('api/users.php', { method: 'POST', body: JSON.stringify({ action: 'updateProfile', username: currentUser.username, phone: phone, newPass: pass }) })
        .then(r => r.json()).then(res => {
            btn.disabled = false; btn.innerHTML = orgHtml;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) { showCustomAlert("Success", "Profil berhasil diperbarui."); closeModal('modal-profile'); } 
            else { showCustomAlert("Error", res.message); }
        }).catch(e => { btn.disabled = false; btn.innerHTML = orgHtml; showCustomAlert("Error", t('err_conn')); });
    }

    function viewPhoto(url) {
        if (!url || url === 'null' || url === 'undefined' || url.trim() === '' || url === '0') {
            showCustomAlert(t('info'), "Tidak ada bukti foto."); return;
        }
        const viewer = document.getElementById('img-viewer-src');
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        viewer.src = baseUrl + url.trim() + '?t=' + new Date().getTime();
        openModal('modal-image-viewer');
    }

    function openExportModal() {
        document.getElementById('exp-type').value = 'GI';
        document.getElementById('exp-start').value = '';
        document.getElementById('exp-end').value = '';
        toggleExpDates(); openModal('modal-export');
    }
    function toggleExpDates() {
        const type = document.getElementById('exp-type').value;
        const dateGroup = document.getElementById('exp-date-group');
        if(type === 'INV') { dateGroup.classList.add('hidden'); } else { dateGroup.classList.remove('hidden'); }
    }
    function processExport(format) {
        const type = document.getElementById('exp-type').value;
        let start = document.getElementById('exp-start').value;
        let end = document.getElementById('exp-end').value;

        if (type !== 'INV' && (!start || !end)) { showCustomAlert("Warning", "Silakan lengkapi tanggal mulai dan tanggal akhir."); return; }
        document.getElementById('exp-loading').classList.remove('hidden');

        fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'exportData', role: currentUser.role, username: currentUser.username, export_type: type, start_date: start, end_date: end }) })
        .then(r => r.json()).then(res => {
            document.getElementById('exp-loading').classList.add('hidden');
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) {
                if(!res.data || res.data.length === 0) { showCustomAlert("Info", "Tidak ada data pada rentang waktu yang dipilih."); return; }
                if (format === 'excel') generateExcel(res.data, type); else generatePdf(res.data, type);
                closeModal('modal-export');
            } else { showCustomAlert("Error", res.message); }
        }).catch(err => { document.getElementById('exp-loading').classList.add('hidden'); showCustomAlert("Error", t('err_conn')); });
    }

    // HELPER: Cek Hak Akses Edit Harga (Semua view, hanya user diizinkan yang edit)
    function canEditPriceHelper() {
        const accRights = currentUser.access_rights ? JSON.parse(currentUser.access_rights) : [];
        const isAppAdmin = currentUser.role === 'Administrator';
        return isAppAdmin || accRights.includes('edit_price');
    }

    function generateExcel(data, type) {
        const wb = XLSX.utils.book_new(); const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        let rows = [[`AUDIT REPORT - ${type === 'GI' ? 'GOOD ISSUE' : (type === 'GR' ? 'GOOD RECEIVE' : 'MASTER INVENTORY')}`], [`Generated By: ${currentUser.fullname} (${currentUser.role})`], [`Date: ${new Date().toLocaleString('id-ID')}`], []];

        // Export selalu menampilkan Harga untuk transparansi audit
        if (type === 'GI') {
            let header = ["ID Request", "Tanggal", "Nomor GI ERP", "Nama", "Departemen", "Seksi", "Deskripsi Aktivitas", "Kode Barang", "Nama Barang", "Qty", "UoM", "Harga Satuan", "Total Harga", "Reason Code", "Cost Center", "Status L1", "Status WH", "Foto Issued", "Diterima Oleh", "Tanggal Terima", "Foto Received"];
            rows.push(header);
            data.forEach(r => { const fIssue = (r.issue_photo && r.issue_photo !== '0') ? baseUrl + r.issue_photo : '-'; const fRecv = (r.receive_photo && r.receive_photo !== '0') ? baseUrl + r.receive_photo : '-'; 
                r.items.forEach(i => { 
                    rows.push([r.req_id, r.created_at, r.erp_gi_no||'-', r.fullname, r.department, r.section, r.purpose, i.code, i.name, parseInt(i.qty), i.uom, parseFloat(i.price||0), parseFloat(i.price||0) * parseInt(i.qty), i.reason_code||'-', i.cost_center||'-', r.app_head, r.app_wh, fIssue, r.received_by||'-', r.receive_time||'-', fRecv]); 
                }); 
            });
        } else if (type === 'GR') {
            let header = ["ID Receive", "Tanggal", "Nomor GR ERP", "Diterima Oleh", "Remarks / Supplier", "Link Foto Bukti", "Kode Barang", "Nama Barang", "Qty Masuk", "UoM", "Harga Satuan", "Total Harga"];
            rows.push(header);
            data.forEach(r => { const fGr = (r.gr_photo && r.gr_photo !== '0') ? baseUrl + r.gr_photo : '-'; 
                r.items.forEach(i => { 
                    rows.push([r.gr_id, r.created_at, r.erp_gr_no||'-', r.fullname, r.remarks, fGr, i.code, i.name, parseInt(i.qty), i.uom, parseFloat(i.price||0), parseFloat(i.price||0) * parseInt(i.qty)]); 
                }); 
            });
        } else if (type === 'INV') {
            let header = ["Kode Barang", "Nama Barang", "Spesifikasi", "Kategori", "UoM", "Stok Terkini", "Harga", "Terakhir Update"];
            rows.push(header);
            data.forEach(r => { 
                rows.push([r.item_code, r.item_name, r.item_spec||'-', r.category, r.uom, parseInt(r.stock), parseFloat(r.price||0), r.last_updated]); 
            });
        }

        const ws = XLSX.utils.aoa_to_sheet(rows); XLSX.utils.book_append_sheet(wb, ws, "Report"); XLSX.writeFile(wb, `GIS_Report_${type}_${new Date().getTime()}.xlsx`);
    }
    
    function generatePdf(data, type) {
        const { jsPDF } = window.jspdf; const doc = new jsPDF('landscape', 'mm', 'a4'); doc.setFontSize(14); doc.text(`Audit Report - ${type}`, 14, 15); doc.setFontSize(9); doc.text(`Generated By: ${currentUser.fullname} | Date: ${new Date().toLocaleString('id-ID')}`, 14, 21);
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/'); let head = []; let body = [];

        if (type === 'GI') { 
            head = [['Req ID/Date', 'ERP No', 'Requestor/Sec', 'Description', 'Item Code & Name', 'Qty/UoM', 'Total Price', 'RC / CC', 'App/Rcv Status']]; 
            data.forEach(r => { r.items.forEach(i => { let st = `L1: ${r.app_head}\nWH: ${r.app_wh}`; if(r.received_by) st += `\nRCV: ${r.received_by}\n${r.receive_time}`; 
                body.push([`${r.req_id}\n${r.created_at}`, r.erp_gi_no || '-', `${r.fullname}\n(${r.department} / ${r.section})`, r.purpose, `${i.code}\n${i.name}`, `${i.qty} ${i.uom}`, 'Rp ' + (parseFloat(i.price||0) * parseInt(i.qty)).toLocaleString('id-ID'), `${i.reason_code||'-'} / ${i.cost_center||'-'}`, st]); 
            }); }); 
        }
        else if (type === 'GR') { 
            head = [['GR ID / Date', 'ERP No', 'Received By', 'Supplier/Remarks', 'Proof Link', 'Item Code & Name', 'Qty', 'UoM', 'Total Price']]; 
            data.forEach(r => { const fGr = (r.gr_photo && r.gr_photo !== '0') ? baseUrl + r.gr_photo : '-'; r.items.forEach(i => { 
                body.push([`${r.gr_id}\n${r.created_at}`, r.erp_gr_no || '-', r.fullname, r.remarks, fGr, `${i.code}\n${i.name}`, i.qty, i.uom, 'Rp ' + (parseFloat(i.price||0) * parseInt(i.qty)).toLocaleString('id-ID')]); 
            }); }); 
        }
        else if (type === 'INV') { 
            head = [['Item Code', 'Item Name', 'Specification', 'Category', 'UoM', 'Stock', 'Price']]; 
            data.forEach(r => { 
                body.push([r.item_code, r.item_name, r.item_spec||'-', r.category, r.uom, r.stock, 'Rp ' + parseFloat(r.price||0).toLocaleString('id-ID')]); 
            }); 
        }
        doc.autoTable({ startY: 26, head: head, body: body, theme: 'grid', styles: { fontSize: 7, cellPadding: 2, overflow: 'linebreak' }, headStyles: { fillColor: [79, 70, 229] }}); doc.save(`GIS_Report_${type}_${new Date().getTime()}.pdf`);
    }

    function showDropdown(inp, type) { document.querySelectorAll('.dropdown-list').forEach(e => e.classList.add('hidden')); const list = inp.parentElement.querySelector('.dropdown-list'); list.classList.remove('hidden'); renderDropdownItems(inp, type); }
    function filterDropdown(inp, type) { renderDropdownItems(inp, type); }
    function renderDropdownItems(inp, type) {
        const list = inp.parentElement.querySelector('.dropdown-list'); const f = inp.value.toLowerCase();
        const filtered = inventoryData.filter(i => (i.item_code && i.item_code.toLowerCase().includes(f)) || (i.item_name && i.item_name.toLowerCase().includes(f)) || (i.item_spec && i.item_spec.toLowerCase().includes(f))).slice(0, 50);
        if(filtered.length === 0) { list.innerHTML = `<div class="p-2 text-xs text-slate-500 italic" data-translate="true">Tidak ada hasil</div>`; AutoTranslator.processDOM(); return; }
        let htmlArray = [];
        filtered.forEach(i => {
            const safeCode = (i.item_code||'').replace(/'/g, "\\'").replace(/"/g, '&quot;'); const safeName = (i.item_name||'').replace(/'/g, "\\'").replace(/"/g, '&quot;'); const safeSpec = (i.item_spec||'').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            let specDisplay = i.item_spec ? ` - ${i.item_spec}` : '';
            htmlArray.push(`<div class="p-2 hover:bg-indigo-50 cursor-pointer text-xs border-b border-slate-50 transition-colors" onmousedown="selectItemOption(this, '${safeCode}', '${safeName}', '${safeSpec}', '${i.stock}', '${i.uom}', '${i.price || 0}', '${type}')"><div class="font-bold text-indigo-700">${i.item_code}</div><div class="text-slate-600">${i.item_name}${specDisplay} (Stk: ${i.stock})</div></div>`);
        });
        list.innerHTML = htmlArray.join(''); AutoTranslator.processDOM();
    }

    function selectItemOption(el, code, name, spec, stock, uom, price, type) {
        const row = el.closest(`div[id^="${type}-row-"]`); let displayVal = code + ' - ' + name; if(spec) displayVal += ' (' + spec + ')';
        row.querySelector(`.${type}-item-display`).value = displayVal; row.querySelector(`.${type}-item-code`).value = code; row.querySelector(`.${type}-item-name`).value = spec ? name + ' - ' + spec : name; row.querySelector(`.${type}-uom`).value = uom;
        const stockInput = row.querySelector(`.${type}-stock`); if(stockInput) stockInput.value = stock;
        
        if(type === 'gi') { 
            const qtyInput = row.querySelector(`.${type}-qty`); qtyInput.max = stock; qtyInput.title = "Max stock: " + stock; 
            const rawPriceEl = row.querySelector('.gi-price-raw'); const dispPriceEl = row.querySelector('.gi-price-display');
            if(rawPriceEl) rawPriceEl.value = price;
            if(dispPriceEl) dispPriceEl.value = 'Rp ' + parseFloat(price).toLocaleString('id-ID');
            if(typeof calculateGiTotal === 'function') calculateGiTotal();
        }
        if(type === 'gr') {
            const priceInput = row.querySelector('.gr-price');
            if(priceInput && (!priceInput.value || priceInput.value == 0)) { priceInput.value = price; }
            if(typeof calculateGrTotal === 'function') calculateGrTotal();
        }
        el.closest('.dropdown-list').classList.add('hidden');
    }
    document.addEventListener('click', function(e) { if(!e.target.closest('.relative.w-full')) { document.querySelectorAll('.dropdown-list').forEach(el => el.classList.add('hidden')); } });

    function compressImage(base64Str, maxWidth = 1000, quality = 0.6) {
        return new Promise((resolve, reject) => {
            const img = new Image(); img.src = base64Str;
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas'); let width = img.width; let height = img.height;
                    if (width > maxWidth) { height *= maxWidth / width; width = maxWidth; }
                    canvas.width = width; canvas.height = height; const ctx = canvas.getContext('2d'); ctx.drawImage(img, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/jpeg', quality));
                } catch(e) { reject("Canvas error: " + e.message); }
            };
            img.onerror = () => resolve(base64Str);
        });
    }

    // --- USERS MANAGEMENT JAVASCRIPT GLOBAL ---
    function openManageUsers() { openModal('modal-users'); loadUsers(); }
    function loadUsers() { fetch('api/users.php', {method:'POST', body:JSON.stringify({action:'getAllUsers'})}).then(r=>r.json()).then(d => { if(d.code===401){logoutAction(); return;} allUsers = Array.isArray(d) ? d : (d.data || []); renderUsers(allUsers); }); }
    function renderUsers(data) { const c = document.getElementById('user-list'); c.innerHTML = ''; let htmlArray = []; data.forEach(u => { htmlArray.push(`<div onclick="editUser('${u.username}')" class="p-3 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 cursor-pointer text-xs mb-2 transition shadow-sm bg-white"><div class="font-bold text-slate-700">${u.fullname}</div><div class="text-[10px] text-slate-500 mt-1.5">${u.username} • <span class="bg-slate-100 px-1.5 py-0.5 rounded font-bold">${u.role}</span></div></div>`); }); c.innerHTML = htmlArray.join(''); }
    function filterUsers() { const t = document.getElementById('search-user').value.toLowerCase(); renderUsers(allUsers.filter(u => u.fullname.toLowerCase().includes(t) || u.username.toLowerCase().includes(t))); }
    function handleRoleChange(sel) { if(sel.value === 'Administrator') { document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = true; chk.disabled = true; }); } else { document.querySelectorAll('.acc-chk').forEach(chk => { chk.disabled = false; }); } }
    function resetUserForm() { document.getElementById('user-form').reset(); document.getElementById('u-user').disabled=false; document.getElementById('u-pass').required=true; document.getElementById('btn-del-user').classList.add('hidden'); document.getElementById('form-title').innerText = 'Create User'; document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = false; chk.disabled = false; }); document.getElementById('chk-gi-submit').checked = true; AutoTranslator.processDOM(); }
    function editUser(u) {
        const user = allUsers.find(x => x.username === u);
        document.getElementById('u-user').value = user.username; document.getElementById('u-user').disabled=true;
        document.getElementById('u-pass').value = ''; document.getElementById('u-pass').required=false;
        document.getElementById('u-name').value = user.fullname; document.getElementById('u-nik').value = user.nik || ''; 
        document.getElementById('u-role').value = user.role; document.getElementById('u-dept').value = user.department; 
        document.getElementById('u-phone').value = user.phone || ''; document.getElementById('btn-del-user').classList.remove('hidden'); 
        document.getElementById('form-title').innerText = 'Edit User';
        const acc = JSON.parse(user.access_rights || '[]');
        document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = acc.includes(chk.value); });
        if(user.role === 'Administrator') { document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = true; chk.disabled = true; }); } else { document.querySelectorAll('.acc-chk').forEach(chk => { chk.disabled = false; }); }
        AutoTranslator.processDOM();
    }
    function saveUser() {
        let acc = []; document.querySelectorAll('.acc-chk').forEach(chk => { if(chk.checked && !chk.disabled) acc.push(chk.value); });
        if (document.getElementById('u-role').value === 'Administrator') { acc = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data', 'edit_price']; }
        const p = { action: 'saveUser', isEdit: document.getElementById('u-user').disabled, data: { username: document.getElementById('u-user').value, password: document.getElementById('u-pass').value, fullname: document.getElementById('u-name').value, nik: document.getElementById('u-nik').value, role: document.getElementById('u-role').value, department: document.getElementById('u-dept').value, phone: document.getElementById('u-phone').value, access_rights: JSON.stringify(acc) } };
        fetch('api/users.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { if(res.code === 401) { logoutAction(); return; } if(res.success){ resetUserForm(); loadUsers(); showCustomAlert("Success", "Berhasil simpan user."); } else showCustomAlert("Error", res.message); });
    }
    function deleteUser() { showCustomConfirm("Delete User", "Hapus user ini?", () => { fetch('api/users.php', {method:'POST', body:JSON.stringify({action:'deleteUser', username:document.getElementById('u-user').value})}).then(r=>r.json()).then(res => { if(res.success){ resetUserForm(); loadUsers(); } }); }); }

  </script>
</body>
</html>