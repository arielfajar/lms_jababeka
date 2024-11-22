<template>
  <v-container>
    <v-table>
      <thead>
        <tr>
          <th @click="sort('index')" style="cursor: pointer; text-align: center;">No</th>
          <th @click="sort('judul')" style="cursor: pointer; text-align: center;">Judul</th>
          <th @click="sort('jenis')" style="cursor: pointer; text-align: center;">Jenis</th>
          <th @click="sort('explore_id')" style="cursor: pointer; text-align: center;">Explore Judul</th>
          <th style="text-align: center;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(content, index) in paginatedContents" :key="content.content_id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ content.judul }}</td>
          <td>{{ getJenisText(content.jenis) }}</td>
          <td>{{ getExploreTitle(content.explore_id) }}</td>
          <td>
            <v-btn icon @click="navigateToAdd(content)">
              <v-icon>bx-plus</v-icon>
            </v-btn>
            <v-btn icon @click="openDetailModal(content)">
              <v-icon>bx-search-alt</v-icon>
            </v-btn>
            <v-btn icon @click="openEditModal(content)">
              <v-icon>bx-edit</v-icon>
            </v-btn>
            <v-btn icon @click="confirmDelete(content.content_id)">
              <v-icon>bx-trash</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>
    <v-row class="d-flex justify-space-between align-center mt-4">
      <v-col cols="auto">
        <v-pagination
          v-model="currentPage"
          :length="pageCount"
          :total-visible="5"
        ></v-pagination>
      </v-col>
      <v-col cols="auto">
        <v-select
          v-model="entriesPerPage"
          :items="entryOptions"
          label="Show entries"
          class="small-select"
        ></v-select>
      </v-col>
    </v-row>

    <v-dialog v-model="detailModal" max-width="600px">
      <v-card>
        <v-card-title class="text-h5">Detail Konten</v-card-title>
        <v-card-text>
          <div v-if="selectedContentDetails.length">
            <template v-if="selectedContentJenis === 3">
              <!-- Tampilkan detail untuk semua materi yang terkait -->
              <div
                v-for="(materi, index) in selectedContentDetails"
                :key="index"
                class="mb-4 pb-2"
                style="border-bottom: 1px solid #e0e0e0;"
              >
                <p><strong>Judul Materi {{ index + 1 }}:</strong> {{ materi.judul }}</p>
                
                <!-- Periksa apakah link adalah embed YouTube -->
                <template v-if="materi.link.includes('youtube.com/embed')">
                  <iframe
                    :src="materi.link"
                    width="100%"
                    height="315"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                </template>
                <template v-else>
                  <p><strong>Link:</strong> <a :href="materi.link" target="_blank">{{ materi.link }}</a></p>
                </template>
              </div>
            </template>
            <template v-else>
              <!-- Tampilkan detail untuk semua soal yang terkait -->
              <div
                v-for="(detail, index) in selectedContentDetails"
                :key="index"
                class="mb-4 pb-2"
                style="border-bottom: 1px solid #e0e0e0;"
              >
                <p v-if="index === 0"><strong>Durasi:</strong> {{ detail.durasi }} detik</p>
                <p><strong>Pertanyaan {{ index + 1 }}:</strong> {{ detail.pertanyaan }}</p>
                <p><strong>Jawaban:</strong></p>
                <ul>
                  <li v-for="(jawaban, i) in JSON.parse(detail.jawaban)" :key="i">{{ jawaban }}</li>
                </ul>
                <p><strong>Jawaban Benar:</strong> {{ detail.jawaban_benar }}</p>
              </div>
            </template>
          </div>
          <div v-else>
            <p>Loading...</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="detailModal = false">Tutup</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<!-- Modal for Edit Content -->
