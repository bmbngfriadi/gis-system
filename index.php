<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Good Issue & Inventory System</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <link rel="icon" type="image/png" href="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png">
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
  
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
    .hidden-important { display: none !important; }
    .loader-spin { border: 3px solid #e2e8f0; border-top: 3px solid #4f46e5; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.8s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .shine-effect { position: relative; overflow: hidden; }
    .shine-effect::before {
        content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
        transform: skewX(-20deg); animation: shine 5s infinite; z-index: 1;
    }
    @keyframes shine { 0% { left: -100%; } 20% { left: 200%; } 100% { left: 200%; } }
    @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .bg-live-gradient { background-size: 200% 200%; animation: gradientBG 4s ease infinite; }
    
    .status-badge { padding: 6px 12px; border-radius: 9999px; font-weight: 800; font-size: 0.65rem; text-transform: uppercase; border: 1px solid transparent; letter-spacing: 0.05em; display: inline-flex; align-items: center; justify-content: center;}
    
    /* Animation for Table Buttons */
    .btn-animated { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
    .btn-animated:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
    .btn-animated:active { transform: translateY(1px) scale(0.96); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    
    /* Clickable Card Filter Active State */
    .card-filter { cursor: pointer; transition: all 0.3s ease; }
    .card-filter:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1); z-index: 10; }
    .card-filter-active { ring: 4px; --tw-ring-color: rgba(255,255,255,0.6); --tw-ring-offset-width: 2px; --tw-ring-offset-color: transparent; box-shadow: 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color), 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color), 0 10px 15px -3px rgba(0,0,0,0.1); transform: scale(1.02); z-index: 20; }
    
    .blob-bg { position: absolute; border-radius: 50%; filter: blur(20px); opacity: 0.4; animation: blobMove 6s infinite alternate; z-index: 0; }
    @keyframes blobMove { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(10px, -15px) scale(1.2); } }

    .tab-active { border-bottom: 3px solid #4f46e5; color: #4f46e5; font-weight: 800; }
    .tab-inactive { color: #64748b; font-weight: 600; transition: color 0.3s; border-bottom: 3px solid transparent; }
    .tab-inactive:hover { color: #4f46e5; }
    
    .custom-scroll::-webkit-scrollbar { width: 5px; height: 5px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    
    .dropdown-scroll::-webkit-scrollbar { width: 5px; }
    .dropdown-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
    .dropdown-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
  </style>
</head>
<body class="text-slate-800 h-screen flex flex-col">

  <div id="login-view" class="flex-grow flex items-center justify-center p-6 relative">
      <div class="blob-bg bg-indigo-200 w-96 h-96 -top-10 -left-10 z-0"></div>
      <div class="blob-bg bg-fuchsia-200 w-96 h-96 bottom-0 right-0 z-0" style="animation-delay: 2s;"></div>
      
      <div class="w-full max-w-sm bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white relative z-10 animate-slide-up">
         <div class="text-center mb-8">
            <img src="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png" alt="Company Logo" class="h-20 mx-auto mb-4 object-contain drop-shadow-md">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">GIS Portal</h1>
            <p class="text-xs text-slate-500 font-medium" data-i18n="app_desc">Good Issue & Inventory System</p>
         </div>
         <form onsubmit="event.preventDefault(); handleLogin();" class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Username</label>
                <input type="text" id="login-u" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>
            <div>
               <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Password</label>
               <div class="relative">
                   <input type="password" id="login-p" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none pr-10" required>
                   <button type="button" onclick="toggleLoginPass()" class="absolute right-3 top-3.5 text-slate-400 hover:text-indigo-600 transition"><i id="icon-login-pass" class="fas fa-eye"></i></button>
               </div>
               <div class="text-right mt-1.5">
                   <button type="button" onclick="openForgotModal()" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold" data-i18n="forgot_pass">Lupa Password?</button>
               </div>
            </div>
            <button type="submit" id="btn-login" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3 rounded-xl shadow-md hover:opacity-90 transition btn-animated mt-2" data-i18n="login">Login</button>
         </form>
      </div>
  </div>

  <div id="app-view" class="hidden-important flex flex-col h-full w-full">
    <nav class="bg-gradient-to-r from-indigo-700 to-purple-800 text-white shadow-md sticky top-0 z-40 flex-none">
       <div class="container mx-auto px-4 py-3 flex justify-between items-center">
         <div class="flex items-center gap-3">
             <div class="bg-white/90 p-1.5 rounded-lg shadow-sm"><img src="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png" alt="Logo" class="h-6 w-auto object-contain"></div>
             <div class="flex flex-col"><span class="font-bold leading-none text-base tracking-tight">GIS System</span><span class="text-[10px] text-indigo-200">PT Cemindo Gemilang</span></div>
         </div>
         <div class="flex items-center gap-2 sm:gap-4">
             <button onclick="toggleLanguage()" class="bg-indigo-900/40 w-8 h-8 rounded-full hover:bg-indigo-900 text-[10px] font-bold border border-indigo-400/50 transition flex items-center justify-center text-indigo-100 hover:text-white"><span id="lang-label">EN</span></button>
             <div class="text-right text-xs hidden sm:block"><div id="nav-user" class="font-bold">User</div><div id="nav-role" class="text-indigo-200">Role</div></div>
             <div class="h-8 w-px bg-indigo-500/50 hidden sm:block mx-1"></div>
             
             <button onclick="openProfileModal()" class="bg-indigo-900/40 p-2.5 rounded-full hover:bg-indigo-900 text-xs border border-indigo-400/50 transition btn-animated" title="My Profile"><i class="fas fa-user-circle"></i></button>
             <button id="btn-admin-users" onclick="openManageUsers()" class="hidden bg-indigo-900/40 p-2.5 rounded-full hover:bg-indigo-900 text-xs border border-indigo-400/50 transition btn-animated" title="Manage Users"><i class="fas fa-users-cog"></i></button>
             <button onclick="logoutAction()" class="bg-rose-600 p-2.5 rounded-full hover:bg-rose-700 text-xs border border-rose-400/50 transition btn-animated" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
         </div>
       </div>
    </nav>
    
    <main class="flex-grow container mx-auto px-4 py-6 overflow-y-auto custom-scroll">
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 mb-6 pb-4 sm:pb-2 gap-4">
          <div class="flex gap-2 overflow-x-auto custom-scroll w-full sm:w-auto pb-2 sm:pb-0">
              <button onclick="switchTab('gi')" id="tab-gi" class="px-5 py-2.5 text-sm tab-active transition-colors whitespace-nowrap flex items-center"><i class="fas fa-file-export mr-2"></i> <span data-i18n="tab_gi">Good Issue (GI)</span></button>
              <button onclick="switchTab('gr')" id="tab-gr" class="hidden px-5 py-2.5 text-sm tab-inactive transition-colors whitespace-nowrap flex items-center"><i class="fas fa-file-import mr-2"></i> <span data-i18n="tab_gr">Good Receive (GR)</span></button>
              <button onclick="switchTab('inv')" id="tab-inv" class="px-5 py-2.5 text-sm tab-inactive transition-colors whitespace-nowrap flex items-center"><i class="fas fa-warehouse mr-2"></i> <span data-i18n="tab_inv">Inventory</span></button>
          </div>
          <button id="btn-export-global" onclick="openExportModal()" class="hidden w-full sm:w-auto bg-slate-800 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-slate-900 transition btn-animated flex items-center justify-center"><i class="fas fa-print mr-2"></i> <span data-i18n="btn_export_data">Export Report</span></button>
      </div>

      <div id="view-gi" class="space-y-6 animate-slide-up">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 sm:gap-4">
            <div id="card-gi-all" onclick="setGiFilter('All')" class="card-filter card-filter-active bg-gradient-to-br from-blue-500 to-indigo-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-list"></i></div>
                <div class="z-10"><div class="text-[8px] font-bold text-blue-100 uppercase tracking-wider mb-0.5" data-i18n="stat_tot_gi">Total Request</div><div class="text-xl font-black" id="stat-total">0</div></div>
            </div>
            <div id="card-gi-pending" onclick="setGiFilter('Pending Head')" class="card-filter bg-gradient-to-br from-amber-400 to-orange-500 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white bg-live-gradient">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-user-clock"></i></div>
                <div class="z-10"><div class="text-[8px] font-bold text-amber-100 uppercase tracking-wider mb-0.5" data-i18n="stat_pend_head">Pending Head</div><div class="text-xl font-black" id="stat-pending-head">0</div></div>
            </div>
            <div id="card-gi-wh" onclick="setGiFilter('Pending Warehouse')" class="card-filter bg-gradient-to-br from-pink-500 to-rose-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-truck-loading"></i></div>
                <div class="z-10"><div class="text-[8px] font-bold text-pink-100 uppercase tracking-wider mb-0.5" data-i18n="stat_pend_wh">Pending WH</div><div class="text-xl font-black" id="stat-pending-wh">0</div></div>
            </div>
            <div id="card-gi-receive" onclick="setGiFilter('Pending Receive')" class="card-filter bg-gradient-to-br from-cyan-500 to-blue-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-people-carry-box"></i></div>
                <div class="z-10"><div class="text-[8px] font-bold text-cyan-100 uppercase tracking-wider mb-0.5" data-i18n="stat_pend_recv">Pending Receive</div><div class="text-xl font-black" id="stat-pending-recv">0</div></div>
            </div>
            <div id="card-gi-done" onclick="setGiFilter('Completed')" class="card-filter bg-gradient-to-br from-emerald-500 to-teal-600 p-4 rounded-2xl shadow-lg flex items-center gap-3 relative group shine-effect text-white">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-sm z-10"><i class="fas fa-check-double"></i></div>
                <div class="z-10"><div class="text-[8px] font-bold text-emerald-100 uppercase tracking-wider mb-0.5" data-i18n="stat_comp">Completed</div><div class="text-xl font-black" id="stat-done">0</div></div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-700" data-i18n="hist_gi">Good Issue History</h2>
                <p class="text-xs text-slate-500" data-i18n="click_filter_info">Klik kartu di atas untuk memfilter data.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                    <input type="text" id="search-gi" onkeyup="filterGI()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition" data-i18n-ph="ph_search_gi" placeholder="Search GI...">
                </div>
                <button id="btn-create-gi" onclick="openGiModal()" class="hidden w-full sm:w-auto bg-indigo-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 btn-animated flex items-center justify-center whitespace-nowrap"><i class="fas fa-plus mr-2"></i> <span data-i18n="btn_new_gi">New GI Form</span></button>
            </div>
        </div>

        <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
            <div id="gi-card-container" class="md:hidden flex flex-col gap-4"></div>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-4" data-i18n="th_id">NO GIF & Date</th>
                            <th class="px-6 py-4" data-i18n="th_req">Requestor Info</th>
                            <th class="px-6 py-4 min-w-[350px] max-w-[450px]" data-i18n="th_items">Items & Activities Description</th>
                            <th class="px-6 py-4 text-center w-[180px]" data-i18n="th_stat">Status</th>
                            <th class="px-6 py-4 text-right w-[140px]" data-i18n="th_act">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gi-table-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
      </div>

      <div id="view-gr" class="hidden space-y-6 animate-slide-up">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-700" data-i18n="hist_gr">Good Receive History</h2>
                <p class="text-xs text-slate-500" data-i18n="desc_gr">Log of incoming items to warehouse.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                    <input type="text" id="search-gr" onkeyup="filterGR()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-teal-500 shadow-sm transition" data-i18n-ph="ph_search_gr" placeholder="Search GR...">
                </div>
                <button id="btn-create-gr" onclick="openGrModal()" class="hidden w-full sm:w-auto bg-teal-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-teal-700 btn-animated flex justify-center items-center whitespace-nowrap"><i class="fas fa-plus mr-2"></i> <span data-i18n="btn_new_gr">New GR Form</span></button>
            </div>
        </div>

        <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
            <div id="gr-card-container" class="md:hidden flex flex-col gap-4"></div>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                        <tr><th class="px-6 py-4" data-i18n="th_gr_id">GR ID & Date</th><th class="px-6 py-4" data-i18n="th_gr_by">Received By</th><th class="px-6 py-4" data-i18n="th_gr_rem">Remarks / Supplier</th><th class="px-6 py-4 w-1/2" data-i18n="th_gr_items">Items Received</th></tr>
                    </thead>
                    <tbody id="gr-table-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
      </div>

      <div id="view-inv" class="hidden space-y-6 animate-slide-up">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-700" data-i18n="mast_inv">Master Inventory</h2>
                <p class="text-xs text-slate-500" data-i18n="desc_inv">Manage warehouse items and stock.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                    <input type="text" id="search-inv" onkeyup="filterInventory()" class="w-full border border-slate-300 rounded-xl p-3 pl-9 text-sm outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm transition" data-i18n-ph="ph_search_inv" placeholder="Search Item (Code/Name)...">
                </div>

                <div class="grid grid-cols-3 sm:flex gap-2 w-full sm:w-auto">
                    <button id="btn-tpl-item" onclick="downloadItemTemplate()" class="hidden w-full sm:w-auto bg-white text-slate-700 border border-slate-300 px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-slate-50 btn-animated flex justify-center items-center" title="Template"><i class="fas fa-download"></i> <span class="hidden sm:inline ml-1" data-i18n="btn_template">Template</span></button>
                    <button id="btn-imp-item" onclick="document.getElementById('import-item-file').click()" class="hidden w-full sm:w-auto bg-blue-600 text-white px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-blue-700 btn-animated flex justify-center items-center" title="Import"><i class="fas fa-file-import"></i> <span class="hidden sm:inline ml-1" data-i18n="btn_import_items">Import</span></button>
                    <input type="file" id="import-item-file" accept=".xlsx, .xls" class="hidden" onchange="handleImportItems(event)">
                    <button id="btn-exp-item" onclick="exportItems()" class="hidden w-full sm:w-auto bg-indigo-600 text-white px-3 py-3 sm:py-2.5 rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-700 btn-animated flex justify-center items-center" title="Export"><i class="fas fa-file-export"></i> <span class="hidden sm:inline ml-1" data-i18n="btn_export_items">Export</span></button>
                </div>
                
                <button id="btn-add-item" onclick="openItemModal()" class="hidden w-full sm:w-auto bg-emerald-600 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-emerald-700 btn-animated flex justify-center items-center whitespace-nowrap"><i class="fas fa-box-open mr-2"></i> <span data-i18n="btn_add_item">Add Item Master</span></button>
            </div>
        </div>

        <div class="bg-transparent sm:bg-white sm:rounded-2xl sm:shadow-sm sm:border sm:border-slate-200 overflow-hidden">
            <div id="inv-card-container" class="md:hidden flex flex-col gap-4"></div>
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold">
                        <tr><th class="px-6 py-4" data-i18n="th_it_code">Item Code</th><th class="px-6 py-4" data-i18n="th_it_name">Item Name</th><th class="px-6 py-4" data-i18n="th_it_cat">Category</th><th class="px-6 py-4 text-center" data-i18n="th_it_stock">Stock / UoM</th><th class="px-6 py-4 text-right hidden" id="th-inv-act" data-i18n="th_act">Action</th></tr>
                    </thead>
                    <tbody id="inv-table-body" class="divide-y divide-slate-100"></tbody>
                </table>
            </div>
        </div>
      </div>

    </main>
  </div>

  <div id="modal-export" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl animate-slide-up overflow-hidden">
          <div class="bg-slate-800 px-6 py-5 border-b border-slate-700 flex justify-between items-center text-white">
              <h3 class="font-bold text-lg tracking-tight"><i class="fas fa-print text-indigo-400 mr-2"></i> <span data-i18n="export_data_title">Export Report</span></h3>
              <button onclick="closeModal('modal-export')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
          </div>
          <div class="p-8">
              <div class="mb-5">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-i18n="export_type">Data Type</label>
                  <select id="exp-type" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition font-medium" onchange="toggleExpDates()">
                      <option value="GI">Good Issue History</option>
                      <option value="GR">Good Receive History</option>
                      <option value="INV">Master Inventory (Current)</option>
                  </select>
              </div>
              <div id="exp-date-group" class="grid grid-cols-2 gap-4 mb-8">
                  <div>
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-i18n="start_date">Start Date</label>
                      <input type="date" id="exp-start" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
                  </div>
                  <div>
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2" data-i18n="end_date">End Date</label>
                      <input type="date" id="exp-end" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
                  </div>
              </div>
              
              <div class="flex gap-3">
                  <button onclick="processExport('excel')" class="flex-1 py-3.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-md transition btn-animated"><i class="fas fa-file-excel mr-1.5"></i> Excel</button>
                  <button onclick="processExport('pdf')" class="flex-1 py-3.5 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 shadow-md transition btn-animated"><i class="fas fa-file-pdf mr-1.5"></i> PDF</button>
              </div>
              <div id="exp-loading" class="hidden text-center mt-5 text-xs font-bold text-indigo-600 animate-pulse"><i class="fas fa-spinner fa-spin mr-2"></i> Processing Data...</div>
          </div>
      </div>
  </div>

  <div id="modal-alert-custom" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="p-8 text-center">
              <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-5 text-indigo-600 shadow-inner"><i class="fas fa-info text-2xl"></i></div>
              <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight" id="alert-custom-title">Information</h3>
              <p class="text-sm text-slate-500 mb-8 leading-relaxed font-medium" id="alert-custom-msg">Message</p>
              <button onclick="closeModal('modal-alert-custom')" class="w-full py-3.5 bg-slate-800 text-white rounded-xl font-bold text-sm hover:bg-slate-900 shadow-md transition btn-animated" data-i18n="btn_ok">OK</button>
          </div>
      </div>
  </div>

  <div id="modal-confirm-custom" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="p-8 text-center">
              <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-5 text-blue-600 shadow-inner"><i class="fas fa-question text-2xl"></i></div>
              <h3 class="text-xl font-black text-slate-800 mb-2 tracking-tight" id="confirm-custom-title">Confirm</h3>
              <p class="text-sm text-slate-500 mb-8 leading-relaxed font-medium" id="confirm-custom-msg">Are you sure?</p>
              <div class="flex gap-3">
                  <button onclick="closeModal('modal-confirm-custom')" class="flex-1 py-3.5 border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition btn-animated" data-i18n="btn_cancel">Cancel</button>
                  <button onclick="executeCustomConfirm()" class="flex-1 py-3.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition btn-animated" data-i18n="btn_yes">Yes, Proceed</button>
              </div>
          </div>
      </div>
  </div>

  <div id="modal-forgot" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center">
              <h3 class="font-bold text-slate-800 tracking-tight" data-i18n="reset_pass">Reset Password</h3>
              <button onclick="closeModal('modal-forgot')" class="text-slate-400 hover:text-red-500"><i class="fas fa-times text-lg"></i></button>
          </div>
          <div class="p-6">
              <div class="mb-5 bg-indigo-50 text-indigo-700 text-[10px] p-4 rounded-xl border border-indigo-100 font-medium" data-i18n="reset_info">
                  <i class="fab fa-whatsapp mr-1 text-base align-middle"></i> Link reset akan dikirimkan ke nomor WhatsApp Anda.
              </div>
              <div class="mb-6">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                  <input type="text" id="forgot-username" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
              </div>
              <button onclick="submitForgot()" id="btn-forgot" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-md hover:bg-indigo-700 transition btn-animated" data-i18n="btn_send_wa">Kirim Link WhatsApp</button>
          </div>
      </div>
  </div>

  <div id="modal-profile" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] animate-slide-up overflow-hidden">
          <div class="bg-indigo-600 px-6 py-5 flex justify-between items-center flex-none">
              <h3 class="font-bold text-white tracking-wide"><i class="fas fa-user-edit mr-2"></i> <span data-i18n="my_profile">My Profile</span></h3>
              <button onclick="closeModal('modal-profile')" class="text-indigo-200 hover:text-white"><i class="fas fa-times text-lg"></i></button>
          </div>
          <div class="p-6 overflow-y-auto flex-1 custom-scroll">
              <div class="grid grid-cols-2 gap-5 mb-5">
                  <div class="col-span-1">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK</label>
                      <input type="text" id="prof-nik" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled>
                  </div>
                  <div class="col-span-1">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="dept">Department</label>
                      <input type="text" id="prof-dept" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled>
                  </div>
                  <div class="col-span-2">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="fullname">Fullname</label>
                      <input type="text" id="prof-name" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-sm text-slate-600 font-bold" readonly disabled>
                  </div>
              </div>

              <div class="border-t border-slate-200 pt-5 mt-2">
                  <div class="mb-5">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5"><span data-i18n="wa_phone">No WhatsApp</span> <span class="text-blue-500 lowercase font-medium italic" data-i18n="editable">(Dapat Diubah)</span></label>
                      <input type="text" id="prof-phone" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="Contoh: 0812345678">
                  </div>
                  <div class="mb-2">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5"><span data-i18n="new_pass">Password Baru</span> <span class="text-slate-400 lowercase font-medium italic" data-i18n="pass_note">(Kosongkan jika tidak diubah)</span></label>
                      <input type="password" id="prof-pass" class="w-full border border-slate-300 rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" placeholder="******">
                  </div>
              </div>
          </div>
          <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
              <button onclick="closeModal('modal-profile')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-i18n="btn_cancel">Cancel</button>
              <button onclick="saveProfile()" id="btn-save-profile" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700" data-i18n="btn_update_prof">Update Profile</button>
          </div>
      </div>
  </div>

  <div id="modal-action-photo" class="hidden fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl flex flex-col max-h-[90vh] animate-slide-up overflow-hidden">
          <div class="bg-slate-800 px-6 py-5 flex justify-between items-center flex-none">
              <h3 class="font-bold text-white tracking-wide" id="action-photo-title"><i class="fas fa-camera mr-2"></i> Photo Proof</h3>
              <button onclick="closeModal('modal-action-photo')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
          </div>
          <div class="p-6 overflow-y-auto flex-1 custom-scroll">
              <input type="hidden" id="action-photo-id">
              <input type="hidden" id="action-photo-type">
              
              <p class="text-xs text-slate-500 mb-4" id="action-photo-desc">Silakan lampirkan foto sebagai bukti transaksi ini.</p>
              
              <div class="flex gap-2 mb-4">
                  <button type="button" onclick="toggleActionPhotoSource('file')" id="btn-act-file" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-indigo-600 text-white shadow-md transition btn-animated"><i class="fas fa-file-upload mr-1.5"></i> Upload</button>
                  <button type="button" onclick="toggleActionPhotoSource('camera')" id="btn-act-cam" class="flex-1 py-2.5 text-xs font-bold rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition btn-animated"><i class="fas fa-camera mr-1.5"></i> Camera</button>
              </div>

              <div id="source-act-file" class="border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition flex items-center justify-center h-48 bg-slate-50 cursor-pointer" onclick="document.getElementById('input-act-photo').click()">
                  <div class="space-y-3 pointer-events-none">
                      <i class="fas fa-cloud-upload-alt text-4xl text-slate-300"></i>
                      <p class="text-sm font-bold text-slate-500" id="act-file-name">Click to upload image</p>
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
              <button onclick="closeModal('modal-action-photo')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated">Cancel</button>
              <button onclick="submitActionWithPhoto()" id="btn-submit-action-photo" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700">Process</button>
          </div>
      </div>
  </div>

  <div id="modal-image-viewer" class="hidden fixed inset-0 bg-slate-900/95 backdrop-blur-md z-[110] flex items-center justify-center p-4 cursor-pointer" onclick="closeModal('modal-image-viewer')">
      <div class="relative w-full max-w-4xl flex justify-center items-center animate-slide-up" onclick="event.stopPropagation()">
          <button onclick="closeModal('modal-image-viewer')" class="absolute -top-12 right-0 text-white/70 hover:text-white text-4xl transition hover:scale-110">&times;</button>
          <img id="img-viewer-src" src="" class="max-w-full max-h-[85vh] rounded-xl shadow-2xl object-contain border-2 border-white/10 bg-slate-800">
      </div>
  </div>
  
  <div id="modal-gi" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeAllDropdowns(event)">
      <div class="bg-white rounded-3xl w-full max-w-5xl shadow-2xl flex flex-col max-h-[95vh] animate-slide-up">
          <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center flex-none rounded-t-3xl">
              <h3 class="font-bold text-slate-800 tracking-tight" id="modal-gi-title"><i class="fas fa-file-invoice text-indigo-600 mr-2 text-lg"></i> <span data-i18n="form_gi">Form Good Issue</span></h3>
              <button onclick="closeModal('modal-gi')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
          </div>
          <div class="p-6 overflow-y-auto flex-1 custom-scroll" id="gi-scroll-container">
              <form id="form-gi">
                  <input type="hidden" id="gi-action-type" value="submit">
                  <input type="hidden" id="gi-edit-id" value="">
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                      <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 shadow-inner">
                          <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5" data-i18n="req_dept">Requestor / Dept</label>
                          <div class="font-black text-slate-700" id="disp-req-name">-</div>
                      </div>
                      <div>
                          <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="sec_req">Section Requestor</label>
                          <input type="text" id="gi-section" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" required data-i18n-ph="ph_sec_req" placeholder="Ex: Maintenance / Produksi">
                      </div>
                  </div>

                  <div class="mb-6">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="act_desc">Activities Description</label>
                      <textarea id="gi-purpose" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" rows="2" required data-i18n-ph="ph_act_desc" placeholder="Jelaskan aktivitas atau keperluan pengambilan barang..."></textarea>
                  </div>
                  
                  <div class="border-t border-slate-200 pt-5">
                      <div class="flex justify-between items-center mb-4">
                          <label class="text-sm font-black text-indigo-800 uppercase tracking-wide"><i class="fas fa-box-open mr-1.5"></i> <span data-i18n="item_list">Item List</span></label>
                          <button type="button" onclick="addGiRow()" class="bg-indigo-100 text-indigo-700 font-bold text-xs px-4 py-2 rounded-xl shadow-sm hover:bg-indigo-200 btn-animated"><i class="fas fa-plus"></i> <span data-i18n="add_row">Add Item Row</span></button>
                      </div>
                      
                      <div class="hidden sm:grid grid-cols-12 gap-3 mb-2 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">
                          <div class="col-span-3 text-left" data-i18n="th_it_name">Item No & Name</div>
                          <div class="col-span-2" data-i18n="curr_stk_short">Curr. Stock</div>
                          <div class="col-span-2" data-i18n="req_qty">Requested Qty</div>
                          <div class="col-span-1">UoM</div>
                          <div class="col-span-2" data-i18n="rsn_code">Reason Code</div>
                          <div class="col-span-2 text-rose-500 font-black" data-i18n="cost_ctr">Cost Center *</div>
                      </div>

                      <div id="gi-items-container" class="space-y-3 pb-24"></div>
                  </div>
              </form>
          </div>
          <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
              <button type="button" onclick="closeModal('modal-gi')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-i18n="btn_cancel">Cancel</button>
              <button type="button" onclick="submitGi()" id="btn-submit-gi" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700"><i class="fas fa-paper-plane mr-1.5"></i> <span data-i18n="btn_submit_form">Submit Form</span></button>
          </div>
      </div>
  </div>

  <div id="modal-gr" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-2 sm:p-4" onclick="closeAllDropdowns(event)">
      <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl flex flex-col max-h-[95vh] animate-slide-up overflow-hidden">
          <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center flex-none rounded-t-3xl">
              <h3 class="font-bold text-slate-800 tracking-tight"><i class="fas fa-truck-loading text-teal-600 mr-2 text-lg"></i> <span data-i18n="form_gr">Form Good Receive (GR)</span></h3>
              <button onclick="closeModal('modal-gr')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-xl"></i></button>
          </div>
          <div class="p-6 overflow-y-auto flex-1 custom-scroll" id="gr-scroll-container">
              <div class="mb-5 bg-teal-50 border border-teal-100 p-4 rounded-2xl text-xs text-teal-700 font-medium shadow-sm" data-i18n="info_gr">
                  <i class="fas fa-info-circle mr-1"></i> Input barang masuk. Data ini akan otomatis menambahkan stok di Master Inventory.
              </div>
              <form id="form-gr">
                  <div class="mb-6">
                      <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="rem_supp">Remarks / Supplier / PO Number</label>
                      <input type="text" id="gr-remarks" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none transition" required placeholder="Ex: PO-12345 / PT. Supplier Tbk">
                  </div>
                  
                  <div class="border-t border-slate-200 pt-5">
                      <div class="flex justify-between items-center mb-4">
                          <label class="text-sm font-black text-teal-800 uppercase tracking-wide"><i class="fas fa-boxes mr-1.5"></i> <span data-i18n="inc_items">Incoming Items</span></label>
                          <button type="button" onclick="addGrRow()" class="bg-teal-100 text-teal-700 font-bold text-xs px-4 py-2 rounded-xl shadow-sm hover:bg-teal-200 btn-animated"><i class="fas fa-plus"></i> <span data-i18n="add_row">Add Item Row</span></button>
                      </div>
                      
                      <div class="hidden sm:grid grid-cols-12 gap-3 mb-2 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">
                          <div class="col-span-5 text-left" data-i18n="th_it_name">Item No & Name</div>
                          <div class="col-span-2" data-i18n="curr_stk_short">Curr. Stock</div>
                          <div class="col-span-3" data-i18n="qty_recv">Qty Received</div>
                          <div class="col-span-2">UoM</div>
                      </div>

                      <div id="gr-items-container" class="space-y-3 pb-24"></div>
                  </div>
              </form>
          </div>
          <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 flex-none rounded-b-3xl">
              <button type="button" onclick="closeModal('modal-gr')" class="px-6 py-2.5 text-slate-600 border border-slate-300 hover:bg-slate-200 rounded-xl text-sm font-bold transition btn-animated" data-i18n="btn_cancel">Cancel</button>
              <button type="button" onclick="submitGr()" id="btn-submit-gr" class="px-8 py-2.5 bg-teal-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-teal-700"><i class="fas fa-save mr-1.5"></i> <span data-i18n="btn_save_stk">Save & Update Stock</span></button>
          </div>
      </div>
  </div>

  <div id="modal-item" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center">
              <h3 class="font-bold text-slate-800 tracking-tight" data-i18n="master_item">Master Item</h3>
              <button onclick="closeModal('modal-item')" class="text-slate-400 hover:text-red-500 transition"><i class="fas fa-times text-lg"></i></button>
          </div>
          <form onsubmit="event.preventDefault(); saveItem();" class="p-6">
              <input type="hidden" id="is-edit-mode" value="0">
              <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="it_code">Item No (Code)</label><input type="text" id="it-code" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 bg-slate-50 transition" required></div>
              <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="it_name">Item Name</label><input type="text" id="it-name" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required></div>
              <div class="mb-4"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="it_spec">Item Specification</label><input type="text" id="it-spec" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="-"></div>
              
              <div class="grid grid-cols-2 gap-4 mb-4">
                  <div><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="cat">Category</label><input type="text" id="it-cat" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" value="General"></div>
                  <div><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">UoM</label><input type="text" id="it-uom" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required placeholder="Pcs/Set"></div>
              </div>
              <div class="mb-6"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5" data-i18n="curr_stk">Current Stock</label><input type="number" id="it-stock" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500 transition" required></div>
              
              <button type="submit" class="w-full py-3.5 bg-emerald-600 text-white rounded-xl font-bold shadow-md hover:bg-emerald-700 btn-animated" data-i18n="btn_save_item">Save Item</button>
          </form>
      </div>
  </div>

  <div id="modal-reject" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl p-8 animate-slide-up text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-600 shadow-inner"><i class="fas fa-times-circle text-2xl"></i></div>
          <h3 class="font-black text-xl mb-2 text-slate-800 tracking-tight" data-i18n="rej_req">Reject Request</h3>
          <p class="text-xs text-slate-500 mb-6">Please provide a reason for rejecting this request.</p>
          <input type="hidden" id="rej-id">
          <textarea id="rej-reason" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm mb-6 outline-none focus:ring-2 focus:ring-red-500 transition" rows="3" data-i18n-ph="ph_rej" placeholder="Reason for rejection..." required></textarea>
          <div class="flex gap-3">
              <button onclick="closeModal('modal-reject')" class="flex-1 py-3 border-2 border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition btn-animated" data-i18n="btn_cancel">Cancel</button>
              <button onclick="executeReject()" class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold text-sm btn-animated shadow-md hover:bg-red-700" data-i18n="btn_conf_rej">Confirm Reject</button>
          </div>
      </div>
  </div>

  <div id="modal-users" class="hidden fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl flex flex-col h-[85vh] animate-slide-up overflow-hidden">
        <div class="bg-slate-800 px-6 py-5 flex justify-between items-center flex-none">
            <h3 class="font-bold text-white tracking-wide"><i class="fas fa-users-cog text-indigo-400 mr-2"></i> Manage Users & Permissions</h3>
            <button onclick="closeModal('modal-users')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-lg"></i></button>
        </div>
        <div class="flex flex-1 overflow-hidden">
            <div class="w-1/3 border-r border-slate-200 flex flex-col bg-slate-50">
                <div class="p-5 border-b border-slate-200">
                    <button onclick="resetUserForm()" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-bold text-xs mb-4 shadow-md hover:bg-indigo-700 btn-animated"><i class="fas fa-plus mr-1"></i> New User</button>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fas fa-search text-xs"></i></span>
                        <input type="text" id="search-user" onkeyup="filterUsers()" class="w-full border border-slate-300 rounded-xl p-2.5 pl-9 text-xs outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm transition" placeholder="Search user...">
                    </div>
                </div>
                <div id="user-list" class="flex-1 overflow-y-auto custom-scroll p-3 space-y-2"></div>
            </div>
            <div class="w-2/3 p-8 overflow-y-auto custom-scroll bg-white">
                <h4 class="font-black text-lg mb-6 text-slate-800 tracking-tight border-b pb-3" id="form-title">Create User</h4>
                <form id="user-form" onsubmit="event.preventDefault(); saveUser();">
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username</label><input type="text" id="u-user" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-slate-50 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password</label><input type="password" id="u-pass" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required placeholder="******"></div>
                        <div class="col-span-2"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Fullname</label><input type="text" id="u-name" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">NIK</label><input type="text" id="u-nik" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" placeholder="Employee ID"></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Department</label><input type="text" id="u-dept" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition" required></div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                            <select id="u-role" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 bg-white transition" onchange="handleRoleChange(this)">
                                <option value="User">User</option><option value="SectionHead">Section Head</option>
                                <option value="TeamLeader">Team Leader</option><option value="Warehouse">Warehouse</option>
                                <option value="PlantHead">Plant Head</option><option value="Administrator">Administrator</option>
                            </select>
                        </div>
                        <div class="col-span-1"><label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Phone (WA)</label><input type="text" id="u-phone" class="w-full border border-slate-300 rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-indigo-500 transition"></div>
                        
                        <div class="col-span-2 border-t border-slate-200 pt-5 mt-2">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3"><i class="fas fa-shield-alt text-indigo-500 mr-1"></i> Access Rights (Permissions)</label>
                            <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-gi-submit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="gi_submit"> Submit Good Issue</label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-gr-submit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="gr_submit"> Submit Good Receive</label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-item-add" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="item_add"> Add Item Master</label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-item-edit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="item_edit"> Edit Item Info</label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-stock-edit" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="stock_edit"> Edit/Adjust Stock</label>
                                <label class="flex items-center gap-2.5 text-slate-700 font-medium cursor-pointer"><input type="checkbox" id="chk-export-data" class="acc-chk w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500" value="export_data"> Export Data (PDF/Excel)</label>
                            </div>
                        </div>

                        <div class="col-span-2 mt-2 flex justify-end gap-3 pt-5 border-t border-slate-100">
                            <button type="button" id="btn-del-user" onclick="deleteUser()" class="hidden bg-red-100 text-red-600 px-5 py-2.5 rounded-xl text-sm font-bold mr-auto hover:bg-red-200 btn-animated"><i class="fas fa-trash"></i></button>
                            <button type="submit" class="bg-indigo-600 text-white px-8 py-2.5 rounded-xl shadow-md font-bold text-sm btn-animated hover:bg-indigo-700">Save User</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>

  <script>
    // System Core
    let currentUser = null;
    let inventoryData = [];
    let giData = [];
    let grData = [];
    let allUsers = [];
    let globalConfirmCallback = null;
    let activeGiFilter = 'All'; 
    let currentLang = localStorage.getItem('portal_lang') || 'en';

    // Image Upload Variables
    let videoStreamAct = null;
    let capturedActBase64 = null;
    let activeSourceAct = 'file';

    const i18n = {
        en: {
            app_desc: "Good Issue & Inventory System", forgot_pass: "Forgot Password?", login: "Login",
            tab_gi: "Good Issue (GI)", tab_gr: "Good Receive (GR)", tab_inv: "Inventory",
            stat_tot_gi: "Total Request", stat_pend: "Pending Approval", stat_comp: "Completed",
            stat_pend_head: "Pending Head", stat_pend_wh: "Pending WH", stat_pend_recv: "Pending Receive",
            hist_gi: "Good Issue History", click_filter_info: "Click statistics cards above to filter data.",
            ph_search_gi: "Search GI (ID/Name/Dept/Desc)...", btn_new_gi: "New GI Form",
            th_id: "NO GIF & Date", th_req: "Requestor Info", th_items: "Items & Activities Description", th_stat: "Status", th_act: "Action",
            hist_gr: "Good Receive History", desc_gr: "Log of incoming items to warehouse.", ph_search_gr: "Search GR (ID/Name/Supplier)...", btn_new_gr: "New GR Form",
            th_gr_id: "GR ID & Date", th_gr_by: "Received By", th_gr_rem: "Remarks / Supplier", th_gr_items: "Items Received",
            mast_inv: "Master Inventory", desc_inv: "Manage warehouse items and stock.", btn_add_item: "Add Item Master",
            th_it_code: "Item Code", th_it_name: "Item Name", th_it_spec: "Specification", th_it_cat: "Category", th_it_stock: "Stock / UoM",
            btn_ok: "OK", btn_cancel: "Cancel", btn_yes: "Yes, Proceed", reset_pass: "Reset Password",
            reset_info: "Reset link will be sent to your WhatsApp number.", btn_send_wa: "Send WhatsApp Link",
            my_profile: "My Profile", dept: "Department", fullname: "Fullname", wa_phone: "WhatsApp No.", editable: "(Editable)", new_pass: "New Password", pass_note: "(Leave blank if unchanged)", btn_update_prof: "Update Profile",
            form_gi: "Form Good Issue", req_dept: "Requestor / Dept", sec_req: "Section Requestor", ph_sec_req: "Ex: Maintenance / Production",
            act_desc: "Activities Description", ph_act_desc: "Explain activities or reason...", item_list: "Item List", add_row: "Add Item Row",
            curr_stk_short: "Curr. Stock", req_qty: "Requested Qty", rsn_code: "Reason Code", cost_ctr: "Cost Center *", btn_submit_form: "Submit Form",
            form_gr: "Form Good Receive (GR)", info_gr: "Input incoming items. Data will automatically update Master Inventory stock.", rem_supp: "Remarks / Supplier / PO Number", inc_items: "Incoming Items", qty_recv: "Qty Received", btn_save_stk: "Save & Update Stock",
            master_item: "Master Item", it_code: "Item No (Code)", it_name: "Item Name", it_spec: "Item Specification", cat: "Category", curr_stk: "Current Stock", btn_save_item: "Save Item",
            rej_req: "Reject Request", ph_rej: "Reason for rejection...", btn_conf_rej: "Confirm Reject",
            no_data: "No data found.", processing: "Processing...", btn_appr: "Approve", btn_rej: "Reject", btn_iss: "Issue Items",
            err_conn: "Connection Error.", err_req: "Please fill all required fields.", ph_search_item: "Search Item...", ph_search_inv: "Search Item (Code/Name)...",
            btn_export_data: "Export Report", export_data_title: "Export Report", export_type: "Data Type", start_date: "Start Date", end_date: "End Date",
            btn_export_items: "Export", btn_import_items: "Import", btn_template: "Template",
            btn_confirm_recv: "Confirm Receive", btn_cancel_req: "Cancel Request", status_cancelled: "Cancelled", err_cost_center: "Cost Center is required for all items."
        },
        id: {
            app_desc: "Sistem Pengeluaran & Inventaris", forgot_pass: "Lupa Password?", login: "Masuk",
            tab_gi: "Pengeluaran (GI)", tab_gr: "Penerimaan (GR)", tab_inv: "Inventaris",
            stat_tot_gi: "Total Permintaan", stat_pend: "Menunggu Persetujuan", stat_comp: "Selesai",
            stat_pend_head: "Menunggu Head", stat_pend_wh: "Menunggu Gudang", stat_pend_recv: "Menunggu Diterima",
            hist_gi: "Riwayat Pengeluaran (GI)", click_filter_info: "Klik kartu statistik di atas untuk memfilter data.", 
            ph_search_gi: "Cari GI (ID/Nama/Dept/Desk)...", btn_new_gi: "Form GI Baru",
            th_id: "NO GIF & Tanggal", th_req: "Info Pemohon", th_items: "Barang & Deskripsi Aktivitas", th_stat: "Status", th_act: "Aksi",
            hist_gr: "Riwayat Penerimaan (GR)", desc_gr: "Catatan barang masuk ke gudang.", ph_search_gr: "Cari GR (ID/Nama/Suplier)...", btn_new_gr: "Form GR Baru",
            th_gr_id: "ID GR & Tanggal", th_gr_by: "Diterima Oleh", th_gr_rem: "Catatan / Suplier", th_gr_items: "Barang Diterima",
            mast_inv: "Master Inventaris", desc_inv: "Kelola stok dan barang gudang.", btn_add_item: "Tambah Master Barang",
            th_it_code: "Kode Barang", th_it_name: "Nama Barang", th_it_spec: "Spesifikasi", th_it_cat: "Kategori", th_it_stock: "Stok / Satuan",
            btn_ok: "OK", btn_cancel: "Batal", btn_yes: "Ya, Lanjutkan", reset_pass: "Reset Kata Sandi",
            reset_info: "Tautan reset akan dikirim ke nomor WhatsApp Anda.", btn_send_wa: "Kirim Tautan WA",
            my_profile: "Profil Saya", dept: "Departemen", fullname: "Nama Lengkap", wa_phone: "No. WhatsApp", editable: "(Dapat Diubah)", new_pass: "Kata Sandi Baru", pass_note: "(Kosongkan jika tidak diubah)", btn_update_prof: "Perbarui Profil",
            form_gi: "Formulir Pengeluaran (GI)", req_dept: "Pemohon / Dept", sec_req: "Seksi Pemohon", ph_sec_req: "Cth: Maintenance / Produksi",
            act_desc: "Deskripsi Aktivitas", ph_act_desc: "Jelaskan aktivitas atau alasan...", item_list: "Daftar Barang", add_row: "Tambah Baris",
            curr_stk_short: "Sisa Stok", req_qty: "Jumlah Diminta", rsn_code: "Kode Alasan", cost_ctr: "Pusat Biaya *", btn_submit_form: "Kirim Formulir",
            form_gr: "Formulir Penerimaan (GR)", info_gr: "Input barang masuk. Data akan otomatis menambah stok Master Inventaris.", rem_supp: "Catatan / Suplier / No. PO", inc_items: "Barang Masuk", qty_recv: "Jml Diterima", btn_save_stk: "Simpan & Perbarui Stok",
            master_item: "Master Barang", it_code: "No/Kode Barang", it_name: "Nama Barang", it_spec: "Spesifikasi Barang", cat: "Kategori", curr_stk: "Stok Saat Ini", btn_save_item: "Simpan Barang",
            rej_req: "Tolak Permintaan", ph_rej: "Alasan penolakan...", btn_conf_rej: "Konfirmasi Tolak",
            no_data: "Tidak ada data.", processing: "Memproses...", btn_appr: "Setujui", btn_rej: "Tolak", btn_iss: "Keluarkan Barang",
            err_conn: "Koneksi Gagal.", err_req: "Harap isi semua kolom wajib.", ph_search_item: "Cari Barang...", ph_search_inv: "Cari Barang (Kode/Nama)...",
            btn_export_data: "Ekspor Laporan", export_data_title: "Ekspor Laporan", export_type: "Tipe Data", start_date: "Tanggal Mulai", end_date: "Tanggal Akhir",
            btn_export_items: "Ekspor", btn_import_items: "Impor", btn_template: "Template",
            btn_confirm_recv: "Konfirmasi Terima", btn_cancel_req: "Batalkan Permintaan", status_cancelled: "Dibatalkan", err_cost_center: "Cost Center wajib diisi untuk semua barang."
        }
    };

    const t = (key) => i18n[currentLang][key] || key;

    function applyLanguage() {
        document.getElementById('lang-label').innerText = currentLang.toUpperCase();
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const k = el.getAttribute('data-i18n');
            if(i18n[currentLang][k]) el.innerHTML = i18n[currentLang][k];
        });
        document.querySelectorAll('[data-i18n-ph]').forEach(el => {
            const k = el.getAttribute('data-i18n-ph');
            if(i18n[currentLang][k]) el.placeholder = i18n[currentLang][k];
        });
    }

    function toggleLanguage() {
        currentLang = (currentLang === 'en') ? 'id' : 'en';
        localStorage.setItem('portal_lang', currentLang);
        applyLanguage();
        if(giData.length>0) applyGiFilters();
        if(grData.length>0) renderGR(grData);
    }

    function logoutAction() {
        fetch('api/auth.php', { method:'POST', body:JSON.stringify({action:'logout'}) })
        .then(() => { localStorage.removeItem('portal_user'); window.location.reload(); })
        .catch(() => { localStorage.removeItem('portal_user'); window.location.reload(); });
    }

    function getMyRights() {
        if (!currentUser) return [];
        if (currentUser.role === 'Administrator') return ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data'];
        if (currentUser.access_rights) return JSON.parse(currentUser.access_rights);
        
        const isWH = currentUser.role === 'Warehouse' || (currentUser.role === 'TeamLeader' && currentUser.department.toLowerCase() === 'warehouse');
        if (isWH) return ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data'];
        
        return ['gi_submit'];
    }

    window.onload = () => {
        applyLanguage();
        
        fetch('api/auth.php', {method:'POST', body:JSON.stringify({action:'checkSession'})})
        .then(r => r.json()).then(res => {
            if(!res.success) {
                localStorage.removeItem('portal_user');
            } else {
                const stored = localStorage.getItem('portal_user');
                if(stored) {
                    currentUser = JSON.parse(stored);
                    document.getElementById('login-view').classList.add('hidden-important');
                    document.getElementById('app-view').classList.remove('hidden-important');
                    document.getElementById('nav-user').innerText = currentUser.fullname;
                    document.getElementById('nav-role').innerText = currentUser.role + " - " + currentUser.department;
                    
                    const rights = getMyRights();
                    const isAdmin = currentUser.role === 'Administrator';
                    
                    if(isAdmin || rights.includes('gi_submit')) {
                        const btnGi = document.getElementById('btn-create-gi');
                        if(btnGi) btnGi.classList.remove('hidden');
                    }
        
                    if(isAdmin || rights.includes('gr_submit')) {
                        document.getElementById('tab-gr').classList.remove('hidden');
                        document.getElementById('btn-create-gr')?.classList.remove('hidden');
                    }
                    if(isAdmin || rights.includes('item_add')) {
                        document.getElementById('btn-add-item').classList.remove('hidden');
                    }
                    if(isAdmin || rights.includes('item_edit') || rights.includes('stock_edit')) {
                        document.getElementById('th-inv-act').classList.remove('hidden');
                    }
                    if(isAdmin || rights.includes('export_data')) {
                        document.getElementById('btn-export-global').classList.remove('hidden');
                    }
        
                    if(isAdmin) {
                        document.getElementById('btn-admin-users').classList.remove('hidden');
                        document.getElementById('btn-tpl-item').classList.remove('hidden');
                        document.getElementById('btn-imp-item').classList.remove('hidden');
                        document.getElementById('btn-exp-item').classList.remove('hidden');
                    }
                    
                    switchTab(rights.includes('gi_submit') ? 'gi' : 'inv');
                    loadData();
                }
            }
        }).catch(err => { console.log(err); });
    };

    function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        if(id === 'modal-action-photo') stopCamera();
        if(id === 'modal-image-viewer') { setTimeout(()=> {document.getElementById('img-viewer-src').src = '';}, 300); }
    }
    
    function showCustomAlert(title, message) {
        document.getElementById('alert-custom-title').innerText = title;
        document.getElementById('alert-custom-msg').innerText = message;
        openModal('modal-alert-custom');
    }

    function showCustomConfirm(title, message, callback) {
        document.getElementById('confirm-custom-title').innerText = title;
        document.getElementById('confirm-custom-msg').innerText = message;
        globalConfirmCallback = callback;
        openModal('modal-confirm-custom');
    }

    function executeCustomConfirm() {
        if(globalConfirmCallback) globalConfirmCallback();
        closeModal('modal-confirm-custom');
        globalConfirmCallback = null;
    }

    function toggleLoginPass() {
        const p = document.getElementById('login-p');
        const icon = document.getElementById('icon-login-pass');
        if(p.type === 'password') { p.type = 'text'; icon.className = 'fas fa-eye-slash'; }
        else { p.type = 'password'; icon.className = 'fas fa-eye'; }
    }

    function handleLogin() {
        const u = document.getElementById('login-u').value;
        const p = document.getElementById('login-p').value;
        const btn = document.getElementById('btn-login'); 
        
        btn.disabled = true;
        btn.innerHTML = `<span class="loader-spin mr-2 border-t-white"></span> ${t('processing')}`;
        
        fetch('api/auth.php', { method:'POST', body:JSON.stringify({action:'login', username:u, password:p}) })
        .then(r=>r.json()).then(res => {
            btn.disabled = false;
            btn.innerText = t('login');
            if(res.success) { 
                localStorage.setItem('portal_user', JSON.stringify(res.user)); 
                window.location.reload(); 
            } else { 
                showCustomAlert("Error", res.message); 
            }
        }).catch(err => {
            btn.disabled = false;
            btn.innerText = t('login');
            showCustomAlert("Error", t('err_conn'));
        });
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
        
        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> ${t('processing')}`;

        fetch('api/users.php', {
            method: 'POST',
            body: JSON.stringify({ action: 'updateProfile', username: currentUser.username, phone: phone, newPass: pass })
        })
        .then(r => r.json()).then(res => {
            btn.disabled = false;
            btn.innerHTML = orgHtml;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) {
                showCustomAlert("Success", "Profil berhasil diperbarui.");
                currentUser.phone = phone; 
                localStorage.setItem('portal_user', JSON.stringify(currentUser));
                closeModal('modal-profile');
            } else { showCustomAlert("Error", res.message); }
        }).catch(e => {
            btn.disabled = false;
            btn.innerHTML = orgHtml;
            showCustomAlert("Error", t('err_conn'));
        });
    }

    function openForgotModal() { document.getElementById('forgot-username').value = ''; openModal('modal-forgot'); }

    function submitForgot() {
        const u = document.getElementById('forgot-username').value;
        if(!u) return showCustomAlert("Info", "Silakan masukkan username");
        
        const btn = document.getElementById('btn-forgot');
        const originalText = btn.innerText;
        btn.disabled = true; 
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${t('processing')}`;

        fetch('api/auth.php', {
            method: 'POST', 
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'requestReset', username: u })
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false; btn.innerText = originalText;
            if(res.success) { showCustomAlert("Success", res.message); closeModal('modal-forgot'); } 
            else { showCustomAlert("Error", res.message); }
        })
        .catch(err => { btn.disabled = false; btn.innerText = originalText; showCustomAlert("Error", t('err_conn')); });
    }

    function switchTab(tab) {
        document.getElementById('view-gi').classList.add('hidden');
        document.getElementById('view-gr').classList.add('hidden');
        document.getElementById('view-inv').classList.add('hidden');
        
        if(document.getElementById('gi-card-container')) document.getElementById('gi-card-container').classList.add('hidden');
        if(document.getElementById('gr-card-container')) document.getElementById('gr-card-container').classList.add('hidden');
        if(document.getElementById('inv-card-container')) document.getElementById('inv-card-container').classList.add('hidden');

        document.getElementById('tab-gi').className = "px-5 py-2.5 text-sm tab-inactive transition-colors whitespace-nowrap flex items-center";
        document.getElementById('tab-gr').className = "px-5 py-2.5 text-sm tab-inactive transition-colors whitespace-nowrap flex items-center";
        document.getElementById('tab-inv').className = "px-5 py-2.5 text-sm tab-inactive transition-colors whitespace-nowrap flex items-center";
        
        const rights = getMyRights();
        const isAdmin = currentUser.role === 'Administrator';

        if(!isAdmin && !rights.includes('gr_submit')) { document.getElementById('tab-gr').classList.add('hidden-important'); }
        else { document.getElementById('tab-gr').classList.remove('hidden-important'); }

        document.getElementById(`view-${tab}`).classList.remove('hidden');
        
        if(document.getElementById(`${tab}-card-container`)) {
            document.getElementById(`${tab}-card-container`).classList.remove('hidden');
        }

        document.getElementById(`tab-${tab}`).className = "px-5 py-2.5 text-sm tab-active transition-colors whitespace-nowrap flex items-center border-b-2 border-indigo-600";
    }

    function loadData() {
        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getInventory'})})
        .then(r => r.json())
        .then(d => { 
            if(d && d.code === 401) { logoutAction(); return; }
            inventoryData = Array.isArray(d) ? d : (d.data || []); 
            renderInventory(inventoryData); 
        })
        .catch(e => { console.error(e); renderInventory([]); });

        fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getRequests', role:currentUser.role, department:currentUser.department, username:currentUser.username})})
        .then(r => r.json())
        .then(d => { 
            if(d && d.code === 401) { logoutAction(); return; }
            giData = Array.isArray(d) ? d : (d.data || []); 
            applyGiFilters(); 
        })
        .catch(e => { console.error(e); applyGiFilters(); });
        
        const rights = getMyRights();
        if(currentUser.role === 'Administrator' || rights.includes('gr_submit')) {
            fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'getReceives', role:currentUser.role})})
            .then(r => r.json())
            .then(d => { 
                if(d && d.code === 401) { logoutAction(); return; }
                grData = Array.isArray(d) ? d : (d.data || []); 
                renderGR(grData); 
            })
            .catch(e => { console.error(e); renderGR([]); });
        }
    }

    function openExportModal() {
        document.getElementById('exp-type').value = 'GI';
        document.getElementById('exp-start').value = '';
        document.getElementById('exp-end').value = '';
        toggleExpDates();
        openModal('modal-export');
    }

    function toggleExpDates() {
        const type = document.getElementById('exp-type').value;
        const dateGroup = document.getElementById('exp-date-group');
        if(type === 'INV') {
            dateGroup.classList.add('hidden');
        } else {
            dateGroup.classList.remove('hidden');
        }
    }

    function processExport(format) {
        const type = document.getElementById('exp-type').value;
        let start = document.getElementById('exp-start').value;
        let end = document.getElementById('exp-end').value;

        if (type !== 'INV' && (!start || !end)) {
            showCustomAlert("Warning", "Silakan lengkapi tanggal mulai dan tanggal akhir.");
            return;
        }

        document.getElementById('exp-loading').classList.remove('hidden');

        const p = { action: 'exportData', role: currentUser.role, username: currentUser.username, export_type: type, start_date: start, end_date: end };

        fetch('api/gis.php', { method: 'POST', body: JSON.stringify(p) })
        .then(r => r.json())
        .then(res => {
            document.getElementById('exp-loading').classList.add('hidden');
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) {
                if(!res.data || res.data.length === 0) {
                    showCustomAlert("Info", "Tidak ada data pada rentang waktu yang dipilih.");
                    return;
                }
                if (format === 'excel') generateExcel(res.data, type);
                else generatePdf(res.data, type);
                closeModal('modal-export');
            } else {
                showCustomAlert("Error", res.message);
            }
        }).catch(err => {
            document.getElementById('exp-loading').classList.add('hidden');
            showCustomAlert("Error", t('err_conn'));
        });
    }

    function generateExcel(data, type) {
        const wb = XLSX.utils.book_new();
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        let rows = [];
        
        rows.push([`AUDIT REPORT - ${type === 'GI' ? 'GOOD ISSUE' : (type === 'GR' ? 'GOOD RECEIVE' : 'MASTER INVENTORY')}`]);
        rows.push([`Generated By: ${currentUser.fullname} (${currentUser.role})`]);
        rows.push([`Date: ${new Date().toLocaleString('id-ID')}`]);
        rows.push([]);

        if (type === 'GI') {
            rows.push(["ID Request", "Tanggal", "Nama", "Departemen", "Seksi", "Deskripsi Aktivitas", "Kode Barang", "Nama Barang", "Qty", "UoM", "Reason Code", "Cost Center", "Status L1", "Status WH", "Foto Issued", "Diterima Oleh", "Tanggal Terima", "Foto Received"]);
            data.forEach(r => {
                const fIssue = (r.issue_photo && r.issue_photo !== '0') ? baseUrl + r.issue_photo : '-';
                const fRecv = (r.receive_photo && r.receive_photo !== '0') ? baseUrl + r.receive_photo : '-';
                r.items.forEach(i => {
                    rows.push([r.req_id, r.created_at, r.fullname, r.department, r.section, r.purpose, i.code, i.name, parseInt(i.qty), i.uom, i.reason_code||'-', i.cost_center||'-', r.app_head, r.app_wh, fIssue, r.received_by||'-', r.receive_time||'-', fRecv]);
                });
            });
        } else if (type === 'GR') {
            rows.push(["ID Receive", "Tanggal", "Diterima Oleh", "Remarks / Supplier", "Kode Barang", "Nama Barang", "Qty Masuk", "UoM"]);
            data.forEach(r => {
                r.items.forEach(i => {
                    rows.push([r.gr_id, r.created_at, r.fullname, r.remarks, i.code, i.name, parseInt(i.qty), i.uom]);
                });
            });
        } else if (type === 'INV') {
            rows.push(["Kode Barang", "Nama Barang", "Spesifikasi", "Kategori", "UoM", "Stok Terkini", "Terakhir Update"]);
            data.forEach(r => {
                rows.push([r.item_code, r.item_name, r.item_spec||'-', r.category, r.uom, parseInt(r.stock), r.last_updated]);
            });
        }

        const ws = XLSX.utils.aoa_to_sheet(rows);
        XLSX.utils.book_append_sheet(wb, ws, "Report");
        XLSX.writeFile(wb, `GIS_Report_${type}_${new Date().getTime()}.xlsx`);
    }

    function generatePdf(data, type) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape', 'mm', 'a4');
        
        doc.setFontSize(14);
        doc.text(`Audit Report - ${type === 'GI' ? 'Good Issue' : (type === 'GR' ? 'Good Receive' : 'Master Inventory')}`, 14, 15);
        doc.setFontSize(9);
        doc.text(`Generated By: ${currentUser.fullname} (${currentUser.role}) | Date: ${new Date().toLocaleString('id-ID')}`, 14, 21);

        let head = [];
        let body = [];

        if (type === 'GI') {
            head = [['Req ID/Date', 'Requestor/Sec', 'Description', 'Item Code & Name', 'Qty/UoM', 'RC / CC', 'App/Rcv Status']];
            data.forEach(r => {
                r.items.forEach(i => {
                    let st = `L1: ${r.app_head}\nWH: ${r.app_wh}`;
                    if(r.received_by) st += `\nRCV: ${r.received_by}\n${r.receive_time}`;
                    body.push([
                        `${r.req_id}\n${r.created_at}`,
                        `${r.fullname}\n(${r.department} / ${r.section})`,
                        r.purpose,
                        `${i.code}\n${i.name}`,
                        `${i.qty} ${i.uom}`,
                        `${i.reason_code||'-'} / ${i.cost_center||'-'}`,
                        st
                    ]);
                });
            });
        } else if (type === 'GR') {
            head = [['GR ID / Date', 'Received By', 'Supplier/Remarks', 'Item Code & Name', 'Qty', 'UoM']];
            data.forEach(r => {
                r.items.forEach(i => {
                    body.push([
                        `${r.gr_id}\n${r.created_at}`,
                        r.fullname,
                        r.remarks,
                        `${i.code}\n${i.name}`,
                        i.qty,
                        i.uom
                    ]);
                });
            });
        } else if (type === 'INV') {
            head = [['Item Code', 'Item Name', 'Specification', 'Category', 'UoM', 'Stock']];
            data.forEach(r => {
                body.push([
                    r.item_code, r.item_name, r.item_spec||'-', r.category, r.uom, r.stock
                ]);
            });
        }

        doc.autoTable({
            startY: 26,
            head: head,
            body: body,
            theme: 'grid',
            styles: { fontSize: 7, cellPadding: 2, overflow: 'linebreak' },
            headStyles: { fillColor: [79, 70, 229] }
        });

        doc.save(`GIS_Report_${type}_${new Date().getTime()}.pdf`);
    }

    function viewPhoto(url) {
        if (!url || url === 'null' || url === 'undefined' || url.trim() === '' || url === '0') {
            showCustomAlert(t('info'), "Tidak ada bukti foto.");
            return;
        }
        const viewer = document.getElementById('img-viewer-src');
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '/');
        viewer.src = baseUrl + url.trim() + '?t=' + new Date().getTime();
        openModal('modal-image-viewer');
    }

    function showDropdown(inp, type) {
        document.querySelectorAll('.dropdown-list').forEach(e => e.classList.add('hidden'));
        const list = inp.parentElement.querySelector('.dropdown-list');
        list.classList.remove('hidden');
        renderDropdownItems(inp, type);
    }

    function filterDropdown(inp, type) { renderDropdownItems(inp, type); }

    function renderDropdownItems(inp, type) {
        const list = inp.parentElement.querySelector('.dropdown-list');
        const f = inp.value.toLowerCase();
        
        const filtered = inventoryData.filter(i => 
            (i.item_code && i.item_code.toLowerCase().includes(f)) || 
            (i.item_name && i.item_name.toLowerCase().includes(f)) ||
            (i.item_spec && i.item_spec.toLowerCase().includes(f))
        ).slice(0, 50);

        if(filtered.length === 0) {
            list.innerHTML = `<div class="p-2 text-xs text-slate-500 italic">Tidak ada hasil</div>`;
            return;
        }

        let htmlArray = [];
        filtered.forEach(i => {
            const safeCode = (i.item_code||'').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const safeName = (i.item_name||'').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const safeSpec = (i.item_spec||'').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            let specDisplay = i.item_spec ? ` - ${i.item_spec}` : '';
            
            htmlArray.push(`<div class="p-2 hover:bg-indigo-50 cursor-pointer text-xs border-b border-slate-50 transition-colors" onmousedown="selectItemOption(this, '${safeCode}', '${safeName}', '${safeSpec}', '${i.stock}', '${i.uom}', '${type}')">
                <div class="font-bold text-indigo-700">${i.item_code}</div>
                <div class="text-slate-600">${i.item_name}${specDisplay} (Stk: ${i.stock})</div>
            </div>`);
        });
        list.innerHTML = htmlArray.join('');
    }

    function selectItemOption(el, code, name, spec, stock, uom, type) {
        const row = el.closest(`div[id^="${type}-row-"]`);
        let displayVal = code + ' - ' + name;
        if(spec) displayVal += ' (' + spec + ')';
        
        row.querySelector(`.${type}-item-display`).value = displayVal;
        row.querySelector(`.${type}-item-code`).value = code;
        row.querySelector(`.${type}-item-name`).value = spec ? name + ' - ' + spec : name; 
        row.querySelector(`.${type}-uom`).value = uom;
        
        const stockInput = row.querySelector(`.${type}-stock`);
        if(stockInput) stockInput.value = stock;
        
        if(type === 'gi') {
            const qtyInput = row.querySelector(`.${type}-qty`);
            qtyInput.max = stock;
            qtyInput.title = "Max stock: " + stock;
        }
        el.closest('.dropdown-list').classList.add('hidden');
    }

    document.addEventListener('click', function(e) {
        if(!e.target.closest('.relative.w-full')) { document.querySelectorAll('.dropdown-list').forEach(el => el.classList.add('hidden')); }
    });

    function filterInventory() {
        const term = document.getElementById('search-inv').value.toLowerCase();
        const filtered = inventoryData.filter(r => 
            (r.item_code && r.item_code.toLowerCase().includes(term)) || 
            (r.item_name && r.item_name.toLowerCase().includes(term)) || 
            (r.item_spec && r.item_spec.toLowerCase().includes(term)) ||
            (r.category && r.category.toLowerCase().includes(term))
        );
        renderInventory(filtered);
    }

    function renderInventory(data = inventoryData) {
        const tb = document.getElementById('inv-table-body'); 
        const cardContainer = document.getElementById('inv-card-container');
        
        const rights = getMyRights();
        const isAdmin = currentUser.role === 'Administrator';
        const canEditInfo = isAdmin || rights.includes('item_edit');
        const canEditStock = isAdmin || rights.includes('stock_edit');
        const canEditAny = canEditInfo || canEditStock;

        if (!data || data.length === 0) {
            tb.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</td></tr>`;
            cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</div>`;
            return;
        }

        const displayData = data.slice(0, 300);
        let htmlArrayTable = [];
        let htmlArrayCard = [];

        displayData.forEach(r => {
            let actTable = canEditAny ? `<td class="px-6 py-4 text-right"><button onclick="openEditItem('${(r.item_code||'').replace(/'/g, "\\'")}','${(r.item_name||'').replace(/'/g, "\\'")}','${(r.item_spec||'').replace(/'/g, "\\'")}','${(r.category||'').replace(/'/g, "\\'")}','${(r.uom||'').replace(/'/g, "\\'")}','${r.stock}')" class="text-blue-600 hover:text-blue-800 bg-blue-50 p-2 rounded-lg shadow-sm transition btn-animated"><i class="fas fa-edit"></i></button></td>` : `<td class="px-6 py-4 text-right hidden" id="th-inv-act"></td>`;
            htmlArrayTable.push(`<tr class="hover:bg-slate-50 border-b border-slate-100 transition-colors duration-200">
                <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">${r.item_code}</td>
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-700">${r.item_name}</div>
                    <div class="text-[10px] text-slate-500 italic mt-0.5">${r.item_spec || '-'}</div>
                </td>
                <td class="px-6 py-4 text-xs text-slate-500"><span class="bg-slate-100 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase border border-slate-200">${r.category}</span></td>
                <td class="px-6 py-4 text-center"><span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm">${r.stock} <span class="font-normal text-[10px] ml-1">${r.uom}</span></span></td>
                ${actTable}
            </tr>`);

            let actCard = canEditAny ? `<button onclick="openEditItem('${(r.item_code||'').replace(/'/g, "\\'")}','${(r.item_name||'').replace(/'/g, "\\'")}','${(r.item_spec||'').replace(/'/g, "\\'")}','${(r.category||'').replace(/'/g, "\\'")}','${(r.uom||'').replace(/'/g, "\\'")}','${r.stock}')" class="text-blue-600 hover:text-white hover:bg-blue-600 bg-blue-50 p-2.5 rounded-xl shadow-sm transition btn-animated"><i class="fas fa-edit"></i></button>` : ``;
            htmlArrayCard.push(`
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-1 transition-all hover:shadow-md">
                <div class="flex justify-between items-start mb-3">
                    <div class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">${r.item_code}</div>
                    <span class="bg-slate-100 px-2 py-1 rounded-md text-[9px] font-bold uppercase border border-slate-200 text-slate-600">${r.category}</span>
                </div>
                <div class="mb-4">
                    <div class="font-bold text-sm text-slate-800">${r.item_name}</div>
                    <div class="text-xs text-slate-500 italic mt-1 leading-snug">${r.item_spec || '-'}</div>
                </div>
                <div class="flex justify-between items-center border-t border-slate-100 pt-4">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase font-bold block mb-1">Current Stock</span>
                        <span class="bg-indigo-50 text-indigo-700 font-black px-3 py-1.5 rounded-lg border border-indigo-200 shadow-sm text-sm">${r.stock} <span class="font-normal text-[10px] ml-1">${r.uom}</span></span>
                    </div>
                    ${actCard}
                </div>
            </div>`);
        });

        if(data.length > 300) {
            htmlArrayTable.push(`<tr><td colspan="5" class="text-center py-4 text-slate-400 text-xs italic">Menampilkan 300 data pertama. Gunakan pencarian untuk hasil lebih spesifik.</td></tr>`);
            htmlArrayCard.push(`<div class="text-center py-4 text-slate-400 text-xs italic">Menampilkan 300 data pertama. Gunakan pencarian.</div>`);
        }

        tb.innerHTML = htmlArrayTable.join('');
        cardContainer.innerHTML = htmlArrayCard.join('');
    }

    function openItemModal() {
        document.getElementById('it-code').value = ''; document.getElementById('it-code').disabled = false;
        document.getElementById('it-name').value = ''; document.getElementById('it-name').disabled = false;
        document.getElementById('it-spec').value = ''; document.getElementById('it-spec').disabled = false;
        document.getElementById('it-cat').value = 'General'; document.getElementById('it-cat').disabled = false;
        document.getElementById('it-uom').value = ''; document.getElementById('it-uom').disabled = false;
        document.getElementById('it-stock').value = ''; document.getElementById('it-stock').disabled = false;
        document.getElementById('is-edit-mode').value = '0';
        openModal('modal-item');
    }
    
    function openEditItem(c, n, spec, cat, u, s) {
        const rights = getMyRights();
        const isAdmin = currentUser.role === 'Administrator';
        const canEditInfo = isAdmin || rights.includes('item_edit');
        const canEditStock = isAdmin || rights.includes('stock_edit');

        document.getElementById('it-code').value = c; document.getElementById('it-code').disabled = true;
        document.getElementById('it-name').value = n; document.getElementById('it-name').disabled = !canEditInfo;
        document.getElementById('it-spec').value = spec; document.getElementById('it-spec').disabled = !canEditInfo;
        document.getElementById('it-cat').value = cat; document.getElementById('it-cat').disabled = !canEditInfo;
        document.getElementById('it-uom').value = u; document.getElementById('it-uom').disabled = !canEditInfo;
        document.getElementById('it-stock').value = s; document.getElementById('it-stock').disabled = !canEditStock;
        document.getElementById('is-edit-mode').value = '1';
        openModal('modal-item');
    }

    function saveItem() {
        const p = { 
            action: 'saveItem', role: currentUser.role, department: currentUser.department, username: currentUser.username,
            is_edit: document.getElementById('is-edit-mode').value, item_code: document.getElementById('it-code').value, 
            item_name: document.getElementById('it-name').value, item_spec: document.getElementById('it-spec').value, 
            category: document.getElementById('it-cat').value, uom: document.getElementById('it-uom').value, stock: document.getElementById('it-stock').value 
        };
        fetch('api/gis.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
            if(res.code === 401) { logoutAction(); return; }
            if(res.success){ closeModal('modal-item'); loadData(); showCustomAlert("Success", "Item Saved."); } else showCustomAlert("Error", res.message); 
        });
    }

    function downloadItemTemplate() {
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet([
            ["Item_Code", "Item_Name", "Item_Specification", "Category", "UoM", "Stock"],
            ["ITM-001", "Bearing SKF 6205", "High Speed Bearing", "Sparepart", "Pcs", 50]
        ]);
        XLSX.utils.book_append_sheet(wb, ws, "Template_Items");
        XLSX.writeFile(wb, "Template_Master_Items.xlsx");
    }

    function exportItems() {
        if(inventoryData.length === 0) return showCustomAlert("Info", "No inventory data to export.");
        const wb = XLSX.utils.book_new();
        const rows = [["Item_Code", "Item_Name", "Item_Specification", "Category", "UoM", "Stock", "Last_Updated"]];
        inventoryData.forEach(i => { rows.push([i.item_code, i.item_name, i.item_spec, i.category, i.uom, parseInt(i.stock), i.last_updated]); });
        const ws = XLSX.utils.aoa_to_sheet(rows);
        XLSX.utils.book_append_sheet(wb, ws, "Master_Items");
        XLSX.writeFile(wb, "GIS_Master_Items_" + new Date().getTime() + ".xlsx");
    }

    function handleImportItems(e) {
        const file = e.target.files[0];
        if(!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            try {
                const data = new Uint8Array(evt.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
                const formatted = json.map(r => ({
                    item_code: String(r.Item_Code || r.item_code || ''), item_name: String(r.Item_Name || r.item_name || ''),
                    item_spec: String(r.Item_Specification || r.item_specification || r.item_spec || ''),
                    category: String(r.Category || r.category || 'General'), uom: String(r.UoM || r.uom || 'Pcs'), stock: parseInt(r.Stock || r.stock || 0)
                })).filter(r => r.item_code && r.item_name);

                if(formatted.length === 0) { document.getElementById('import-item-file').value = ''; return showCustomAlert("Error", "Format tidak valid atau data kosong."); }

                fetch('api/gis.php', { method: 'POST', body: JSON.stringify({ action: 'importItems', role: currentUser.role, data: formatted }) })
                .then(r=>r.json()).then(res => {
                    document.getElementById('import-item-file').value = '';
                    if(res.code === 401) { logoutAction(); return; }
                    if(res.success) { showCustomAlert("Success", res.message); loadData(); } else { showCustomAlert("Error", res.message); }
                });
            } catch(err) { document.getElementById('import-item-file').value = ''; showCustomAlert("Error", "Gagal parsing file Excel."); }
        };
        reader.readAsArrayBuffer(file);
    }

    function applyGiFilters() {
        const term = document.getElementById('search-gi').value.toLowerCase();
        let filtered = giData.filter(r => 
            (r.req_id || '').toLowerCase().includes(term) || 
            (r.fullname || '').toLowerCase().includes(term) || 
            (r.department || '').toLowerCase().includes(term) || 
            (r.purpose || '').toLowerCase().includes(term) || 
            (r.status || '').toLowerCase().includes(term)
        );
        
        if(activeGiFilter === 'Completed') {
            filtered = filtered.filter(r => r.status === 'Completed' || r.status === 'Rejected' || r.status === 'Cancelled');
        } else if (activeGiFilter !== 'All') {
            filtered = filtered.filter(r => r.status === activeGiFilter);
        }
        renderGI(filtered);
    }

    function renderGI(data) {
        const tb = document.getElementById('gi-table-body'); 
        const cardContainer = document.getElementById('gi-card-container');
        const formatDt = (dtStr) => {
            if(!dtStr || dtStr === '0000-00-00 00:00:00' || dtStr === '-') return '-';
            const d = new Date(dtStr);
            if(isNaN(d)) return dtStr;
            return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
        };

        let countHead = 0, countWh = 0, countRecv = 0, countDone = 0;
        giData.forEach(r => {
            if(r.status === 'Pending Head') countHead++;
            if(r.status === 'Pending Warehouse') countWh++;
            if(r.status === 'Pending Receive') countRecv++;
            if(r.status === 'Completed' || r.status === 'Rejected' || r.status === 'Cancelled') countDone++;
        });
        if(document.getElementById('stat-total')) document.getElementById('stat-total').innerText = giData.length;
        if(document.getElementById('stat-pending-head')) document.getElementById('stat-pending-head').innerText = countHead;
        if(document.getElementById('stat-pending-wh')) document.getElementById('stat-pending-wh').innerText = countWh;
        if(document.getElementById('stat-pending-recv')) document.getElementById('stat-pending-recv').innerText = countRecv;
        if(document.getElementById('stat-done')) document.getElementById('stat-done').innerText = countDone;

        if(!data || data.length === 0) { 
            tb.innerHTML = `<tr><td colspan="5" class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</td></tr>`; 
            cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</div>`;
            return;
        }

        let htmlArrayTable = [];
        let htmlArrayCard = [];

        data.forEach(r => {
            let sColor = 'bg-amber-100 text-amber-800 border-amber-200'; 
            if(r.status==='Completed') sColor='bg-emerald-100 text-emerald-800 border-emerald-200'; 
            if(r.status==='Rejected') sColor='bg-red-100 text-red-800 border-red-200';
            if(r.status==='Cancelled') sColor='bg-slate-200 text-slate-600 border-slate-300';
            if(r.status==='Pending Receive') sColor='bg-blue-100 text-blue-800 border-blue-200 animate-pulse';
            
            let appHeadStr = r.app_head || 'Pending';
            let appWhStr = r.app_wh || 'Pending';

            let l1Color = appHeadStr.includes('Approved') ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (appHeadStr.includes('Rejected') ? 'text-red-700 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200');
            let l1Icon = appHeadStr.includes('Approved') ? 'fa-check-circle' : (appHeadStr.includes('Rejected') ? 'fa-times-circle' : 'fa-clock');

            let whColor = appWhStr.includes('Issued') ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : (appWhStr.includes('Rejected') ? 'text-red-700 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200');
            let whIcon = appWhStr.includes('Issued') ? 'fa-check-circle' : (appWhStr.includes('Rejected') ? 'fa-times-circle' : 'fa-clock');
            
            let rcColor = r.received_by ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-slate-400 bg-slate-50 border-slate-200';
            let rcIcon = r.received_by ? 'fa-check-double' : 'fa-hourglass-start';
            if (r.status === 'Pending Receive') { rcColor = 'text-blue-600 bg-blue-50 border-blue-200'; rcIcon = 'fa-box-open'; }
            if (r.status === 'Rejected' || r.status === 'Cancelled') { rcColor = 'text-slate-300 bg-slate-50 border-slate-100'; rcIcon = 'fa-minus'; l1Color = 'text-slate-300 bg-slate-50 border-slate-100'; whColor = 'text-slate-300 bg-slate-50 border-slate-100'; }

            let transStatus = r.status;
            if(currentLang === 'id') {
                if(r.status === 'Completed') transStatus = 'Selesai';
                if(r.status === 'Rejected') transStatus = 'Ditolak';
                if(r.status === 'Cancelled') transStatus = 'Dibatalkan';
                if(r.status === 'Pending Head') transStatus = 'Menunggu Head';
                if(r.status === 'Pending Warehouse') transStatus = 'Menunggu Gudang';
                if(r.status === 'Pending Receive') transStatus = 'Menunggu Diterima';
            }

            const l1Time = formatDt(r.head_time);
            const whTime = formatDt(r.wh_time);
            const rcTime = formatDt(r.receive_time);

            const statusHTMLTable = `
                <div class="flex flex-col items-center w-[180px] mx-auto">
                    <span class="status-badge border shadow-sm ${sColor} mb-3 w-full text-center py-2 px-2 leading-snug flex items-center justify-center min-h-[36px]">${transStatus}</span>
                    <div class="w-full flex flex-col gap-2 text-[9px] text-left">
                        <div class="flex flex-col border p-2.5 rounded-xl ${l1Color} shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5">
                                <span class="font-black uppercase opacity-75 tracking-wider"><i class="fas ${l1Icon} mr-1"></i> Dept Head</span>
                            </div>
                            <div class="font-semibold truncate mb-1" title="${appHeadStr}">${appHeadStr.replace('Approved by ', '').replace('Rejected by ', '')}</div>
                            ${r.head_time && r.status !== 'Cancelled' ? `<div class="text-[8px] opacity-80 font-mono flex items-center gap-1"><i class="far fa-clock"></i> ${l1Time}</div>` : ''}
                        </div>
                        <div class="flex flex-col border p-2.5 rounded-xl ${whColor} shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5">
                                <span class="font-black uppercase opacity-75 tracking-wider"><i class="fas ${whIcon} mr-1"></i> Warehouse</span>
                                ${r.issue_photo && r.issue_photo !== '0' ? `<button onclick="viewPhoto('${r.issue_photo}')" class="text-blue-600 bg-white border border-blue-200 px-1.5 py-0.5 rounded shadow-sm hover:bg-blue-50 transition"><i class="fas fa-camera"></i></button>` : ''}
                            </div>
                            <div class="font-semibold truncate mb-1" title="${appWhStr}">${appWhStr.replace('Issued by ', '').replace('Rejected by ', '')}</div>
                            ${r.wh_time && r.status !== 'Cancelled' ? `<div class="text-[8px] opacity-80 font-mono flex items-center gap-1"><i class="far fa-clock"></i> ${whTime}</div>` : ''}
                        </div>
                        <div class="flex flex-col border p-2.5 rounded-xl ${rcColor} shadow-sm transition-all hover:shadow-md hover:-translate-y-0.5">
                            <div class="flex justify-between items-center mb-1.5 border-b border-black/5 pb-1.5">
                                <span class="font-black uppercase opacity-75 tracking-wider"><i class="fas ${rcIcon} mr-1"></i> User Rcv</span>
                                ${r.receive_photo && r.receive_photo !== '0' ? `<button onclick="viewPhoto('${r.receive_photo}')" class="text-emerald-600 bg-white border border-emerald-200 px-1.5 py-0.5 rounded shadow-sm hover:bg-emerald-50 transition"><i class="fas fa-camera"></i></button>` : ''}
                            </div>
                            <div class="font-semibold truncate mb-1" title="${r.received_by || '-'}">${r.received_by ? r.received_by : (r.status === 'Pending Receive' ? 'Waiting...' : '-')}</div>
                            ${r.receive_time && r.status !== 'Cancelled' ? `<div class="text-[8px] opacity-80 font-mono flex items-center gap-1"><i class="far fa-clock"></i> ${rcTime}</div>` : ''}
                        </div>
                    </div>
                </div>
            `;

            const statusHTMLCard = `
                <span class="status-badge border shadow-sm ${sColor} px-3 py-1.5 mb-4 inline-block w-full text-center">${transStatus}</span>
                <div class="grid grid-cols-3 gap-2 text-[9px] mb-2">
                    <div class="flex flex-col border p-2 rounded-xl ${l1Color} shadow-sm justify-between">
                        <div class="font-black uppercase opacity-75 mb-1"><i class="fas ${l1Icon}"></i> L1</div>
                        <div class="font-semibold truncate mb-1" title="${appHeadStr}">${appHeadStr.replace('Approved by ', '').replace('Rejected by ', '')}</div>
                    </div>
                    <div class="flex flex-col border p-2 rounded-xl ${whColor} shadow-sm justify-between">
                        <div class="font-black uppercase opacity-75 mb-1"><i class="fas ${whIcon}"></i> WH ${r.issue_photo && r.issue_photo !== '0' ? `<i class="fas fa-camera float-right text-blue-500 bg-white rounded p-0.5" onclick="viewPhoto('${r.issue_photo}')"></i>` : ''}</div>
                        <div class="font-semibold truncate mb-1" title="${appWhStr}">${appWhStr.replace('Issued by ', '').replace('Rejected by ', '')}</div>
                    </div>
                    <div class="flex flex-col border p-2 rounded-xl ${rcColor} shadow-sm justify-between">
                        <div class="font-black uppercase opacity-75 mb-1"><i class="fas ${rcIcon}"></i> RCV ${r.receive_photo && r.receive_photo !== '0' ? `<i class="fas fa-camera float-right text-emerald-500 bg-white rounded p-0.5" onclick="viewPhoto('${r.receive_photo}')"></i>` : ''}</div>
                        <div class="font-semibold truncate mb-1" title="${r.received_by || '-'}">${r.received_by ? r.received_by : (r.status === 'Pending Receive' ? 'Wait...' : '-')}</div>
                    </div>
                </div>
            `;

            let itemsHtmlTable = '<div class="flex flex-col gap-2 mt-2 pt-2 border-t border-slate-100">';
            let itemsHtmlCard = '<div class="flex flex-col gap-2 mt-2">';
            let itemsArr = r.items || [];
            
            itemsArr.forEach(i => {
                const itemBlock = `
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col hover:border-indigo-300 transition-colors">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[11px] font-bold text-indigo-700 pr-2 leading-snug w-full break-words whitespace-normal" title="${i.name}">${i.code} - ${i.name}</span>
                        <span class="text-xs font-black text-slate-800 bg-slate-100 px-2.5 py-1 rounded-lg shadow-inner border border-slate-200 whitespace-nowrap ml-2">${i.qty} <span class="text-[9px] font-bold text-slate-500">${i.uom}</span></span>
                    </div>
                    <div class="flex gap-2 text-[9px] text-slate-500 uppercase mt-1">
                        <div class="bg-slate-50 px-2.5 py-1.5 rounded-md shadow-sm border border-slate-100 flex-1 flex items-center justify-between" title="Reason Code"><span class="opacity-70">RC</span> <span class="font-bold text-slate-700">${i.reason_code || '-'}</span></div>
                        <div class="bg-slate-50 px-2.5 py-1.5 rounded-md shadow-sm border border-slate-100 flex-1 flex items-center justify-between" title="Cost Center"><span class="opacity-70">CC</span> <span class="font-bold text-slate-700">${i.cost_center || '-'}</span></div>
                    </div>
                </div>`;
                itemsHtmlTable += itemBlock;
                itemsHtmlCard += itemBlock;
            });
            itemsHtmlTable += '</div>';
            itemsHtmlCard += '</div>';

            let actionBtnsTable = [];
            let actionBtnsCard = [];
            
            if(r.status === 'Pending Head' && ['SectionHead','TeamLeader'].includes(currentUser.role)) {
                const btnAppr = `<button onclick="updateGI('${r.req_id}','approve')" class="text-xs bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mb-1.5"><i class="fas fa-check-circle"></i> ${t('btn_appr')}</button>`;
                const btnRej = `<button onclick="openRej('${r.req_id}')" class="text-xs bg-white border border-red-300 text-red-600 px-4 py-2 rounded-xl shadow-sm hover:bg-red-50 transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-times-circle"></i> ${t('btn_rej')}</button>`;
                actionBtnsTable.push(btnAppr, btnRej);
                actionBtnsCard.push(`<div class="grid grid-cols-2 gap-3 mt-3">`, btnAppr, btnRej, `</div>`);
            } else if (r.status === 'Pending Warehouse' && (['Warehouse', 'Administrator'].includes(currentUser.role) || (currentUser.role === 'TeamLeader' && currentUser.department.toLowerCase() === 'warehouse'))) {
                const btnIss = `<button onclick="openActionPhotoModal('${r.req_id}', 'issue')" class="text-xs bg-gradient-to-r from-indigo-500 to-blue-600 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-box-open text-lg"></i> ${t('btn_iss')}</button>`;
                actionBtnsTable.push(btnIss);
                actionBtnsCard.push(`<div class="mt-3 w-full">`, btnIss, `</div>`);
            } else if (r.status === 'Pending Receive' && r.username === currentUser.username) {
                const btnRecv = `<button onclick="openActionPhotoModal('${r.req_id}', 'receive')" class="text-xs bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-3 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold"><i class="fas fa-hand-holding-box text-lg"></i> ${t('btn_confirm_recv')}</button>`;
                actionBtnsTable.push(btnRecv);
                actionBtnsCard.push(`<div class="mt-3 w-full">`, btnRecv, `</div>`);
            }
            
            if (r.status === 'Pending Head' && r.username === currentUser.username) {
                const btnEdit = `<button onclick="openEditGiModal('${r.req_id}')" class="text-xs bg-gradient-to-r from-amber-400 to-orange-500 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mt-2"><i class="fas fa-edit"></i> Edit</button>`;
                const btnCancel = `<button onclick="cancelGI('${r.req_id}')" class="text-xs bg-gradient-to-r from-rose-500 to-red-600 text-white px-4 py-2 rounded-xl shadow-md hover:shadow-lg transition-all duration-200 active:scale-95 w-full flex items-center justify-center gap-1.5 font-bold mt-2"><i class="fas fa-ban"></i> Cancel</button>`;
                actionBtnsTable.push(btnEdit, btnCancel);
                actionBtnsCard.push(`<div class="grid grid-cols-2 gap-2 mt-2 w-full">`, btnEdit, btnCancel, `</div>`);
            }
            
            let btnTable = actionBtnsTable.length > 0 ? `<div class="flex flex-col w-[120px] mx-auto">${actionBtnsTable.join('')}</div>` : '<span class="text-slate-300 text-center block">-</span>';
            let btnCard = actionBtnsCard.length > 0 ? actionBtnsCard.join('') : '';

            htmlArrayTable.push(`<tr class="border-b border-slate-100 hover:bg-slate-50/50 align-top transition-colors">
                <td class="px-6 py-5"><div class="font-black text-xs text-indigo-700 mb-1">${r.req_id}</div><div class="text-[10px] text-slate-400 font-mono font-medium">${r.created_at}</div></td>
                <td class="px-6 py-5"><div class="font-bold text-xs text-slate-800">${r.fullname}</div><div class="text-[10px] text-slate-500 font-medium mb-1.5">${r.department}</div><div class="text-[9px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md inline-block font-bold border border-slate-200 shadow-sm">Sec: ${r.section || '-'}</div></td>
                <td class="px-6 py-5 min-w-[350px] max-w-[450px] whitespace-normal">
                    <div class="text-xs text-slate-700 font-medium mb-3 bg-indigo-50/60 p-3 rounded-xl border border-indigo-100 shadow-sm"><span class="text-[9px] text-indigo-500 uppercase font-black tracking-wider block mb-1.5"><i class="fas fa-tasks mr-1"></i> Act. Desc:</span> <span class="italic leading-relaxed text-indigo-900">"${r.purpose}"</span></div>
                    ${itemsHtmlTable}
                </td>
                <td class="px-6 py-5 text-center flex justify-center">${statusHTMLTable}</td>
                <td class="px-6 py-5 text-right align-middle">${btnTable}</td>
            </tr>`);

            htmlArrayCard.push(`
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 relative transition-all hover:shadow-md">
                <div class="flex justify-between items-start mb-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="font-black text-sm text-indigo-700 mb-0.5">${r.req_id}</div>
                        <div class="text-[10px] text-slate-400 font-mono">${r.created_at}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xs text-slate-800">${r.fullname}</div>
                        <div class="text-[10px] text-slate-500">${r.department} <span class="bg-slate-100 px-1 py-0.5 rounded ml-1">Sec: ${r.section || '-'}</span></div>
                    </div>
                </div>
                
                <div class="mb-5">
                    <div class="text-[10px] text-indigo-500 uppercase font-black tracking-wider mb-2"><i class="fas fa-tasks mr-1"></i> Act. Desc:</div>
                    <div class="text-xs italic text-indigo-900 bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 shadow-inner">"${r.purpose}"</div>
                </div>
                
                <div class="mb-5">
                    <div class="text-[10px] text-slate-400 uppercase font-black tracking-wider mb-2"><i class="fas fa-box-open mr-1"></i> Items Requested:</div>
                    ${itemsHtmlCard}
                </div>
                
                <div class="border-t border-slate-100 pt-4">
                    ${statusHTMLCard}
                    ${btnCard}
                </div>
            </div>`);
        });

        tb.innerHTML = htmlArrayTable.join('');
        cardContainer.innerHTML = htmlArrayCard.join('');
    }

    function openActionPhotoModal(id, actType) {
        document.getElementById('action-photo-id').value = id;
        document.getElementById('action-photo-type').value = actType;
        document.getElementById('input-act-photo').value = '';
        document.getElementById('act-file-name').innerText = 'Click to upload image';
        
        if (actType === 'issue') {
            document.getElementById('action-photo-title').innerHTML = '<i class="fas fa-box-open mr-2"></i> Issue Items (Warehouse)';
            document.getElementById('action-photo-desc').innerText = 'Harap lampirkan bukti foto barang fisik yang disiapkan/dikeluarkan dari gudang.';
        } else {
            document.getElementById('action-photo-title').innerHTML = '<i class="fas fa-hand-holding-box mr-2"></i> Confirm Receive (User)';
            document.getElementById('action-photo-desc').innerText = 'Harap lampirkan bukti foto barang telah diterima dengan baik.';
        }

        toggleActionPhotoSource('file'); 
        openModal('modal-action-photo');
    }

    function toggleActionPhotoSource(source) {
        activeSourceAct = source;
        const btnFile = document.getElementById('btn-act-file');
        const btnCam = document.getElementById('btn-act-cam');
        const contFile = document.getElementById('source-act-file');
        const contCam = document.getElementById('source-act-camera');

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

    async function startCamera() {
        const video = document.getElementById('camera-stream-act');
        const preview = document.getElementById('camera-preview-act');
        preview.classList.add('hidden'); video.classList.remove('hidden');
        document.getElementById('btn-capture-act').classList.remove('hidden');
        document.getElementById('btn-retake-act').classList.add('hidden');
        capturedActBase64 = null;
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
            video.srcObject = stream;
            videoStreamAct = stream;
        } catch (err) { 
            showCustomAlert("Camera Error", "Kamera tidak bisa diakses. Gunakan fitur Upload File."); 
            toggleActionPhotoSource('file'); 
        }
    }

    function stopCamera() {
        if (videoStreamAct) { videoStreamAct.getTracks().forEach(track => track.stop()); videoStreamAct = null; }
    }

    function takeActionSnapshot() {
        const video = document.getElementById('camera-stream-act');
        const canvas = document.getElementById('camera-canvas-act');
        const preview = document.getElementById('camera-preview-act');
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d'); ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            capturedActBase64 = canvas.toDataURL('image/jpeg', 0.8);
            preview.src = capturedActBase64; 
            preview.classList.remove('hidden'); 
            video.classList.add('hidden');
            document.getElementById('btn-capture-act').classList.add('hidden');
            document.getElementById('btn-retake-act').classList.remove('hidden');
        }
    }

    function retakeActionPhoto() {
        capturedActBase64 = null;
        document.getElementById('camera-preview-act').classList.add('hidden');
        document.getElementById('camera-stream-act').classList.remove('hidden');
        document.getElementById('btn-capture-act').classList.remove('hidden');
        document.getElementById('btn-retake-act').classList.add('hidden');
    }

    function compressImage(base64Str, maxWidth = 1000, quality = 0.6) {
        return new Promise((resolve, reject) => {
            const img = new Image(); img.src = base64Str;
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas');
                    let width = img.width; let height = img.height;
                    if (width > maxWidth) { height *= maxWidth / width; width = maxWidth; }
                    canvas.width = width; canvas.height = height;
                    const ctx = canvas.getContext('2d'); ctx.drawImage(img, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/jpeg', quality));
                } catch(e) { reject("Canvas error: " + e.message); }
            };
            img.onerror = () => resolve(base64Str);
        });
    }

    async function submitActionWithPhoto() {
        const reqId = document.getElementById('action-photo-id').value;
        const actType = document.getElementById('action-photo-type').value;
        const btn = document.getElementById('btn-submit-action-photo');
        const orgHtml = btn.innerHTML;

        let base64Data = null;
        
        if (activeSourceAct === 'camera' && capturedActBase64) {
            base64Data = capturedActBase64;
        } else {
            const fileInput = document.getElementById('input-act-photo');
            if (fileInput.files.length > 0) {
                base64Data = await new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => resolve(e.target.result);
                    reader.readAsDataURL(fileInput.files[0]);
                });
            }
        }

        if (!base64Data) {
            showCustomAlert("Error", "Harap lampirkan foto bukti terlebih dahulu!");
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...`;

        try {
            const compressedBase64 = await compressImage(base64Data);
            
            const payload = {
                action: 'updateStatus',
                reqId: reqId,
                act: actType,
                role: currentUser.role,
                department: currentUser.department,
                username: currentUser.username,
                fullname: currentUser.fullname,
                photoBase64: compressedBase64
            };

            const response = await fetch('api/gis.php', { method: 'POST', body: JSON.stringify(payload) });
            const res = await response.json();

            btn.disabled = false; btn.innerHTML = orgHtml;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success) {
                closeModal('modal-action-photo');
                loadData();
                showCustomAlert("Success", res.message);
            } else {
                showCustomAlert("Error", res.message);
            }

        } catch (err) {
            btn.disabled = false; btn.innerHTML = orgHtml;
            showCustomAlert("Error", "Gagal memproses foto atau jaringan bermasalah.");
        }
    }

    function openGiModal() {
        document.getElementById('gi-action-type').value = 'submit';
        document.getElementById('gi-edit-id').value = '';
        document.getElementById('disp-req-name').innerText = currentUser.fullname + " / " + currentUser.department;
        document.getElementById('gi-section').value = '';
        document.getElementById('gi-purpose').value = '';
        document.getElementById('gi-items-container').innerHTML = '';
        giRowCount = 0; 
        
        document.getElementById('modal-gi-title').innerHTML = `<i class="fas fa-file-invoice text-indigo-600 mr-2 text-lg"></i> <span data-i18n="form_gi">Form Good Issue</span>`;
        const submitBtn = document.getElementById('btn-submit-gi');
        submitBtn.innerHTML = `<i class="fas fa-paper-plane mr-1.5"></i> <span data-i18n="btn_submit_form">Submit Form</span>`;
        submitBtn.className = "px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-indigo-700";
        
        addGiRow();
        openModal('modal-gi');
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
        giRowCount = 0;
        
        const itemsArr = req.items || [];
        itemsArr.forEach(it => {
            addGiRow();
            const row = document.getElementById(`gi-row-${giRowCount}`);
            const displayVal = it.code + ' - ' + it.name; 
            row.querySelector('.gi-item-display').value = displayVal;
            row.querySelector('.gi-item-code').value = it.code;
            row.querySelector('.gi-item-name').value = it.name;
            row.querySelector('.gi-qty').value = it.qty;
            row.querySelector('.gi-uom').value = it.uom;
            row.querySelector('.gi-reason').value = it.reason_code || '';
            row.querySelector('.gi-cost').value = it.cost_center || '';
            
            const invItem = inventoryData.find(inv => inv.item_code === it.code);
            if(invItem) {
                row.querySelector('.gi-qty').max = invItem.stock;
                row.querySelector('.gi-qty').title = "Max stock: " + invItem.stock;
                row.querySelector('.gi-stock').value = invItem.stock;
            } else {
                row.querySelector('.gi-stock').value = 0;
            }
        });
        
        document.getElementById('modal-gi-title').innerHTML = `<i class="fas fa-edit text-amber-500 mr-2 text-lg"></i> Edit Good Issue Form`;
        const submitBtn = document.getElementById('btn-submit-gi');
        submitBtn.innerHTML = `<i class="fas fa-save mr-1.5"></i> Update Form`;
        submitBtn.className = "px-8 py-2.5 bg-amber-500 text-white rounded-xl font-bold text-sm shadow-md btn-animated hover:bg-amber-600";
        
        openModal('modal-gi');
    }

    function addGiRow() {
        giRowCount++;
        
        const d = document.createElement('div');
        d.className = "grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm relative transition hover:border-indigo-200";
        d.id = `gi-row-${giRowCount}`;
        
        d.innerHTML = `
            <button type="button" onclick="document.getElementById('${d.id}').remove()" class="absolute -top-2.5 -right-2.5 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow-md btn-animated z-10"><i class="fas fa-times text-[10px]"></i></button>
            <div class="sm:col-span-3">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">Item</label>
                <div class="relative w-full">
                    <input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs gi-item-display focus:ring-2 focus:ring-indigo-500 outline-none cursor-pointer bg-slate-50 font-medium transition" placeholder="${t('ph_search_item')}" onfocus="showDropdown(this, 'gi')" onkeyup="filterDropdown(this, 'gi')" autocomplete="off" required>
                    <input type="hidden" class="gi-item-code">
                    <input type="hidden" class="gi-item-name">
                    <i class="fas fa-search absolute right-3 top-3.5 text-slate-400 pointer-events-none text-[12px]"></i>
                    <div class="dropdown-list hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-2xl mt-1.5 max-h-48 overflow-y-auto dropdown-scroll left-0"></div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-i18n="curr_stk_short">Curr. Stock</label>
                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gi-stock text-slate-500 font-bold" placeholder="0" readonly tabindex="-1">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-i18n="req_qty">Req Qty</label>
                <input type="number" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-qty focus:ring-2 focus:ring-indigo-500 outline-none font-black text-slate-700 transition" placeholder="Qty" required>
            </div>
            <div class="sm:col-span-1">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">UoM</label>
                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gi-uom text-slate-500 font-bold" placeholder="UoM" readonly tabindex="-1">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">Reason Code</label>
                <input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-reason focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition" placeholder="Code">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5 text-rose-500" data-i18n="cost_ctr">Cost Center *</label>
                <input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gi-cost focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition" placeholder="Cost Ctr" required>
            </div>
        `;
        document.getElementById('gi-items-container').appendChild(d);
    }

    function submitGi() {
        const section = document.getElementById('gi-section').value;
        const purpose = document.getElementById('gi-purpose').value;
        if(!section || !purpose) { showCustomAlert("Info", t('err_req')); return; }

        const rows = document.querySelectorAll('#gi-items-container > div');
        let items = [];
        let valid = true;
        
        rows.forEach(r => {
            const code = r.querySelector('.gi-item-code').value;
            const name = r.querySelector('.gi-item-name').value;
            const qty = r.querySelector('.gi-qty').value;
            const uom = r.querySelector('.gi-uom').value;
            const reason = r.querySelector('.gi-reason').value;
            const cost = r.querySelector('.gi-cost').value.trim();
            const max = r.querySelector('.gi-qty').max;

            if(code && qty > 0) {
                if(parseInt(qty) > parseInt(max)) { 
                    showCustomAlert("Error", "Jumlah melebihi stok untuk " + name); 
                    valid = false; 
                }
                if(cost === '') {
                    showCustomAlert("Warning", t('err_cost_center'));
                    valid = false;
                }
                items.push({ code: code, name: name, qty: qty, uom: uom, reason_code: reason, cost_center: cost });
            }
        });
        
        if(!valid) return;
        if(items.length === 0) { showCustomAlert("Error", "Minimal 1 barang harus diisi dengan benar."); return; }
        
        const actionType = document.getElementById('gi-action-type').value;
        const reqId = document.getElementById('gi-edit-id').value;

        const btn = document.getElementById('btn-submit-gi');
        const orgTxt = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1.5"></i> ${t('processing')}`;

        const p = { 
            action: actionType === 'edit' ? 'editRequest' : 'submitRequest', 
            reqId: reqId,
            username: currentUser.username, 
            fullname: currentUser.fullname, 
            department: currentUser.department, 
            section: section, 
            purpose: purpose, 
            items: items 
        };

        fetch('api/gis.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
            btn.disabled = false; btn.innerHTML = orgTxt;
            if(res.code === 401) { logoutAction(); return; }
            if(res.success){ closeModal('modal-gi'); loadData(); showCustomAlert("Success", res.message); } else { showCustomAlert("Error", res.message); } 
        }).catch(e => { btn.disabled = false; btn.innerHTML = orgTxt; showCustomAlert("Error", t('err_conn')); });
    }

    function cancelGI(reqId) {
        showCustomConfirm("Cancel Request", "Anda yakin ingin membatalkan permintaan ini?", () => {
            const p = { action: 'cancelRequest', reqId: reqId, username: currentUser.username, fullname: currentUser.fullname };
            fetch('api/gis.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
                if(res.code === 401) { logoutAction(); return; }
                if(res.success) { loadData(); showCustomAlert("Success", res.message); } 
                else showCustomAlert("Error", res.message); 
            }).catch(e => showCustomAlert("Error", t('err_conn')));
        });
    }

    function updateGI(id, act, reason='') {
        if(act==='approve') {
            showCustomConfirm("Approve Request", "Approve request ini?", () => {
                fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'updateStatus', reqId:id, act:act, role:currentUser.role, fullname:currentUser.fullname, reason:reason})}).then(r=>r.json()).then(res => { 
                    if(res.code === 401) { logoutAction(); return; }
                    if(res.success) loadData(); else showCustomAlert("Error", res.message); 
                });
            });
        } else {
            fetch('api/gis.php', {method:'POST', body:JSON.stringify({action:'updateStatus', reqId:id, act:act, role:currentUser.role, fullname:currentUser.fullname, reason:reason})}).then(r=>r.json()).then(res => { 
                if(res.code === 401) { logoutAction(); return; }
                if(res.success) loadData(); else showCustomAlert("Error", res.message); 
            });
        }
    }

    function openRej(id) { document.getElementById('rej-id').value = id; document.getElementById('rej-reason').value=''; openModal('modal-reject'); }
    function executeReject() { const id = document.getElementById('rej-id').value; const r = document.getElementById('rej-reason').value; if(r){ closeModal('modal-reject'); updateGI(id, 'reject', r); } else showCustomAlert('Error', 'Reason required'); }

    // --- GOOD RECEIVE (GR) ---
    function filterGR() {
        const term = document.getElementById('search-gr').value.toLowerCase();
        const filtered = grData.filter(r => 
            (r.gr_id || '').toLowerCase().includes(term) || 
            (r.fullname || '').toLowerCase().includes(term) || 
            (r.remarks || '').toLowerCase().includes(term)
        );
        renderGR(filtered);
    }

    function renderGR(data = grData) {
        const tb = document.getElementById('gr-table-body');
        const cardContainer = document.getElementById('gr-card-container');
        
        const formatDt = (dtStr) => {
            if(!dtStr || dtStr === '0000-00-00 00:00:00' || dtStr === '-') return '-';
            const d = new Date(dtStr);
            if(isNaN(d)) return dtStr;
            return d.toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'});
        };

        if(!data || data.length === 0) { 
            tb.innerHTML = `<tr><td colspan="4" class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</td></tr>`; 
            cardContainer.innerHTML = `<div class="text-center py-10 text-slate-400 text-xs italic">${t('no_data')}</div>`;
            return; 
        }

        let htmlArrayTable = [];
        let htmlArrayCard = [];

        data.forEach(r => {
            let itemsHtmlTable = '<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-1">';
            let itemsHtmlCard = '<div class="flex flex-col gap-2 mt-2">';
            
            let itemsArr = r.items || [];
            itemsArr.forEach(i => {
                const itemBlock = `
                <div class="bg-teal-50 p-2.5 rounded-lg border border-teal-100 flex justify-between items-center shadow-sm">
                    <span class="text-[10px] font-bold text-teal-800 pr-2 leading-tight" title="${i.name}">${i.code} - ${i.name}</span>
                    <span class="text-xs font-black text-teal-900 bg-white px-2 py-0.5 rounded shadow-sm border border-teal-50 whitespace-nowrap">+${i.qty} <span class="text-[9px] font-normal text-slate-500">${i.uom}</span></span>
                </div>`;
                itemsHtmlTable += itemBlock;
                itemsHtmlCard += itemBlock;
            });
            itemsHtmlTable += '</div>';
            itemsHtmlCard += '</div>';

            htmlArrayTable.push(`<tr class="border-b border-slate-100 hover:bg-slate-50 align-top transition-colors">
                <td class="px-6 py-4"><div class="font-bold text-xs text-teal-700">${r.gr_id}</div><div class="text-[9px] text-slate-400 font-mono mt-0.5">${formatDt(r.created_at)}</div></td>
                <td class="px-6 py-4"><div class="font-bold text-xs text-slate-700">${r.fullname}</div><div class="text-[10px] text-slate-500">Warehouse Admin</div></td>
                <td class="px-6 py-4 text-xs text-slate-600 font-medium italic"><div class="bg-slate-50 p-2 rounded-lg border border-slate-100">"${r.remarks}"</div></td>
                <td class="px-6 py-4 min-w-[300px] whitespace-normal">${itemsHtmlTable}</td>
            </tr>`);

            htmlArrayCard.push(`
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-1 transition-all hover:shadow-md">
                <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <div class="font-black text-sm text-teal-700 mb-0.5">${r.gr_id}</div>
                        <div class="text-[10px] text-slate-400 font-mono">${formatDt(r.created_at)}</div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-xs text-slate-800">${r.fullname}</div>
                        <div class="text-[9px] text-slate-500 uppercase font-bold bg-slate-100 px-1.5 py-0.5 rounded mt-1 inline-block">Warehouse Admin</div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="text-[10px] text-slate-400 uppercase font-bold mb-1.5">Remarks / Supplier:</div>
                    <div class="text-xs font-medium italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">"${r.remarks}"</div>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold mb-1.5">Items Received:</div>
                    ${itemsHtmlCard}
                </div>
            </div>`);
        });
        
        tb.innerHTML = htmlArrayTable.join('');
        cardContainer.innerHTML = htmlArrayCard.join('');
    }

    let grRowCount = 0;
    function openGrModal() {
        document.getElementById('gr-remarks').value = '';
        document.getElementById('gr-items-container').innerHTML = '';
        grRowCount = 0; addGrRow();
        openModal('modal-gr');
    }

    function addGrRow() {
        grRowCount++;
        
        const d = document.createElement('div');
        d.className = "grid grid-cols-1 sm:grid-cols-12 gap-3 items-center bg-white p-4 rounded-xl border border-teal-100 shadow-sm relative transition hover:border-teal-200";
        d.id = `gr-row-${grRowCount}`;
        
        d.innerHTML = `
            <button type="button" onclick="document.getElementById('${d.id}').remove()" class="absolute -top-2.5 -right-2.5 bg-red-100 text-red-600 rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500 hover:text-white transition shadow-md btn-animated z-10"><i class="fas fa-times text-[10px]"></i></button>
            <div class="sm:col-span-5">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">Item</label>
                <div class="relative w-full">
                    <input type="text" class="w-full border border-slate-300 rounded-xl p-3 text-xs gr-item-display focus:ring-2 focus:ring-teal-500 outline-none cursor-pointer bg-slate-50 font-medium transition" placeholder="${t('ph_search_item')}" onfocus="showDropdown(this, 'gr')" onkeyup="filterDropdown(this, 'gr')" autocomplete="off" required>
                    <input type="hidden" class="gr-item-code">
                    <input type="hidden" class="gr-item-name">
                    <i class="fas fa-search absolute right-3 top-2.5 text-slate-400 pointer-events-none text-[12px]"></i>
                    <div class="dropdown-list hidden absolute z-50 w-full bg-white border border-slate-200 rounded-xl shadow-2xl mt-1.5 max-h-48 overflow-y-auto dropdown-scroll left-0"></div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-i18n="curr_stk_short">Curr. Stock</label>
                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gr-stock text-slate-500 font-bold" placeholder="0" readonly tabindex="-1">
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5" data-i18n="qty_recv">Qty Masuk</label>
                <input type="number" class="w-full border border-slate-300 rounded-xl p-3 text-xs text-center gr-qty focus:ring-2 focus:ring-teal-500 outline-none font-black text-slate-700 transition" placeholder="Qty Masuk" required min="1">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[9px] font-bold text-slate-400 uppercase sm:hidden mb-1.5">UoM</label>
                <input type="text" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs text-center gr-uom text-slate-500 font-bold" placeholder="UoM" readonly tabindex="-1">
            </div>
        `;
        document.getElementById('gr-items-container').appendChild(d);
    }

    function submitGr() {
        const remarks = document.getElementById('gr-remarks').value;
        if(!remarks) { showCustomAlert("Info", "Harap isi Remarks / Supplier."); return; }

        const rows = document.querySelectorAll('#gr-items-container > div');
        let items = [];
        
        rows.forEach(r => {
            const code = r.querySelector('.gr-item-code').value;
            const name = r.querySelector('.gr-item-name').value;
            const qty = r.querySelector('.gr-qty').value;
            const uom = r.querySelector('.gr-uom').value;

            if(code && qty > 0) {
                items.push({ code: code, name: name, qty: qty, uom: uom });
            }
        });
        
        if(items.length === 0) { showCustomAlert("Info", "Minimal 1 barang masuk harus diisi."); return; }
        
        showCustomConfirm("Konfirmasi", "Yakin memproses penerimaan ini? Stok di Inventory akan bertambah otomatis.", () => {
            const btn = document.getElementById('btn-submit-gr');
            const orgTxt = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1.5"></i> Menyimpan...`;

            const p = { action: 'submitGR', role: currentUser.role, department: currentUser.department, username: currentUser.username, fullname: currentUser.fullname, remarks: remarks, items: items };
            fetch('api/gis.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
                btn.disabled = false; btn.innerHTML = orgTxt;
                if(res.code === 401) { logoutAction(); return; }
                if(res.success){ closeModal('modal-gr'); loadData(); showCustomAlert("Success", res.message); } 
                else { showCustomAlert("Error", res.message); } 
            }).catch(e => { btn.disabled = false; btn.innerHTML = orgTxt; showCustomAlert("Error", t('err_conn')); });
        });
    }

    // --- USERS MANAGEMENT (Admin Only) ---
    function openManageUsers() { openModal('modal-users'); loadUsers(); }
    function loadUsers() { fetch('api/users.php', {method:'POST', body:JSON.stringify({action:'getAllUsers'})}).then(r=>r.json()).then(d => { if(d.code===401){logoutAction(); return;} allUsers = Array.isArray(d) ? d : (d.data || []); renderUsers(allUsers); }); }
    function renderUsers(data) {
        const c = document.getElementById('user-list'); c.innerHTML = '';
        let htmlArray = [];
        data.forEach(u => {
            htmlArray.push(`<div onclick="editUser('${u.username}')" class="p-3 border border-slate-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 cursor-pointer text-xs mb-2 transition shadow-sm bg-white"><div class="font-bold text-slate-700">${u.fullname}</div><div class="text-[10px] text-slate-500 mt-1.5">${u.username} • <span class="bg-slate-100 px-1.5 py-0.5 rounded font-bold">${u.role}</span></div></div>`);
        });
        c.innerHTML = htmlArray.join('');
    }
    function filterUsers() { const t = document.getElementById('search-user').value.toLowerCase(); renderUsers(allUsers.filter(u => u.fullname.toLowerCase().includes(t) || u.username.toLowerCase().includes(t))); }
    
    function handleRoleChange(sel) {
        if(sel.value === 'Administrator') {
            document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = true; chk.disabled = true; });
        } else {
            document.querySelectorAll('.acc-chk').forEach(chk => { chk.disabled = false; });
        }
    }

    function resetUserForm() { 
        document.getElementById('user-form').reset(); 
        document.getElementById('u-user').disabled=false; 
        document.getElementById('u-pass').required=true; 
        document.getElementById('btn-del-user').classList.add('hidden'); 
        document.getElementById('form-title').innerText = 'Create User'; 
        
        document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = false; chk.disabled = false; });
        document.getElementById('chk-gi-submit').checked = true; 
    }
    
    function editUser(u) {
        const user = allUsers.find(x => x.username === u);
        document.getElementById('u-user').value = user.username; document.getElementById('u-user').disabled=true;
        document.getElementById('u-pass').value = ''; document.getElementById('u-pass').required=false;
        document.getElementById('u-name').value = user.fullname; 
        document.getElementById('u-nik').value = user.nik || ''; 
        document.getElementById('u-role').value = user.role;
        document.getElementById('u-dept').value = user.department; 
        document.getElementById('u-phone').value = user.phone || '';
        document.getElementById('btn-del-user').classList.remove('hidden'); 
        document.getElementById('form-title').innerText = 'Edit User';

        const acc = JSON.parse(user.access_rights || '[]');
        document.querySelectorAll('.acc-chk').forEach(chk => {
            chk.checked = acc.includes(chk.value);
        });

        if(user.role === 'Administrator') {
             document.querySelectorAll('.acc-chk').forEach(chk => { chk.checked = true; chk.disabled = true; });
        } else {
             document.querySelectorAll('.acc-chk').forEach(chk => { chk.disabled = false; });
        }
    }
    
    function saveUser() {
        let acc = [];
        document.querySelectorAll('.acc-chk').forEach(chk => {
            if(chk.checked && !chk.disabled) acc.push(chk.value);
        });
        if (document.getElementById('u-role').value === 'Administrator') {
             acc = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data'];
        }

        const p = { 
            action: 'saveUser', 
            isEdit: document.getElementById('u-user').disabled, 
            data: { 
                username: document.getElementById('u-user').value, 
                password: document.getElementById('u-pass').value, 
                fullname: document.getElementById('u-name').value, 
                nik: document.getElementById('u-nik').value, 
                role: document.getElementById('u-role').value, 
                department: document.getElementById('u-dept').value, 
                phone: document.getElementById('u-phone').value,
                access_rights: JSON.stringify(acc)
            } 
        };
        fetch('api/users.php', {method:'POST', body:JSON.stringify(p)}).then(r=>r.json()).then(res => { 
            if(res.code === 401) { logoutAction(); return; }
            if(res.success){ resetUserForm(); loadUsers(); showCustomAlert("Success", "Berhasil simpan user."); } else showCustomAlert("Error", res.message); 
        });
    }
    
    function deleteUser() { showCustomConfirm("Delete User", "Hapus user ini?", () => { fetch('api/users.php', {method:'POST', body:JSON.stringify({action:'deleteUser', username:document.getElementById('u-user').value})}).then(r=>r.json()).then(res => { if(res.success){ resetUserForm(); loadUsers(); } }); }); }

  </script>
</body>
</html>