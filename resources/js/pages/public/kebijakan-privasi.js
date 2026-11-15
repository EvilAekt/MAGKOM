document.addEventListener('DOMContentLoaded', () => {
    const panePrivasi = document.getElementById('panePrivasi');
    const paneSyarat = document.getElementById('paneSyarat');
    const btnPrivasi = document.getElementById('tabBtnPrivasi');
    const btnSyarat = document.getElementById('tabBtnSyarat');

    if (!panePrivasi || !paneSyarat || !btnPrivasi || !btnSyarat) {
        return;
    }

    const activeClasses = 'bg-white text-blue-700 shadow-sm border border-slate-200';
    const inactiveClasses = 'text-slate-600 hover:text-blue-600 hover:bg-white/60';
    const baseClasses = 'flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-center transition-all flex items-center justify-center gap-2';

    function switchDocTab(tab) {
        if (tab === 'syarat') {
            panePrivasi.classList.add('hidden');
            paneSyarat.classList.remove('hidden');
            btnPrivasi.className = `${baseClasses} ${inactiveClasses}`;
            btnSyarat.className = `${baseClasses} ${activeClasses}`;
            try {
                history.replaceState(null, '', '#syarat');
            } catch (e) {}
        } else {
            paneSyarat.classList.add('hidden');
            panePrivasi.classList.remove('hidden');
            btnSyarat.className = `${baseClasses} ${inactiveClasses}`;
            btnPrivasi.className = `${baseClasses} ${activeClasses}`;
            try {
                history.replaceState(null, '', '#privasi');
            } catch (e) {}
        }
    }

    btnPrivasi.addEventListener('click', () => switchDocTab('privasi'));
    btnSyarat.addEventListener('click', () => switchDocTab('syarat'));

    if (window.location.hash === '#syarat') {
        switchDocTab('syarat');
    }
});
