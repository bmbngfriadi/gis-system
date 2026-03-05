<?php
session_start();
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - GIS System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
    .blob-bg { position: absolute; border-radius: 50%; filter: blur(20px); opacity: 0.4; animation: blobMove 6s infinite alternate; z-index: 0; }
    @keyframes blobMove { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(10px, -15px) scale(1.2); } }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-animated { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .btn-animated:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15); }
    .loader-spin { border: 3px solid #e2e8f0; border-top: 3px solid #4f46e5; border-radius: 50%; width: 18px; height: 18px; animation: spin 0.8s linear infinite; display: inline-block; vertical-align: middle; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  </style>
</head>
<body class="text-slate-800 h-screen flex flex-col">
  <div class="flex-grow flex items-center justify-center p-6 relative">
      <div class="blob-bg bg-indigo-200 w-96 h-96 -top-10 -left-10 z-0"></div>
      <div class="blob-bg bg-fuchsia-200 w-96 h-96 bottom-0 right-0 z-0" style="animation-delay: 2s;"></div>
      
      <div class="w-full max-w-sm bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white relative z-10 animate-slide-up">
         <div class="text-center mb-8">
            <img src="https://i.ibb.co.com/prMYS06h/LOGO-2025-03.png" alt="Company Logo" class="h-20 mx-auto mb-4 object-contain drop-shadow-md">
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">GIS Portal</h1>
            <p class="text-xs text-slate-500 font-medium">Good Issue & Inventory System</p>
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
               <div class="text-right mt-1.5"><button type="button" onclick="document.getElementById('modal-forgot').classList.remove('hidden')" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold">Lupa Password?</button></div>
            </div>
            <button type="submit" id="btn-login" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3 rounded-xl shadow-md hover:opacity-90 transition btn-animated mt-2">Login</button>
         </form>
      </div>
  </div>

  <div id="modal-forgot" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[70] flex items-center justify-center p-4">
      <div class="bg-white rounded-3xl w-full max-w-sm shadow-2xl animate-slide-up overflow-hidden">
          <div class="bg-slate-50 px-6 py-5 border-b border-slate-200 flex justify-between items-center">
              <h3 class="font-bold text-slate-800 tracking-tight">Reset Password</h3>
              <button onclick="document.getElementById('modal-forgot').classList.add('hidden')" class="text-slate-400 hover:text-red-500"><i class="fas fa-times text-lg"></i></button>
          </div>
          <div class="p-6">
              <div class="mb-5 bg-indigo-50 text-indigo-700 text-[10px] p-4 rounded-xl border border-indigo-100 font-medium">
                  <i class="fab fa-whatsapp mr-1 text-base align-middle"></i> Link reset akan dikirimkan ke nomor WhatsApp Anda.
              </div>
              <div class="mb-6">
                  <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                  <input type="text" id="forgot-username" class="w-full border border-slate-300 rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition">
              </div>
              <button onclick="submitForgot()" id="btn-forgot" class="w-full bg-indigo-600 text-white font-bold py-3.5 rounded-xl shadow-md hover:bg-indigo-700 transition btn-animated">Kirim Link WhatsApp</button>
          </div>
      </div>
  </div>

  <script>
    // ==========================================
    // FUNGSI TRIGGER ESC UNTUK MENUTUP MODAL FORGOT PASSWORD
    // ==========================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            const forgotModal = document.getElementById('modal-forgot');
            if (forgotModal && !forgotModal.classList.contains('hidden')) {
                forgotModal.classList.add('hidden');
            }
        }
    });
    // ==========================================

    function toggleLoginPass() {
        const p = document.getElementById('login-p'); const icon = document.getElementById('icon-login-pass');
        if(p.type === 'password') { p.type = 'text'; icon.className = 'fas fa-eye-slash'; } else { p.type = 'password'; icon.className = 'fas fa-eye'; }
    }
    function handleLogin() {
        const u = document.getElementById('login-u').value; const p = document.getElementById('login-p').value; const btn = document.getElementById('btn-login'); 
        btn.disabled = true; btn.innerHTML = `<span class="loader-spin mr-2 border-t-white"></span> Memproses...`;
        
        fetch('api/auth.php', { method:'POST', body:JSON.stringify({action:'login', username:u, password:p}) })
        .then(r=>r.json()).then(res => {
            btn.disabled = false; btn.innerHTML = `Login`;
            if(res.success) { window.location.href = 'index.php'; } else { alert(res.message); }
        }).catch(err => { btn.disabled = false; btn.innerHTML = `Login`; alert("Connection error."); });
    }
    function submitForgot() {
        const u = document.getElementById('forgot-username').value; if(!u) return alert("Silakan masukkan username");
        const btn = document.getElementById('btn-forgot'); const originalText = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Memproses...`;

        fetch('api/auth.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ action: 'requestReset', username: u }) })
        .then(r => r.json()).then(res => {
            btn.disabled = false; btn.innerHTML = originalText;
            if(res.success) { alert(res.message); document.getElementById('modal-forgot').classList.add('hidden'); } else { alert(res.message); }
        }).catch(err => { btn.disabled = false; btn.innerHTML = originalText; alert("Connection error."); });
    }
  </script>
</body>
</html>