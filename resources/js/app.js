import './bootstrap';
import './components/toast';
import './components/kuota-pagination';
import './components/arsip-detail-pagination';
import './components/plan-growth-chart';
import './components/pengaturan-akun';
import './layouts/dashboard';
import './pages/peserta/profil-show';
import './pages/admin/dashboard';
import './pages/admin/kuota-draft';
import { initSuperAdminMotion } from './services/superadmin-motion.js';

if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.body && document.body.classList.contains('role-superadmin')) {
            initSuperAdminMotion('global');
        }
    });
}

