<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="12">
        <!-- Check if quiz is already completed -->
        <div v-if="hasCompletedQuiz" class="text-center mb-5">
          <v-alert type="info" color="blue" dark>
            Anda Sudah Menyelesaikan Soal Ini.
          </v-alert>
        </div>

        <!-- Countdown Timer -->
        <v-row class="mb-2" justify="center" v-if="!hasCompletedQuiz">
          <v-chip color="green" class="white--text pa-2">
          <v-icon left>bx-timer</v-icon>
            Waktu tersisa: {{ formattedTime }}
          </v-chip>
        </v-row>

        <!-- Main Quiz Section with Question and Options -->
        <v-row>
          <!-- Question and Options Section -->
          <v-col cols="12" md="8">
            <v-card class="pa-4" v-if="soalList.length > 0">
              <h4  style="margin-bottom: 10px;">{{ currentQuestion + 1 }}) {{ soalList[currentQuestion].pertanyaan }}</h4>
              <v-radio-group v-model="userAnswers[currentQuestion]" :disabled="hasCompletedQuiz">
                <v-radio
                  v-for="(option, idx) in soalList[currentQuestion].jawaban"
                  :key="idx"
                  :label="option"
                  :value="option"
                  style="margin-bottom: 7px;"
                >
                <template v-slot:label>
                  <span>
                    {{ option }}
                    <v-icon v-if="hasCompletedQuiz" :color="isCorrectAnswer(currentQuestion) && userAnswers[currentQuestion] === option ? 'green' : (userAnswers[currentQuestion] === option ? 'red' : 'grey')">
                      {{ isCorrectAnswer(currentQuestion) && userAnswers[currentQuestion] === option ? 'bx-check-circle' : (userAnswers[currentQuestion] === option ? 'bx-x-circle' : '') }}
                    </v-icon>
                  </span>
                </template>
                </v-radio>
              </v-radio-group>
              <div v-if="hasCompletedQuiz" class="mt-4">
              <span>
                <strong>Jawaban Benar :</strong> {{ soalList[currentQuestion].jawaban_benar }}
              </span>
            </div>

              <!-- Navigation Buttons below the Question and Options -->
              <v-row class="mt-4" justify="space-between">
                <v-col cols="6">
                  <v-btn @click="prevQuestion" :disabled="currentQuestion === 0" color="secondary" block>
                    Kembali
                  </v-btn>
                </v-col>
                <v-col cols="6">
                  <v-btn @click="nextQuestion" :disabled="currentQuestion === soalList.length - 1" color="primary" block>
                    Selanjutnya
                  </v-btn>
                </v-col>
              </v-row>
            </v-card>
          </v-col>

          <!-- Question Numbers and Submit Button Section -->
          <v-col cols="12" md="4">
            <v-card class="pa-4">
              <h4>Nomor Soal:</h4>
              <v-row justify="start" align="center" style="flex-wrap: wrap;">
                <v-col cols="auto" v-for="(soal, index) in soalList" :key="index" class="text-center" style="max-width: 18%;">
                  <v-btn
                    :class="{
                      'active-question': currentQuestion === index,
                      'correct-answer': hasCompletedQuiz && isCorrectAnswer(index) && currentQuestion !== index,
                      'wrong-answer': hasCompletedQuiz && !isCorrectAnswer(index) && userAnswers[index] !== undefined && currentQuestion !== index,
                      'answered-question': !hasCompletedQuiz && userAnswers[index] !== undefined && currentQuestion !== index, // warna kuning untuk soal yang sudah dijawab tapi belum di-submit
                      'grey-button': !hasCompletedQuiz && currentQuestion !== index
                    }"
                    class="mx-1"
                    style="margin-bottom: -10px;"
                    @click="currentQuestion = index"
                    outlined
                    block
                  >
                    {{ index + 1 }}
                  </v-btn>
                </v-col>
              </v-row>
              <div class="mt-8 text-center">
                <div v-if="hasCompletedQuiz">
                  <div class="answer-status">
                    <span class="correct-answer"></span> <span>Jawaban Benar</span>
                  </div>
                  <div class="answer-status">
                    <span class="wrong-answer"></span> <span>Jawaban Salah</span>
                  </div>
                </div>
                <div v-else>
                  <div class="answer-status">
                    <span class="answered-question"></span> <span>Sudah Dipilih</span>
                  </div>
                </div>
              </div>


              <!-- Submit Button -->
              <v-row class="mt-4" justify="center">
                <v-col cols="12">
                  <v-btn 
                    @click="hasCompletedQuiz ? redirectToContent() : submitQuiz()" 
                    color="success" 
                    :disabled="!isComplete && !hasCompletedQuiz"
                    block
                  >
                    {{ hasCompletedQuiz ? 'Kembali ke Halaman Konten' : 'Submit' }}
                  </v-btn>
                </v-col>
              </v-row>
            </v-card>
          </v-col>

        </v-row>
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
      currentQuestion: 0,
      userAnswers: [],
      soalList: [],
      timeLeft: 0,
      content_id: this.$route.query.content_id || '',
      submitted: false,
      hasCompletedQuiz: false, // New flag for quiz completion status
    };
  },
  computed: {
    isComplete() {
      return this.userAnswers.every((answer) => answer !== undefined);
    },
    formattedTime() {
      const minutes = Math.floor(this.timeLeft / 60);
      const seconds = this.timeLeft % 60;
      return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    },
  },
  methods: {
    async fetchQuestions() {
      try {
        const response = await axios.get(`/api/soal/${this.content_id}`);
        this.soalList = response.data.map((soal) => ({
          ...soal,
          jawaban: JSON.parse(soal.jawaban),
        }));
        this.userAnswers = new Array(this.soalList.length).fill(undefined);
        this.timeLeft = this.soalList.length > 0 ? this.soalList[0].durasi : 0;
      } catch (error) {
        console.error('Error fetching questions:', error);
      }
    },
    async fetchScores() {
      // Periksa apakah content_id bernilai null atau kosong
      if (!this.content_id) {
        this.hasCompletedQuiz = false; // Izinkan upaya kuis jika content_id bernilai null
        return;
      }

      try {
        // Ambil ID pengguna dari API
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const user_id = userResponse.data.user_id;

        // Ambil semua nilai untuk pengguna
        const scoreResponse = await axios.get(`/api/nilai?user_id=${user_id}`);

        // Cek jika content_id yang diambil ada di dalam nilai yang diambil
        const scores = scoreResponse.data;
        const hasScoreForCurrentContent = scores.some(score => score.content_id == this.content_id);

        if (hasScoreForCurrentContent) {
          // Jika ada nilai untuk content_id yang sesuai, tandai kuis sebagai selesai
          this.hasCompletedQuiz = true;
          const scoreDetails = scores.find(score => score.content_id == this.content_id);
          this.userAnswers = scoreDetails.jawaban_pilihan || [];
        } else {
          // Jika tidak ada nilai untuk content_id yang sesuai, izinkan kuis diambil
          this.hasCompletedQuiz = false;
        }
      } catch (error) {
        console.error('Error fetching scores:', error);
        this.hasCompletedQuiz = false; // Pastikan kuis bisa diambil jika terjadi kesalahan
      }
    },

    prevQuestion() {
      if (this.currentQuestion > 0) {
        this.currentQuestion--;
      }
    },
    nextQuestion() {
      if (this.currentQuestion < this.soalList.length - 1) {
        this.currentQuestion++;
      }
    },
    async submitQuiz() {
      if (this.submitted || this.hasCompletedQuiz) return;

      this.submitted = true;
      try {
        const userResponse = await axios.get('/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        const user_id = userResponse.data.user_id;

        const contentResponse = await axios.get(`/api/content/${this.content_id}`);
        const jenis = contentResponse.data.jenis;

        let correctAnswers = 0;
        for (let i = 0; i < this.soalList.length; i++) {
          if (this.userAnswers[i] === this.soalList[i].jawaban_benar) {
            correctAnswers++;
          }
        }

        const nilai = (correctAnswers / this.soalList.length) * 100; // Hitung nilai berdasarkan jumlah jawaban benar
        await axios.post('/api/nilai', {
          content_id: this.content_id,
          user_id: user_id,
          jawaban_pilihan: this.userAnswers,
          nilai: nilai,
        });

        Swal.fire('Success', 'Quiz submitted successfully!', 'success');
        this.redirectToContent();
        this.hasCompletedQuiz = true; // Tandai kuis sebagai selesai setelah submit
      } catch (error) {
        console.error('Error submitting quiz:', error);
        Swal.fire('Error', 'There was an error submitting the quiz.', 'error');
      }
    },
    redirectToContent() {
    this.$router.push({ path: '/training-mandiri/content' }); // Absolute path
  },
    isCorrectAnswer(index) {
      return this.userAnswers[index] === this.soalList[index].jawaban_benar;
    },
  },
  mounted() {
    this.fetchQuestions();
    this.fetchScores();
    setInterval(() => {
      if (this.timeLeft > 0 && !this.hasCompletedQuiz) {
        this.timeLeft--;
      }else if (this.timeLeft === 0 && !this.submitted && !this.hasCompletedQuiz) {
      // Waktu habis, lakukan submit otomatis jika belum di-submit
      this.submitQuiz();
    }
    }, 1000);
  },
};
</script>

<style>
.answer-status {
  display: flex; /* Menggunakan flex untuk penataan */
  align-items: center; /* Untuk menyejajarkan elemen secara vertikal */
  margin-bottom: 5px; /* Jarak antar status */
}

.correct-answer, .wrong-answer, .answered-question {
  width: 16px; /* Lebar kotak warna */
  height: 16px; /* Tinggi kotak warna */
  margin-right: 8px; /* Jarak antara kotak warna dan teks */
}
.active-question {
  background-color: #1976d2;
  color: white;
}
.grey-button {
  background-color: rgba(128, 128, 128, 0.4) !important;
  color: white !important;
}
.answered-question {
  background-color: rgba(255, 255, 0, 0.9) !important;
  color: black !important;
}

.correct-answer {
  background-color: green !important;
  color: white !important;
}
.wrong-answer {
  background-color: red !important;
  color: white !important;
}
</style>
