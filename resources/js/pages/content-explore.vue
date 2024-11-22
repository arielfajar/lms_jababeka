<template>
  <v-container>
    <!-- Bagian Title -->
    <v-row>
      <v-col
        cols="12"
        class="d-flex justify-center align-center title-container"
        :style="backgroundImageStyle"
      >
        <h1 class="title">{{ exploreData.judul || 'Loading...' }}</h1>
      </v-col>
    </v-row>

    <!-- Spacing antara Title dan konten utama -->
    <v-row class="mt-9">
      <v-col cols="12" md="8">
        <!-- Tentang Training Ini dan Objective tanpa jarak antar mereka -->
        <v-row>
          <v-col cols="12" class="krem-background">
            <h2>Tentang Training Ini</h2>
            <p>{{ exploreData.tentang || 'Loading...' }}</p>
          </v-col>
        </v-row>
        <v-row class="mt-3"> <!-- Hilangkan margin-top di sini agar dempet -->
          <v-col cols="12" class="krem-background">
            <h2>Objective</h2>
            <p>{{ exploreData.objective || 'Loading...' }}</p>
          </v-col>
        </v-row>

        <!-- Course Content -->
        <v-row class="mt-3">
          <v-col cols="12" class="krem-background">
            <h2>Course Content</h2>
            <p>{{ exploreData.course || 'Loading...' }}</p>
          </v-col>
        </v-row>

        <!-- Trainer -->
        <v-row class="mt-3">
          <v-col cols="12" class="krem-background">
            <h2>Trainer</h2>
            <p>{{ exploreData.trainer || 'Loading...' }}</p>
          </v-col>
        </v-row>
      </v-col>

      <!-- Bagian Abu-Abu dengan Button dan Total Pendaftar -->
      <v-col cols="12" md="4" class="d-flex align-center justify-center">
        <div class="abu-abu-background">
          <div class="button-container text-center">
            <v-btn color="primary" class="mb-1" @click="gabungTraining">Gabung Training Ini</v-btn>
          </div>
          <div class="total-pendaftar text-center mb-2">
            <span>Total Pendaftar: 0</span>
          </div>
          <!-- Informasi tambahan mengenai training dengan icon bx -->
          <div class="training-details text-center mt-15">
            <strong> Training Ini Terdiri Dari:</strong>
            <ul>
              <li><i class="bx bx-check"></i> Pre Test</li>
              <li><i class="bx bx-play-circle"></i> Materi </li>
              <li><i class="bx bx-check-circle"></i> Post Test</li>
              <li><i class="bx bx-award"></i> Sertifikat</li>
            </ul>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      exploreData: {}, // Initial empty object to hold explore data
      image: this.$route.query.image,
      exploreId: this.$route.query.explore_id, // Retrieve explore_id from query
    };
  },
  computed: {
    backgroundImageStyle() {
      return {
        backgroundImage: `url(${this.getImageUrl(this.image)})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        height: '160px',
        position: 'relative',
         border: '1px solid #000000', // Add your desired border color and size here
      borderRadius: '10px'
      };
    },
  },
  methods: {
    async fetchExploreData() {
      try {
        const response = await axios.get(`/api/explore/${this.exploreId}`); 
        this.exploreData = response.data; // Assign the fetched data to exploreData
      } catch (error) {
        console.error('Error fetching explore data:', error);
      }
    },
async gabungTraining() {
  try {
    // Ambil user_id dari API user
    const userResponse = await axios.get('/api/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    const user_id = userResponse.data.user_id;
    console.log("User ID:", user_id);

    // Ambil daftar pendaftaran user berdasarkan user_id
    const daftarResponse = await axios.get(`/api/daftar/${user_id}`);
    const userTrainings = daftarResponse.data;

    // Cek apakah user sudah terdaftar pada explore_id ini
    const sudahMendaftar = userTrainings.some(training => String(training.explore_id) === String(this.exploreId));

    if (sudahMendaftar) {
      // Jika sudah terdaftar, tampilkan alert dan hentikan proses
      Swal.fire({
        title: 'Info',
        text: 'Anda sudah mendaftar training ini',
        icon: 'info',
        confirmButtonText: 'OK'
      });
      return; // Pastikan proses berhenti di sini jika sudah mendaftar
    }

    // Jika belum terdaftar, kirim request untuk bergabung ke training
    const response = await axios.post('/api/daftar', {
      user_id: user_id,
      explore_id: this.exploreId,
    });

    Swal.fire({
      title: 'Berhasil!',
      text: 'Anda berhasil bergabung ke training ini',
      icon: 'success',
      confirmButtonText: 'OK'
    }).then(() => {
      this.$router.push('/training-mandiri');
    });
  } catch (error) {
    console.error('Error joining training:', error);
    Swal.fire({
      title: 'Terjadi kesalahan!',
      text: 'Terjadi kesalahan saat bergabung.',
      icon: 'error',
      confirmButtonText: 'OK'
    });
  }
},

    getImageUrl(imagePath) {
      // Construct the full URL for the image
      return `/storage/${imagePath}`;
    },
  },
  mounted() {
    this.fetchExploreData(); // Fetch data when the component is mounted
  },
};
</script>

<style scoped>
.title-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.title {
  background-color: rgba(0, 0, 0, 0.3);
  color: white;
  z-index: 1;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
   padding: 10px 20px; /* Add some padding */
  border-radius: 50px;
}

/* Beri jarak antara title dan konten utama */
.mt-5 {
  margin-top: 40px;
}

.krem-background {
  background-color: rgba(255, 255, 210, 0.4);
  padding: 20px;
}

/* Hilangkan jarak atas di antara Tentang Training dan Objective */
.mt-0 {
  margin-top: 0;
}

.mt-2 {
  margin-top: 16px;
}

.mt-4 {
  margin-top: 32px;
}

.abu-abu-background {
  background-color: rgba(211, 211, 211, 0.8);
  padding: 20px;
  border-radius: 8px;
  max-width: 300px;
  overflow: auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.button-container {
  margin-bottom: 0; /* Kurangi jarak antara tombol dan total pendaftar */
}

.total-pendaftar {
  margin-top: 4px; /* Sesuaikan agar lebih dekat dengan button */
  margin-bottom: 16px; /* Tambahkan jarak dari Training Ini Terdiri Dari */
}

.training-details ul {
  padding-left: 0;
  margin-top: 8px;
  list-style: none; /* Hilangkan bullet points */
}

.training-details ul li {
  margin-bottom: 8px;
  font-size: 16px;
}

.training-details ul li i {
  font-size: 20px;
  color: #333;
  margin-right: 8px;
}

.training-details i.bxs-book {
  font-size: 24px;
  color: #333;
  margin-right: 8px;
}

h1,
h2 {
  margin-bottom: 20px;
}
</style>