<v-dialog v-model="editModal" max-width="600px">
  <v-card>
    <v-card-title class="text-h5">Edit Konten</v-card-title>
    <v-card-text>
      <div v-if="editableContentDetails.length">
        <template v-if="selectedContentJenis === 3">
          <!-- Edit fields for content type "Materi" -->
          <div
            v-for="(materi, index) in editableContentDetails"
            :key="index"
            class="mb-4 pb-2"
            style="border-bottom: 1px solid #e0e0e0;"
          >
            <v-text-field
              label="Judul Materi"
              v-model="materi.judul"
              style="margin-bottom: 8px;" 
            ></v-text-field>
            
            <!-- Ganti v-text-field untuk Link dengan v-textarea -->
           <template v-if="materi.jenis === 'youtube'">
                <v-text-field
                  label="Link (YouTube Embed)"
                  v-model="materi.link"
                ></v-text-field>
              </template>
              <template v-else-if="materi.jenis === 'pdf' || materi.jenis === 'ppt'">
                <v-file-input
                  label="Upload File"
                  @change="handleFileUpload"
                ></v-file-input>
              </template>
          </div>
        </template>
        <template v-else>
          <!-- Edit fields for content type "Soal" -->
          <div
            v-for="(detail, index) in editableContentDetails"
            :key="index"
            class="mb-4 pb-2"
            style="border-bottom: 3px solid #e0e0e0;"
          >
            <v-text-field
              v-if="index === 0"
              label="Durasi (detik)"
              v-model="detail.durasi"
              type="number"
              style="max-width: 200px; margin-bottom: 8px;" 
            ></v-text-field>
            
            <!-- Ganti v-text-field untuk Pertanyaan dengan v-textarea -->
            <v-textarea
              label="Pertanyaan"
              v-model="detail.pertanyaan"
              auto-grow
              rows="2"
              style="max-width: 100%; margin-bottom: 20px;" 
              class="resizable"
            ></v-textarea>

            <!-- Render jawaban fields as separate text areas for better resizing -->
            <v-textarea
              v-for="(jawaban, i) in detail.jawabanArray"
              :key="i"
              :label="`Jawaban ${i + 1}`"
              v-model="detail.jawabanArray[i]"
              auto-grow
              rows="1"
              style="max-width: 100%; margin-bottom: 8px;"
              class="resizable"
            ></v-textarea>

            <!-- Dropdown for selecting correct answer -->
            <v-select
              :items="detail.jawabanArray"
              label="Jawaban Benar"
              v-model="detail.jawaban_benar"
              style="max-width: 100%;" 
            ></v-select>
          </div>
        </template>
      </div>
      <div v-else>
        <p>Loading...</p>
      </div>
    </v-card-text>
    <v-card-actions>
      <v-btn color="blue darken-1" text @click="editModal = false">Batal</v-btn>
      <v-btn color="blue darken-1" text @click="saveEdit">Simpan</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>



  </v-container>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      currentPage: 1,
      entriesPerPage: 6,
      entryOptions: [3, 6, 12, 18, 24],
      contents: [],
      explores: [],
      sortOrder: 'asc',
      sortKey: 'index',
      selectedContentDetails: [],
      selectedContentJenis: null, // Jenis konten yang dipilih
      detailModal: false, // Mengontrol visibilitas modal
      editModal: false,
      editableContentDetails: [],
    };
  },
  computed: {
    paginatedContents() {
      const start = (this.currentPage - 1) * this.entriesPerPage;
      const end = start + this.entriesPerPage;
      return this.sortedContents.slice(start, end);
    },
    sortedContents() {
      return this.contents.sort((a, b) => {
        let modifier = this.sortOrder === 'asc' ? 1 : -1;
        if (a[this.sortKey] < b[this.sortKey]) return -1 * modifier;
        if (a[this.sortKey] > b[this.sortKey]) return 1 * modifier;
        return 0;
      });
    },
    pageCount() {
      return Math.ceil(this.contents.length / this.entriesPerPage);
    },
  },
  methods: {
    isYouTubeEmbed(link) {
      return link && link.includes('youtube.com/embed');
    },
    // Handle file upload for materi
    handleFileUpload(event) {
      const file = event.target.files[0];
      // Proses file yang diunggah, misalnya dengan mengirimnya ke server
    },
    async fetchContents() {
      try {
        const response = await axios.get('/api/content');
        this.contents = response.data;
      } catch (error) {
        console.error('Error fetching contents:', error);
      }
    },
    async fetchExplores() {
      try {
        const response = await axios.get('/api/explore');
        this.explores = response.data;
      } catch (error) {
        console.error('Error fetching explores:', error);
      }
    },
    getExploreTitle(explore_id) {
      const explore = this.explores.find(e => e.explore_id === explore_id);
      return explore ? explore.judul : '';
    },
    sort(key) {
      this.sortKey = key;
      this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
    },
    getJenisText(jenis) {
      switch (jenis) {
        case 1:
          return 'Pre test';
        case 2:
          return 'Soal';
        case 3:
          return 'Materi';
        case 4:
          return 'Post Test';
        default:
          return '';
      }
    },
    navigateToAdd(content) {
      const path = content.jenis === 3 ? '/admin/content/materi' : '/admin/content/soal';
      this.$router.push({ path, query: { content_id: content.content_id } });
    },
    async openDetailModal(content) {
      try {
        this.selectedContentJenis = content.jenis; // Simpan jenis konten
        if (content.jenis === 3) {
          // Jika jenis adalah "Materi", panggil API Materi
          const response = await axios.get(`/api/materi/${content.content_id}`);
          this.selectedContentDetails = response.data;
        } else {
          // Jika jenis bukan "Materi", panggil API Soal
          const response = await axios.get(`/api/soal/${content.content_id}`);
          this.selectedContentDetails = response.data;
        }
        this.detailModal = true;
      } catch (error) {
        console.error('Error fetching detail:', error);
      }
    },
    async openEditModal(content) {
      try {
        this.selectedContentJenis = content.jenis;
        if (content.jenis === 3) {
          // Fetch "Materi" data
          const response = await axios.get(`/api/materi/${content.content_id}`);
          this.editableContentDetails = response.data;
        } else {
          // Fetch "Soal" data
          const response = await axios.get(`/api/soal/${content.content_id}`);
          this.editableContentDetails = response.data.map(detail => ({
            ...detail,
            jawabanArray: JSON.parse(detail.jawaban) // Convert answers to an array
          }));
        }
        this.editModal = true;
      } catch (error) {
        console.error('Error fetching content details:', error);
      }
    },
    async saveEdit() {
      try {
        let apiUrl = this.selectedContentJenis === 3 ? '/api/materi' : '/api/soal';
        
        // Kirim permintaan simpan untuk Materi atau Soal
        for (let detail of this.editableContentDetails) {
          let payload = {
            id: this.selectedContentJenis === 3 ? detail.materi_id : detail.soal_id, 
            judul: detail.judul,
            jenis: detail.jenis,
            link: detail.link,
            pertanyaan: detail.pertanyaan,
            jawaban: detail.jawabanArray,
            jawaban_benar: detail.jawaban_benar,
            durasi: detail.durasi
          };
          
          // API call untuk menyimpan perubahan
          await axios.put(`${apiUrl}/${payload.id}`, payload);
        }
        
        // Tutup modal dan refresh data setelah berhasil disimpan
        this.editModal = false;
        this.fetchContents();
        
      } catch (error) {
        console.error('Error saving edit:', error);
        alert('Gagal menyimpan perubahan.');
      }
    },
    confirmDelete(contentId) {
      // Code to confirm deletion
    },
  },
  async created() {
    await this.fetchExplores();
    this.fetchContents();
  },
};
</script>

<style scoped>
.v-table {
  width: 100%;
  border-collapse: collapse;
}

.v-table th {
  background: rgba(1, 228, 0, 0.1);
  text-align: center;
}
.v-table tr {
  text-align: center;
}
.truncate-text {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.small-select {
  max-width: 200px;
}
.resizable {
  resize: both; /* Allows resizing in both horizontal and vertical directions */
  overflow: auto;
  padding: 4px;
}
</style>
