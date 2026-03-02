<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - GIS Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
    .animate-slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .blob-bg { position: absolute; border-radius: 50%; filter: blur(30px); opacity: 0.5; animation: blobMove 8s infinite alternate; z-index: 0; }
    @keyframes blobMove { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(15px, -20px) scale(1.1); } }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative">

  <div class="blob-bg bg-indigo-200 w-72 h-72 top-10 left-10 z-0"></div>
  <div class="blob-bg bg-purple-200 w-80 h-80 bottom-10 right-10 z-0" style="animation-delay: 2s;"></div>

  <div class="w-full max-w-sm bg-white/90 backdrop-blur-md rounded-3xl shadow-2xl p-8 border border-white relative z-10 animate-slide-up">
     <div class="text-center mb-8">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-2xl mx-auto flex items-center justify-center text-3xl mb-4 shadow-lg"><i class="fas fa-key"></i></div>
        <h1 class="text-2xl font-black text-slate-800 tracking-tight">Set New Password</h1>
        <p class="text-xs text-slate-500 mt-1 font-medium">Buat password baru untuk akun Anda.</p>
     </div>

     <div id="reset-form-container">
         <form onsubmit="event.preventDefault(); submitReset();" class="space-y-5">
            <input type="hidden" id="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
            
            <div>
               <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">New Password</label>
               <div class="relative">
                   <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-lock"></i></span>
                   <input type="password" id="new-pass" class="w-full border border-slate-300 rounded-xl p-3 pl-10 pr-10 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition" required placeholder="******">
                   <button type="button" onclick="togglePass('new-pass', 'icon-pass')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 focus:outline-none transition">
                       <i id="icon-pass" class="fas fa-eye"></i>
                   </button>
               </div>
            </div>
            
            <button type="submit" id="btn-submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-3.5 rounded-xl shadow-md hover:opacity-90 transition transform hover:-translate-y-0.5">
                Simpan Password Baru
            </button>
         </form>
     </div>

     <div id="success-msg" class="hidden text-center animate-slide-up">
         <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
             <i class="fas fa-check text-3xl"></i>
         </div>
         <h3 class="text-xl font-bold text-slate-800 mb-2">Berhasil!</h3>
         <p class="text-sm text-slate-500 mb-6 leading-relaxed">Password Anda telah berhasil diperbarui. Silakan login menggunakan password baru Anda.</p>
         <a href="index.php" class="block w-full bg-slate-800 text-white font-bold py-3.5 rounded-xl hover:bg-slate-900 transition shadow-md">
             Kembali ke Login
         </a>
     </div>
  </div>

  <script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text"; 
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password"; 
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    function submitReset() {
        const token = document.getElementById('token').value;
        const pass = document.getElementById('new-pass').value;
        
        if(!token) { alert("Token link tidak valid atau kosong."); return; }
        if(!pass) { alert("Harap masukkan password baru."); return; }

        const btn = document.getElementById('btn-submit');
        const orgText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';

        fetch('api/auth.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'confirmReset', token: token, newPassword: pass })
        })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = orgText;
            
            if(res.success) {
                // Sembunyikan form, tampilkan pesan sukses
                document.getElementById('reset-form-container').classList.add('hidden');
                document.getElementById('success-msg').classList.remove('hidden');
            } else {
                alert("Gagal: " + res.message);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = orgText;
            alert("Koneksi gagal. Silakan coba lagi.");
        });
    }

    // Cek jika token kosong saat halaman dimuat
    window.onload = function() {
        if(!document.getElementById('token').value) {
            alert("Link Reset Password tidak valid atau sudah kadaluarsa.");
            window.location.href = "index.php";
        }
    }
  </script>
</body>
</html>