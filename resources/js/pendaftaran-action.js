window.handleAction = function(btn, url, type) {
    if (btn.disabled) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const isApprove = type === 'setuju';
    
    // Tampilan Konfirmasi Modern
    Swal.fire({
        title: isApprove ? 'Setujui Pendaftaran?' : 'Tolak Pendaftaran?',
        text: isApprove 
            ? 'Siswa akan diterima sebagai calon peserta didik.' 
            : 'Pendaftaran siswa akan dibatalkan/ditolak.',
        icon: 'warning',
        iconColor: isApprove ? '#10b981' : '#f43f5e',
        showCancelButton: true,
        confirmButtonText: isApprove ? 'Ya, Setujui' : 'Ya, Tolak',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        buttonsStyling: false,
        customClass: {
            container: 'swal-modern-backdrop',
            popup: 'swal-modern-popup',
            title: 'swal-modern-title',
            htmlContainer: 'swal-modern-content',
            confirmButton: `swal-btn ${isApprove ? 'swal-btn-success' : 'swal-btn-danger'}`,
            cancelButton: 'swal-btn swal-btn-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan Loading State di Tombol
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Eksekusi Fetch
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success || data.id_pendaftaran) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Status pendaftaran telah diperbarui.',
                        timer: 1500,
                        showConfirmButton: false,
                        customClass: { popup: 'swal-modern-popup' }
                    }).then(() => window.location.reload());
                } else {
                    throw new Error(data.message || 'Gagal memperbarui status.');
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: err.message,
                    customClass: { popup: 'swal-modern-popup' }
                });
                btn.disabled = false;
                btn.innerHTML = originalContent;
            });
        }
    });
}