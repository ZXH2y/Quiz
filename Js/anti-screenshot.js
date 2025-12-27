// Anti-Screenshot Protection
(function() {
    // Deteksi screenshot dengan cara menyembunyikan konten saat blur/visibility change
    let originalContent = document.body.innerHTML;
    let isHidden = false;

    function hideContent() {
        if (!isHidden) {
            document.body.style.opacity = '0';
            document.body.style.filter = 'blur(50px)';
            document.body.style.backgroundColor = '#000'; 
            isHidden = true;
        }
    }

    function showContent() {
        if (isHidden) {;
            document.body.style.opacity = '1';
            document.body.style.filter = 'none';
            isHidden = false;
        }
    }

    // Deteksi kombinasi tombol screenshot
    document.addEventListener('keyup', function(e) {
        // Print Screen
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            hideContent();
            navigator.clipboard.writeText('Screenshot tidak diizinkan pada test ini!');
            alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
            setTimeout(showContent, 100);
        }
    });

document.addEventListener('keydown', function(e) {
        // Print Screen (semua variasi)
    if (e.key === 'PrintScreen' || e.code === 'PrintScreen' || e.keyCode === 44) {
        e.preventDefault();
        hideContent();
        alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
        setTimeout(showContent, 100);
        return false;
    }
    
    // Shift + Print Screen (Ubuntu area screenshot)
    if (e.shiftKey && (e.key === 'PrintScreen' || e.code === 'PrintScreen')) {
        e.preventDefault();
        hideContent();
        alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
        setTimeout(showContent, 100);
        return false;
    }
    
    // Ctrl + Print Screen (Ubuntu clipboard screenshot)
    if (e.ctrlKey && (e.key === 'PrintScreen' || e.code === 'PrintScreen')) {
        e.preventDefault();
        hideContent();
        alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
        setTimeout(showContent, 100);
        return false;
    }
    
    // Ctrl + Shift + Print Screen (Ubuntu window screenshot)
    if (e.ctrlKey && e.shiftKey && (e.key === 'PrintScreen' || e.code === 'PrintScreen')) {
        e.preventDefault();
        hideContent();
        alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
        setTimeout(showContent, 100);
        return false;
    }
    
    if (e.altKey && (e.key === 'PrintScreen' || e.code === 'PrintScreen')) {
        e.preventDefault();
        hideContent();
        alert('⚠️ Screenshot tidak diizinkan selama test berlangsung!');
        setTimeout(showContent, 100);
        return false;
    }
});

        // Deteksi blur (kemungkinan screenshot tool aktif)
        window.addEventListener('blur', function() {
            hideContent(); // Langsung hide tanpa delay
        });

        window.addEventListener('focus', function() {
            showContent();
        });

        // Deteksi Print Screen dengan cara monitoring clipboard
        let lastClipboard = '';
        setInterval(async function() {
            try {
                const clipboardText = await navigator.clipboard.readText();
                if (clipboardText !== lastClipboard && clipboardText.includes('Screenshot')) {
                    hideContent();
                    alert('⚠️ Screenshot terdeteksi! Tindakan ini akan dilaporkan.');
                    setTimeout(showContent, 2000);
                }
                lastClipboard = clipboardText;
            } catch (e) {
                // Clipboard access blocked, itu bagus
            }
        }, 500);

    // Deteksi visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            hideContent();
        } else {
            setTimeout(showContent, 100);
        }
    });

    // Disable context menu (klik kanan)
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        alert('⚠️ Klik kanan tidak diizinkan selama test berlangsung!');
        return false;
    });

    // Tambahan: Deteksi dev tools (optional)
    let devtoolsOpen = false;
    const threshold = 160;
    
    setInterval(function() {
        if (window.outerWidth - window.innerWidth > threshold || 
            window.outerHeight - window.innerHeight > threshold) {
            if (!devtoolsOpen) {
                devtoolsOpen = true;
                hideContent();
                alert('⚠️ Developer tools terdeteksi! Mohon tutup untuk melanjutkan.');
            }
        } else {
            if (devtoolsOpen) {
                devtoolsOpen = false;
                showContent();
            }
        }
    }, 500);

    // CSS tambahan untuk mencegah selection
    const style = document.createElement('style');
    style.textContent = `
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        body {
            transition: opacity 0.1s, filter 0.1s;
        }
    `;
    document.head.appendChild(style);
})();