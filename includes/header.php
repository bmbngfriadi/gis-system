<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$currentUser = $_SESSION['user_data'];
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $currentUser['role'];
$dept = $currentUser['department'];
$rights = json_decode($currentUser['access_rights'] ?? '[]', true) ?: [];
$isAdmin = ($role === 'Administrator');
$isWH = ($role === 'Warehouse' || ($role === 'TeamLeader' && strtolower($dept) === 'warehouse'));

if (empty($rights) && $isWH) {
    $rights = ['gi_submit', 'gr_submit', 'item_add', 'item_edit', 'stock_edit', 'export_data', 'edit_price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GIS Portal - PT Cemindo Gemilang</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png">
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
  
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
    .loader-spin { border: 3px solid #e2e8f0; border-top: 3px solid #4f46e5; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.8s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .shine-effect { position: relative; overflow: hidden; }
    .shine-effect::before { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%); transform: skewX(-20deg); animation: shine 5s infinite; z-index: 1; }
    @keyframes shine { 0% { left: -100%; } 20% { left: 200%; } 100% { left: 200%; } }
    @keyframes gradientBG { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
    .bg-live-gradient { background-size: 200% 200%; animation: gradientBG 4s ease infinite; }
    .status-badge { padding: 6px 12px; border-radius: 9999px; font-weight: 800; font-size: 0.65rem; text-transform: uppercase; border: 1px solid transparent; letter-spacing: 0.05em; display: inline-flex; align-items: center; justify-content: center;}
    .btn-animated { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; overflow: hidden; }
    .btn-animated:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
    .btn-animated:active { transform: translateY(1px) scale(0.96); box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
    .card-filter { cursor: pointer; transition: all 0.3s ease; }
    .card-filter:hover { transform: translateY(-4px); box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1); z-index: 10; }
    .card-filter-active { ring: 4px; --tw-ring-color: rgba(255,255,255,0.6); --tw-ring-offset-width: 2px; --tw-ring-offset-color: transparent; box-shadow: 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color), 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color), 0 10px 15px -3px rgba(0,0,0,0.1); transform: scale(1.02); z-index: 20; }
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

  <nav class="bg-gradient-to-r from-indigo-700 to-purple-800 text-white shadow-md sticky top-0 z-40 flex-none">
     <div class="container mx-auto px-4 py-3 flex justify-between items-center">
       <div class="flex items-center gap-3">
           <div class="bg-white/90 p-1.5 rounded-lg shadow-sm"><img src="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png" alt="Logo" class="h-6 w-auto object-contain"></div>
           <div class="flex flex-col"><span class="font-bold leading-none text-base tracking-tight">GIS System</span><span class="text-[10px] text-indigo-200">PT Cemindo Gemilang</span></div>
       </div>
       <div class="flex items-center gap-2 sm:gap-4">
           <button onclick="toggleLanguage()" class="bg-indigo-900/40 w-8 h-8 rounded-full hover:bg-indigo-900 text-[10px] font-bold border border-indigo-400/50 transition flex items-center justify-center text-indigo-100 hover:text-white"><span id="lang-label">EN</span></button>
           <div class="text-right text-xs hidden sm:block">
               <div class="font-bold"><?= htmlspecialchars($currentUser['fullname']) ?></div>
               <div class="text-indigo-200"><?= htmlspecialchars($role . " - " . $dept) ?></div>
           </div>
           <div class="h-8 w-px bg-indigo-500/50 hidden sm:block mx-1"></div>
           
           <button onclick="openProfileModal()" class="bg-indigo-900/40 p-2.5 rounded-full hover:bg-indigo-900 text-xs border border-indigo-400/50 transition btn-animated" title="My Profile"><i class="fas fa-user-circle"></i></button>
           
           <?php if($isAdmin): ?>
           <button onclick="openManageUsers()" class="bg-indigo-900/40 p-2.5 rounded-full hover:bg-indigo-900 text-xs border border-indigo-400/50 transition btn-animated" title="Manage Users"><i class="fas fa-users-cog"></i></button>
           <?php endif; ?>

           <button onclick="logoutAction()" class="bg-rose-600 p-2.5 rounded-full hover:bg-rose-700 text-xs border border-rose-400/50 transition btn-animated" title="Logout"><i class="fas fa-sign-out-alt"></i></button>
       </div>
     </div>
  </nav>
  
  <main class="flex-grow container mx-auto px-4 py-6 overflow-y-auto custom-scroll">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 mb-6 pb-4 sm:pb-2 gap-4">
        <div class="flex gap-2 overflow-x-auto custom-scroll w-full sm:w-auto pb-2 sm:pb-0">
            <a href="index.php" class="px-5 py-2.5 text-sm <?= ($currentPage == 'index.php') ? 'tab-active border-b-2 border-indigo-600' : 'tab-inactive' ?> transition-colors whitespace-nowrap flex items-center"><i class="fas fa-file-export mr-2"></i> <span data-translate="true" data-i18n="tab_gi">Good Issue (GI)</span></a>
            
            <?php if($isAdmin || in_array('gr_submit', $rights)): ?>
            <a href="gr.php" class="px-5 py-2.5 text-sm <?= ($currentPage == 'gr.php') ? 'tab-active border-b-2 border-indigo-600' : 'tab-inactive' ?> transition-colors whitespace-nowrap flex items-center"><i class="fas fa-file-import mr-2"></i> <span data-translate="true" data-i18n="tab_gr">Good Receive (GR)</span></a>
            <?php endif; ?>
            
            <a href="inventory.php" class="px-5 py-2.5 text-sm <?= ($currentPage == 'inventory.php') ? 'tab-active border-b-2 border-indigo-600' : 'tab-inactive' ?> transition-colors whitespace-nowrap flex items-center"><i class="fas fa-warehouse mr-2"></i> <span data-translate="true" data-i18n="tab_inv">Inventory</span></a>
        </div>
        
        <?php if($isAdmin || in_array('export_data', $rights)): ?>
        <button onclick="openExportModal()" class="w-full sm:w-auto bg-slate-800 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-slate-900 transition btn-animated flex items-center justify-center"><i class="fas fa-print mr-2"></i> <span data-translate="true" data-i18n="btn_export_data">Export Report</span></button>
        <?php endif; ?>
    </div>