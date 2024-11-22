// Import yang diperlukan
import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';
import axios from 'axios';

// Membuat router
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Navigation Guard untuk mengecek autentikasi dan role_id
router.beforeEach(async (to, from, next) => {
  const publicPages = ['/login']; // Halaman yang tidak memerlukan autentikasi
  const authRequired = !publicPages.includes(to.path); // Memeriksa apakah rute memerlukan autentikasi
  const token = localStorage.getItem('token'); // Ambil token dari localStorage

  // Jika halaman memerlukan autentikasi dan token tidak ada
  if (authRequired && !token) {
    return next('/login'); // Redirect ke halaman login
  }

  // Jika ada token, lakukan verifikasi
  if (token) {
    try {
      const response = await axios.get('/api/user', {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      const user = response.data;
      const roleId = Number(user.role_id); // Ambil role_id dari data user

      // Jika halaman memiliki prefix '/admin' tetapi role_id bukan 1, redirect ke dashboard atau halaman lain
      if (to.path.startsWith('/admin') && roleId !== 1) {
        return next('/dashboard'); // Redirect ke dashboard jika role_id bukan 1
      }

      // Jika token valid, lanjutkan ke rute yang diminta
      return next();
    } catch (error) {
      // Jika token tidak valid, redirect ke login
      return next('/login');
    }
  }

  next(); // Lanjutkan ke rute yang diminta
});

// Ekspor router
export default function (app) {
  app.use(router);
}

export { router };
