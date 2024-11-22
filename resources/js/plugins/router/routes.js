export const routes = [
  { path: '/', redirect: '/login' },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
      },
      {
        path: 'training-mandiri',
        component: () => import('@/pages/training-mandiri.vue'),
      },
      {
        path: 'training-mandiri/content',
        component: () => import('@/pages/content-training.vue'),
      },
      {
        path: 'training-mandiri/content/soal',
        component: () => import('@/pages/soal-training.vue'),
      },
      {
        path: 'training-mandiri/content/materi',
        component: () => import('@/pages/materi-training.vue'),
      },
      {
        path: 'pengajuan',
        component: () => import('@/pages/pengajuan.vue'),
      },
      {
        path: 'pengajuan/form',
        component: () => import('@/pages/formPengajuan.vue'), // Pastikan file ini ada
      },
      {
        path: 'pengajuan/cetak/:pengajuan_id',
        component: () => import('@/pages/cetakPengajuan.vue'), // Pastikan file ini ada
      },
      {
        path: 'explore',
        component: () => import('@/pages/explore-training.vue'), // Pastikan file ini ada
      },
      {
        path: 'explore/content',
        component: () => import('@/pages/content-explore.vue'), // Pastikan file ini ada
      },
      {
        path: 'sertifikat',
        component: () => import('@/pages/sertifikat.vue'),
      },
      {
        path: 'account-settings',
        component: () => import('@/pages/account-settings.vue'),
      },
      {
        path: 'admin/dashboard',
        component: () => import('@/pages/admin/admindashboard.vue'),
      },
      {
        path: 'admin/explore',
        component: () => import('@/pages/admin/explore.vue'),
      },
      {
        path: 'admin/explore/add',
        component: () => import('@/pages/admin/addexplore.vue'),
      },
      {
        path: 'admin/content',
        component: () => import('@/pages/admin/content.vue'),
      },
      {
        path: 'admin/content/soal',
        component: () => import('@/pages/admin/addsoal.vue'),
      },
      {
        path: 'admin/content/materi',
        component: () => import('@/pages/admin/addmateri.vue'),
      },
      {
        path: 'admin/datadaftar',
        component: () => import('@/pages/admin/datadaftar.vue'),  
      },
      {
        path: 'admin/datapengajuan',
        component: () => import('@/pages/admin/datapengajuan.vue'),  
      },
      {
        path: 'admin/reportpengajuan',
        component: () => import('@/pages/admin/laporan_pengajuan.vue'),  
      },
      {
        path: 'admin/hasiltraining',
        component: () => import('@/pages/admin/laporan_hasiltraining.vue'),  
      },
      {
        path: 'admin/ttd',
        component: () => import('@/pages/admin/ttd.vue'),
      },
      {
        path: 'admin/ttd/show',
        component: () => import('@/pages/admin/showttd.vue'),
      },
      {
        path: 'admin/karyawan',
        component: () => import('@/pages/admin/karyawan.vue'),
      },
      {
        path: 'admin/addkaryawan',
        component: () => import('@/pages/admin/addkaryawan.vue'),
      },
      {
        path: 'admin/department',
        component: () => import('@/pages/admin/unit.vue'),
      },
      
      {
        path: 'hod/dashboard',
        component: () => import('@/pages/hod/hoddashboard.vue'),
      },
      {
        path: 'hod/datadaftar',
        component: () => import('@/pages/hod/datadaftar.vue'),  
      },
      {
        path: 'hod/datapengajuan',
        component: () => import('@/pages/hod/datapengajuan.vue'),  
      },
      
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
      },
      {
        path: 'cards',
        component: () => import('@/pages/cards.vue'),
      },
      {
        path: 'tables',
        component: () => import('@/pages/tables.vue'),
      },
      {
        path: 'form-layouts',
        component: () => import('@/pages/form-layouts.vue'),
      },
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'login',
        component: () => import('@/pages/login.vue'),
      },
      {
        path: 'register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
