<template>
  <v-container>
   <v-row class="d-flex justify-center">
        <v-col cols="12" md="16">
          <v-container class="training-title-container">
           <v-row align="center" justify="space-between">
            <h1 class="training-title"><i class="bx bx-award"></i>Sertifikat Anda</h1>
            <Breadcrumb :items="breadcrumbItems" class="breadcrumb-style" />
            </v-row>
          </v-container>
        </v-col>
      </v-row>
    <v-row>
      <v-col
        v-for="sertifikat in sertifikats"
        :key="sertifikat.id"
        style="flex: 0 0 300px; max-width: 300px; height: 350px; margin-left: 16px;"
      >
        <v-card style="border-radius: 16px; overflow: hidden;">
          <!-- Only this part will be captured for download -->
          <v-img
            :id="`sertifikat-content-${sertifikat.id}`"
            :src="sertifikatImage"
            height="200px"
            class="white--text"
            style="background-size: cover; background-position: center; cursor: pointer;"
            @click="showImagePreview(sertifikat.user.nama_lengkap, sertifikat.explore.judul, sertifikat.tanggal, sertifikatImage)" 
          >
            <v-container fluid class="d-flex justify-center align-center pa-2" style="height: 249px;">
              <div class="text-center responsive-text" style="font-family: 'Droid Serif', serif; text-transform: uppercase; color: white;">
                <h2 class="mb-3" style="font-size: 12px;">{{ sertifikat.user.nama_lengkap }}</h2>
                <h3 class="mb-1" style="font-size: 7px;">{{ sertifikat.explore.judul }}</h3>
                <h3 class="text-sm mb-0" style="font-size: 6px;">{{ sertifikat.tanggal }}</h3>
                <v-row justify="center">
                  <v-col cols="auto">
                    <v-img 
                      v-if="ttds.length > 0" 
                      :src="`/storage/${ttds[0].img}`" 
                      height="20px"
                      width="20px"
                    ></v-img>
                  </v-col>
                </v-row>

                <h3 v-if="ttds.length > 0" style="font-size: 5px; text-transform: none;">{{ ttds[0].nama }}</h3>
                <h4 v-if="ttds.length > 0" style="font-size: 3px; text-transform: none; font-weight: normal;">{{ ttds[0].jabatan }}</h4>
              </div>
            </v-container>
          </v-img>

          <!-- Excluded part from screenshot -->
          <v-card-title>{{ sertifikat.explore.judul }}</v-card-title>
          <v-card-subtitle>{{ sertifikat.tanggal }}</v-card-subtitle>
          <v-card-actions>
            <v-btn
              color="primary"
              @click="downloadImage(sertifikat.id)"
            >
              Unduh Gambar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialog for image preview -->
    <v-dialog v-model="dialog" max-width="600">
          <v-card>
    <v-img :src="previewImage" height="400px" class="white--text">
        <v-container fluid class="d-flex justify-center align-center pa-2" style="height: 503px;">
          <div class="text-center responsive-text" style="font-family: 'Droid Serif', serif; text-transform: uppercase;">
            <h2 class="mb-6">{{ previewUser }}</h2>
            <h2 class="mb-4" style="font-size: 16px;">{{ previewTitle }}</h2>
            <h2 class="text-sm mb-6" style="font-size: 10px;">{{ previewDate }}</h2>
            <v-img
              v-if="ttds.length > 0"
              :src="`/storage/${ttds[0].img}`"
              height="30px"
            ></v-img>
            <h3 v-if="ttds.length > 0" style="font-size: 8px; text-transform: none;">{{ ttds[0].nama }}</h3>
            <h1 v-if="ttds.length > 0" style="font-size: 7px; text-transform: none; font-weight: normal;">{{ ttds[0].jabatan }}</h1>
          </div>
        </v-container>
      </v-img>
        <v-card-title class="headline">{{ previewTitle }}</v-card-title>
        <v-card-subtitle>{{ previewUser }}</v-card-subtitle>
        <v-card-subtitle>{{ previewDate }}</v-card-subtitle>
        <v-card-actions>
          <v-btn color="primary" text @click="dialog = false">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import Breadcrumb from '@/views/dashboard/Breadcrumb.vue';
import html2canvas from 'html2canvas';
import axios from 'axios';
import sertifikatImage from '@images/pages/sertifikat_lms.png'; // Adjust the path to your image

export default {
  components: {
    Breadcrumb, // Register the Breadcrumb component
  },
  data() {
    return {
       breadcrumbItems: [
        { text: 'Dashboard', link: '/dashboard' },
        { text: 'Sertifikat', link: '/sertifikat' },
      ],
      sertifikats: [],
      sertifikatImage,
      dialog: false,
      previewImage: '',
      previewTitle: '',
      previewDate: '',
      previewUser: '',
      ttds: [],
    };
  },
  created() {
    this.fetchUserIdAndSertifikats();
    this.fetchTtds();
  },
  methods: {
    async fetchUserIdAndSertifikats() {
      try {
        // Fetch user ID from the /api/user endpoint
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        
        const user_id = userResponse.data.user_id; // Get user ID
        
        // Now fetch sertifikats using the obtained user ID
        await this.fetchSertifikats(user_id);
      } catch (error) {
        console.error('Error fetching user ID:', error);
      }
    },
    async fetchSertifikats(user_id) {
      try {
        const response = await axios.get('/api/sertifikat', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
          params: {
            user_id, // Use the user ID for fetching sertifikats
          },
        });
        this.sertifikats = response.data.map(sertifikat => ({
          id: sertifikat.sertifikat_id,
          explore: sertifikat.explore,
          tanggal: sertifikat.tanggal,
          user: sertifikat.user,
        }));
        console.log('Fetched sertifikats:', this.sertifikats);
      } catch (error) {
        console.error('Error fetching sertifikats:', error);
      }
    },
    async fetchTtds() {
      try {
        const response = await axios.get('/api/ttd');
        this.ttds = response.data;
      } catch (error) {
        console.error('Error fetching TTDs:', error);
      }
    },
    downloadImage(sertifikatId) {
      // Find the specific element for capture
      const element = document.getElementById(`sertifikat-content-${sertifikatId}`);
      
      // Use html2canvas to capture content, increasing the quality by setting a higher scale
      html2canvas(element, { useCORS: true, scale: 10 }).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `Sertifikat-${sertifikatId}.png`;
        link.click();
      }).catch(error => {
        console.error('Error capturing the image:', error);
      });
    },
    showImagePreview(user, title, date, image) {
      this.previewImage = image;
      this.previewTitle = title;
      this.previewDate = date;
      this.previewUser = user;
      this.dialog = true;
    }
  },
};
</script>

<style scoped>
.responsive-text {
  font-family: 'Droid Serif', serif;
  text-transform: uppercase;
  color: white;
}
.training-title i {
  margin-right: 10px; /* Berikan jarak 10px antara ikon dan teks */
}
</style>
