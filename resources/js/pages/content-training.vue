<template>
  <v-container>
    <!-- Bagian Title -->
    <v-row>
      <v-col cols="12" class="d-flex justify-center align-center title-container" :style="backgroundImageStyle">
        <h1 class="title">{{ title }}</h1>
      </v-col>
    </v-row>
      <!-- Bagian Isi Konten -->
      <v-row class="mt-5">
        <v-col cols="12" md="8">
          <div class="content-section">
            <ul class="content-list">
              <li
                v-for="(content, index) in contents"
                :key="content.content_id"
                class="item-wrapper"
              >


          <!-- Konten item -->
          <div :class="getItemClass(content, index)" @click="navigateTo(content)">
            <div 
              :style="{ 
                backgroundColor: getIconClass(content.jenis).backgroundColor, 
                color: getIconClass(content.jenis).color,
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'center',
                borderRadius: '30%',
                width: '35px',
                height: '35px',
                marginRight: '8px'
              }">
              <span :class="getIconClass(content.jenis).iconClass" :style="{ fontSize: '28px' }"></span>
            </div>
            <div class="content-details">
              <div class="content-title">{{ content.judul }}</div>
              <div class="content-nilai" v-if="content.nilai !== null && (content.jenis === 1 || content.jenis === 2 || content.jenis === 4)">
                <span class="score-label">Nilai Anda :</span>
                <span class="score-circle">{{ content.nilai }}</span>
              </div>
              <span v-if="(content.progress === null && (index !== 0 || (index === 0 && contents[0].progress !== null)))" class="lock-icon">
                <div class="lock-container">
                  <i class="bx bx-lock" style="font-size: 17px;"></i>
                  <span class="lock-text">Konten ini masih terkunci</span>
                </div>
              </span>
            </div>
          </div>
              <!-- Ikon Kunci di luar item-container -->
              <div class="icon-container">
                <div :class="['icon-wrapper', { 'checked-icon': content.nilai !== null }]"> <!-- New wrapper for icons -->
                  <span 
                    :class="{
                      'bx': true,
                      'bx-lock': content.progress === null && content.nilai === null,
                      'bx-lock-open': content.progress !== null && content.nilai === null,
                      'bx-checkbox-checked': content.nilai !== null
                    }"
                    class="lock-icon"
                    style="font-size: 27px;"
                  ></span>
                </div>
                <!-- <div class="line" :style="{ height: `${lineHeight}px` }" v-if="index < contents.length - 1"></div> -->
              </div>
        </li>
      </ul>
    </div>
  </v-col>

  <!-- Container Informasi Ikon -->
      <v-col cols="12" md="4"> <!-- This column will hold the information about icons -->
        <div class="info-container">
          <v-alert type="info" color="blue" dark class="text-center mb-4">
            <h4>Information</h4>
          </v-alert>
          <ul class="info-list">
            <li>
              <div class="info-item">
                <div class="icon-info" style="background-color: rgba(211, 211, 211, 1);">
                  <span class="bx bx-lock" style="font-size: 21px; color: #000;"></span>
                </div>
                <div class="info-text">
                  Konten terkunci dan belum dapat diakses.
                </div>
              </div>
            </li>
            <li>
              <div class="info-item">
                <div class="icon-info" style="background-color: rgba(76, 175, 80, 0.3);">
                  <span class="bx bx-lock-open" style="font-size: 21px; color: #000;"></span>
                </div>
                <div class="info-text">
                  Konten sudah dapat diakses.
                </div>
              </div>
            </li>
            <li>
              <div class="info-item">
                <div class="icon-info" style="background-color: rgba(255, 255, 0, 0.7);">
                  <span class="bx bx-checkbox-checked" style="font-size: 28px; color: #000;"></span>
                </div>
                <div class="info-text">
                  Konten sudah dikerjakan atau ditonton.
                </div>
              </div>
            </li>
          </ul>
          <v-divider class="my-4"></v-divider>
          <div class="info-notes">
              <p><strong>Notes :</strong> Untuk membuka konten yang terkunci, anda harus menyelesaikan konten yang sudah dapat diakses terlebih dahulu!</p>
          </div>
        </div>
      </v-col>
</v-row>

  </v-container>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2
import Breadcrumb from '@/views/dashboard/Breadcrumb.vue';

