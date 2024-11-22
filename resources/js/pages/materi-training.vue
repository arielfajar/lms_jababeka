<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <h3 class="text-center">{{ currentContent?.judul }}</h3>
        <!-- Conditional Rendering for Different Content Types -->
        <v-responsive aspect-ratio="16/9" v-if="currentContent?.jenis === 'youtube'">
          <iframe
            :src="getYoutubeEmbedLink(currentContent?.link)"
            frameborder="0"
            allow="autoplay; encrypted-media"
            allowfullscreen
            width="100%"
            height="500px"
          ></iframe>
        </v-responsive>
        <v-responsive aspect-ratio="16/9" v-else-if="currentContent?.jenis === 'pdf'">
          <iframe :src="getEmbedLink(currentContent?.link)" width="100%" height="500px"></iframe>
        </v-responsive>
        <v-responsive aspect-ratio="16/9" v-else-if="currentContent?.jenis === 'ppt'">
          <iframe :src="getEmbedLink(currentContent?.link)" width="100%" height="500px"></iframe>
        </v-responsive>

        <!-- Navigation Buttons (Back & Next) with equal width -->
        <v-row justify="center" class="mt-4">
          <v-col cols="6">
            <v-btn block color="secondary" @click="handleBack" :disabled="currentContentIndex === 0">
              Kembali
            </v-btn>
          </v-col>
          <v-col cols="6">
            <v-btn block color="primary" @click="handleNext">
              {{ currentContentIndex === contentList.length - 1 ? 'Selesai' : 'Selanjutnya' }}
            </v-btn>
          </v-col>
        </v-row>
      </v-col>
      
      <!-- Title Section (Right) -->
      <v-col cols="12" md="4">
        <v-card class="pa-4">
          <h4>Judul Materi:</h4>
          <v-list>
            <v-list-item
              v-for="(content, index) in contentList"
              :key="index"
              :class="{'active-title': currentContentIndex === index}"
              @click="setContent(index)"
            >
              <v-list-item-content class="d-flex align-center">
                <v-icon left class="mr-2" :class="currentContentIndex === index ? 'bx bx-pause-circle' : 'bx bx-play-circle'"></v-icon>
                <v-list-item-title>{{ content.judul }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2'; // Import SweetAlert2

export default {
  data() {
    return {
      currentContentIndex: 0,
      contentList: [],
      content_id: this.$route.query.content_id || '',
      hasCompletedQuiz: false // Added to track if quiz is completed
    };
  },
  computed: {
    currentContent() {
      return this.contentList[this.currentContentIndex] || {};
    }
  },
  methods: {
    async fetchContent() {
      try {
        const response = await axios.get(`/api/materi/${this.content_id}`);
        this.contentList = response.data;
      } catch (error) {
        console.error('Error fetching content:', error);
      }
    },
    async fetchScores() {
      // Check if content_id is null or empty
      if (!this.content_id) {
        this.hasCompletedQuiz = false; // Allow quiz attempt if content_id is null
        return;
      }

      try {
        // Fetch user ID from API
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const user_id = userResponse.data.user_id;

        // Fetch all scores for the user
        const scoreResponse = await axios.get(`/api/nilai?user_id=${user_id}`);

        // Check if current content_id exists in the fetched scores
        const scores = scoreResponse.data;
        const hasScoreForCurrentContent = scores.some(score => score.content_id == this.content_id);

        this.hasCompletedQuiz = hasScoreForCurrentContent;

      } catch (error) {
        console.error('Error fetching scores:', error);
        this.hasCompletedQuiz = false; // Ensure quiz can be taken if an error occurs
      }
    },
    async saveScore() {
      try {
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const user_id = userResponse.data.user_id;

        const payload = {
          content_id: this.currentContent.content_id,
          user_id: user_id,
          nilai: null,
        };

        await axios.post('/api/nilai', payload);

        Swal.fire({
          icon: 'success',
          title: 'Progress Berhasil Disimpan',
          text: 'Anda bisa melihat konten selanjutnya!',
        });

        // Navigate to training-mandiri/content after saving score
        this.$router.push('/training-mandiri/content');

      } catch (error) {
        console.error('Error saving score:', error);
      }
    },
    async handleNext() {
      // Check if currentContentIndex is the last one
      if (this.currentContentIndex === this.contentList.length - 1) {
        if (this.hasCompletedQuiz) {
          // Redirect if quiz already completed
          this.$router.push('/training-mandiri/content');
        } else {
          // Save score and then redirect
          await this.saveScore();
        }
      } else {
        this.currentContentIndex++;
      }
    },
    handleBack() {
      if (this.currentContentIndex > 0) {
        this.currentContentIndex--;
      }
    },
    setContent(index) {
      this.currentContentIndex = index;
    },
    getEmbedLink(link) {
      return `/storage/${link}`;
    },
    getYoutubeEmbedLink(link) {
      return `${link}?autoplay=1`;
    }
  },
  async created() {
    await this.fetchContent();
    await this.fetchScores(); // Fetch scores on component creation
  }
};
</script>

<style scoped>
h4 {
  margin-bottom: 16px;
}
p {
  font-size: 16px;
  font-weight: bold;
}
.active-title {
background-color: rgba(224, 224, 224, 0.7);
  font-weight: bold;
}
.v-icon {
  font-size: 24px;
  color: #4CAF50; /* Icon color */
  margin-right: 8px;
}
.active-title .v-icon {
  color: #7B68EE; /* Icon color when active */
}
</style>