export default {
  components: {
    Breadcrumb, // Register the Breadcrumb component
  },
  data() {
    return {
      breadcrumbItems: [
        { text: 'Dashboard', link: '/dashboard' },
        { text: 'My Training', link: '/training-mandiri' },
        { text: 'Konten', link: '/training-mandiri/content' },
      ],
      title: localStorage.getItem('contentTitle'), // Get title from localStorage
      image: localStorage.getItem('contentImage'), // Get image from localStorage
      contents: [],
      explore_id: localStorage.getItem('exploreId'), // Get explore_id from localStorage
      unlockedFirstNull: false, 
      sertifikatGiven: localStorage.getItem('sertifikatGiven') || false, // Track if sertifikat is given
    };
  },
  computed: {
      lineHeight() {
    return (this.contents.length - 1) * 53; // Assuming each item has a height of 50px (adjust as needed)
  },
    backgroundImageStyle() {
      return {
        backgroundImage: `url(${this.image})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        height: '160px',
        position: 'relative',
        border: '1px solid #000000', // Add your desired border color and size here
      borderRadius: '10px'
      };
    }
  },
  methods: {
    async fetchContents() {
      try {
        const response = await axios.get(`/api/content/${this.explore_id}`);
        this.contents = response.data;
        await this.fetchScores(); // Fetch scores for each content
      } catch (error) {
        console.error('Error fetching contents:', error);
      }
    },
    async fetchScores() {
      try {
        // Fetch user ID from the API
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        
        const user_id = userResponse.data.user_id; // Get the user ID

        // Fetch scores for the user
        const scoreResponse = await axios.get(`/api/nilai?user_id=${user_id}`);
        const scores = scoreResponse.data;

        // Update contents with scores
        this.contents = this.contents.map(content => {
          const scoreData = scores.find(score => score.content_id === content.content_id);
          return {
            ...content,
            nilai: scoreData ? scoreData.nilai : null,
            progress: scoreData ? scoreData.progress : null, // Adjust this based on your API response
          };
        });

        // Unlock the first content item with null progress
        this.unlockFirstNullProgress();

        // Cek dan simpan sertifikat jika belum diberikan
        this.checkAndSaveSertifikat(user_id);
        
      } catch (error) {
        console.error('Error fetching scores:', error);
      }
    },

    unlockFirstNullProgress() {
      const firstNullIndex = this.contents.findIndex(content => !content.progress);
      if (firstNullIndex !== -1 && !this.unlockedFirstNull) {
        this.contents[firstNullIndex].progress = true; // Unlock progress
        this.unlockedFirstNull = true; // Prevent further unlocking
      }
    },

    async checkAndSaveSertifikat(user_id) {
  try {
    // Check if the certificate already exists
    const existsResponse = await axios.get(`/api/sertifikat/exists`, {
      params: {
        user_id: user_id,
        explore_id: this.explore_id,
      },
    });

    if (existsResponse.data.exists) {
      // If the certificate exists, don't send it again
      return;
    }

    // If the certificate doesn't exist, check if it qualifies for a new certificate
    const sertifikatContent = this.contents.find(content => content.jenis === 4 && content.nilai !== 0 && content.nilai !== null);
    
    if (sertifikatContent) {
      // Save certificate to the database
      await axios.post('/api/sertifikat', {
        explore_id: this.explore_id,
        user_id: user_id,
        tanggal: new Date().toISOString().slice(0, 10),
      });

      // Show success alert
      Swal.fire({
        icon: 'success',
        title: 'Selamat!',
        text: 'Anda mendapatkan sertifikat!',
      }).then(() => {
        // Redirect to /sertifikat after the alert is closed
        this.$router.push('/sertifikat');
      });

      // No need for localStorage to track anymore
    }
  } catch (error) {
    console.error('Error checking or saving sertifikat:', error);
  }
},


    navigateTo(content) {
      if (content.progress) {
        const path = content.jenis === 3 ? 'content/materi' : 'content/soal';
        this.$router.push({ path, query: { content_id: content.content_id } });
      }
    },

  getIconClass(jenis) {
    let iconClass = '';
    let color = '';
    let backgroundColor = '';

    // Map `jenis` to icons, text color, and background color
    switch (jenis) {
      case 1:
        iconClass = 'bx bx-clipboard';
        color = ''; 
        backgroundColor = 'rgba(190, 191, 61, 0.5)'; // Blue background for primary
        break;
      case 2:
        iconClass = 'bx bx-notepad';
        color = ''; 
        backgroundColor = 'rgba(190, 191, 61, 0.5)'; // Green background for success
        break;
      case 3:
        iconClass = 'bx bx-book-bookmark';
        color = ''; 
        backgroundColor = 'rgba(190, 191, 61, 0.5)'; // Light blue background for info
        break;
      case 4:
        iconClass = 'bx bx-task';
        color = ''; 
        backgroundColor = 'rgba(190, 191, 61, 0.5)'; // Red background for error
        break;
      default:
        iconClass = '';
        color = '#000000'; 
        backgroundColor = '#D3D3D3'; // Gray background as default
    }

    return {
      iconClass,
      color,
      backgroundColor,
    };
  },
    
    getItemClass(content, index) {
      return {
        'item-container': true,
        'locked-item': content.progress === null && (index !== 0 || (index === 0 && this.contents[0].progress !== null)),
      };
    },
  },

  created() {
    this.fetchContents();
  },
  
  beforeDestroy() {
    localStorage.removeItem('contentTitle');
    localStorage.removeItem('contentImage');
    localStorage.removeItem('exploreId');
  }
};
</script>


<style scoped>
.title-container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.title {
  background-color: rgba(0, 0, 0, 0.7);
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
.content-title {
  font-weight: 600; /* Make title bold */
  margin-top: 5px;
}
.content-section {
  margin-top: 20px;
}
.content-list {
  padding-left: 0;
  list-style: none; /* Menghilangkan bullet points */
}
.content-details {
  justify-content: space-between; /* Space between title and lock icon */
  align-items: center;
}
.content-nilai {
  margin-top: 16px;
  border: 1px solid #ddd; /* Light gray border */
  border-radius: 8px; /* Rounded corners */
  padding: 8px; /* Space inside the border */
  background: rgba(39, 245, 208, 0.8);
}

.content-title {
  flex-grow: 1; /* Allow title to take space */
}
.item-wrapper {
  display: flex; /* Use Flexbox to align items horizontally */
  align-items: center; /* Center items vertically */
  margin-bottom: 16px;
}
.item-container {
  display: flex;
  align-items: flex-start;
  margin-bottom: 10px;
  background: rgba(1, 228, 0, 0.1);
  padding: 10px; /* Padding for each item */
  border: 1px solid rgba(1, 228, 0, 0.2);
  border-radius: 8px; /* Rounded corners for each item */
  cursor: pointer; /* Pointer cursor to indicate clickable items */
  flex-grow: 1;
}
.item-container i {
  font-size: 24px;
  color: #333;
  margin-right: 12px;
}
.icon-container {
  position: relative; /* Position the container */
  display: flex;
  align-items: center; /* Center icons vertically */
}
.icon-wrapper {
  width: 25px; /* Adjust the width as needed */
  height: 25px; /* Adjust the height as needed */
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative; /* This allows the line to be positioned relatively */
  z-index: 2; /* Ensure it's above the line */
  background-color: rgba(211, 211, 211, 1); /* Example: white with slight transparency */
  border-radius: 5px;
  margin-left: 10px;
}
.checked-icon {
  background-color: yellow; /* Warna kuning untuk icon yang memiliki nilai */
  opacity: 0.7;
}


.line {
  width: 2px; /* Width of the vertical line */
  background-color: rgba(51, 51, 51, 0.5);
  position: absolute; /* Position it absolutely within the container */
  left: 30%; /* Center the line horizontally */
  top: 0; /* Start from the top */
}

.score-label {
  font-weight: bold; /* Make the label bold */
  font-size: 14px;
}
.score-circle {
  display: inline-block;
  width: 40px; /* Increased width */
  height: 40px; /* Increased height */
  border-radius: 50%;
  background-color: #4caf50; /* Green background for score */
  color: white;
  text-align: center;
  line-height: 40px; /* Center the number vertically */
  margin-left: 12px;
  font-size: 16px; /* Increase font size if needed */
}
.lock-icon {
  z-index: 1;
}

.lock-container {
  display: flex; /* Use Flexbox for centering */
  flex-direction: column; /* Arrange items in a column */
  align-items: center; /* Center items horizontally */
  text-align: center; /* Center text */
  margin-top: 10px; 
}

.lock-text {
  display: block; /* To make it occupy a new line */
  font-size: 9px; /* Font size for the lock text */
  color: #666; /* Color for the lock text */
  margin-top: 4px; /* Space between lock icon and text */
  text-align: center; /* Center align the text */
}
.locked-item {
  background-color: #A9A9A9; /* Change to your desired color for locked items */
}
.info-container {
  margin-top: 20px;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: rgba(249, 249, 249, 0.4);
}
.info-title {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 12px;
}
.info-list {
  list-style: none;
  padding-left: 0;
}
.info-item {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}
.icon-info {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}
.info-text {
  font-size: 14px;
}
</style>
