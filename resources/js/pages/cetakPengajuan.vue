<template>
  <div>
   <div v-if="pengajuan">
    <table>
      <tr>
        <td rowspan="2" class="header" style="text-align:center;">
          <img :src="logoImage" alt="Logo" width="100" />
        </td>
        <td class="header">DOKUMEN PENDUKUNG</td>
        <td style="border-bottom: none;"></td>
      </tr>
      <tr>
        <td class="header" style="text-align: center; font-size: 1.3em;">FORMULIR KEBUTUHAN TRAINING PUBLIK</td>
        <td style="border-top: none; font-size: 0.5em; vertical-align:top;">No.: DP-01/PK-09/HRD-GA <br>Revisi: 2<br>Tanggal: 22 Maret 2016<br>Halaman: 1 dari 1</td>
      </tr>
    </table>

      <table>
        <tr>
          <td colspan="2" style="width: 50%;">
            <div class="form-container">
              <div class="form-group">
                <span class="label-text">NAMA</span><span class="colon">:</span><span class="value-text">{{ pengajuan.nama }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">DEPT / DIVISI</span><span class="colon">:</span><span class="value-text">{{ pengajuan.divisi }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">JABATAN</span><span class="colon">:</span><span class="value-text">{{ pengajuan.jabatan }}</span>
              </div>
            </div>
          </td>
          <td colspan="2" style="width: 50%;">
            <div class="form-container">
              <div class="form-group">
                <span class="label-text">NIK</span><span class="colon">:</span><span class="value-text">{{ pengajuan.nik }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">TANGGAL MASUK</span><span class="colon">:</span><span class="value-text">{{ new Date(pengajuan.tanggal_masuk).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
              </div>
            </div>
          </td>
        </tr>
      </table>

      <table>
        <tr>
          <td class="full-width">
            <div class="form-container">
              <div class="form-group">
                <span class="label-text">JUDUL TRAINING</span><span class="colon">:</span><span class="value-text">{{ pengajuan.judul_training }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">PENYELENGGARA</span><span class="colon">:</span><span class="value-text">{{ pengajuan.penyelenggara }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">BIAYA</span><span class="colon">:</span><span class="value-text">{{ pengajuan.biaya }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">HARI/TANGGAL</span><span class="colon">:</span><span class="value-text">{{ new Date(pengajuan.hari_tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">JAM</span><span class="colon">:</span><span class="value-text">{{ pengajuan.jam }}</span>
              </div>
              <div class="form-group">
                <span class="label-text">TEMPAT</span><span class="colon">:</span><span class="value-text">{{ pengajuan.tempat }}</span>
              </div>
            </div>
          </td>
        </tr>
      </table>

      <!-- Tujuan Training -->
      <table>
        <tr>
          <td colspan="2">TUJUAN DIBERIKANNYA TRAINING (Silahkan di checklist √) :</td>
        </tr>
        <tr>
          <td colspan="2">
            <div class="checkbox">
              <span v-if="Array.isArray(pengajuan.tujuan_training) && pengajuan.tujuan_training.includes('Pembekalan untuk Promosi Karyawan')">✔</span>
            </div> Pembekalan untuk Promosi Karyawan
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <div class="checkbox">
              <span v-if="Array.isArray(pengajuan.tujuan_training) && pengajuan.tujuan_training.includes('Pengembangan kompetensi/keahlian karyawan di posisi saat ini.')">✔</span>
            </div> Pengembangan kompetensi/keahlian karyawan di posisi saat ini.
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <div class="checkbox">
             <span v-if="Array.isArray(pengajuan.tujuan_training) && pengajuan.tujuan_training.filter(item => item !== 'Pembekalan untuk Promosi Karyawan' && item !== 'Pengembangan kompetensi/keahlian karyawan di posisi saat ini.').length > 0">✔</span>
            </div>  Lain-lain:  {{ Array.isArray(pengajuan.tujuan_training) ? pengajuan.tujuan_training.filter(item => item !== 'Pembekalan untuk Promosi Karyawan' && item !== 'Pengembangan kompetensi/keahlian karyawan di posisi saat ini.').join(', ') : '' }}
          </td>
        </tr>
      </table>

      <!-- Harapan Training -->
      <table>
        <tr>
          <td colspan="2">APA YANG DIHARAPKAN DARI TRAINING TERSEBUT :</td>
        </tr>
        <tr>
          <td colspan="2">
            <ul>
              <ul v-for="(harapan, index) in filteredHarapan(pengajuan.harapan_training)" :key="index">{{ index + 1 }}. {{ harapan }}</ul>
            </ul>
          </td>
        </tr>
      </table>

      <!-- Signature Section -->
      <table class="signatures">
        <tr>
          <td colspan="3" class="subheader">HRD. UNIT</td>
        </tr>
        <tr>
          <td class="header" style="border-bottom: none; border-top: none;">Pemohon</td>
          <td class="header" style="border-bottom: none; border-top: none;">Pimpinan Divisi</td>
          <td class="header" style="border-bottom: none; border-top: none;">HR Unit</td>
        </tr>
        <tr>
          <td class="empty-space" style="border-bottom: none; border-top: none; text-align: center;" v-if="pengajuan.ttd">
            <img :src="`/storage/${pengajuan.ttd}`" alt="TTD" width="50" height="50" />
          </td>
          <td class="empty-space" style="border-bottom: none; border-top: none;" v-else></td>
          <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
          <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
          
        </tr>
        <tr>
          <td class="header" style="border-top: none; width: 33%;">({{ pengajuan.nama }})</td>
          <td class="header" style="border-top: none; width: 33%;">
            <span v-if="pengajuan.approvalLine">({{ pengajuan.approvalLine.name }})</span>
            <span v-else>(...........................................)</span>
          </td>
          <td class="header" style="border-top: none;">(...........................................)</td>
        </tr>
      </table>

      <table class="signatures">
        <tr>
            <td colspan="2" class="subheader">CORPORATE HR</td>
        </tr>
        <tr>
            <td class="header" style="border-bottom: none; border-top: none;">HR Corp. Learning & Talent Development</td>
            <td class="header" style="border-bottom: none; border-top: none;">HR Director</td>
        </tr>
        <tr>
            <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
            <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
        </tr>
        <tr>
            <td class="header" style="border-top: none; width: 50%;">(Sugiyanto)</td>
            <td class="header" style="border-top: none;">(Reza Widyaprastha)</td>
        </tr>
    </table>
    <table class="signatures">
        <tr>
            <td class="subheader">DISETUJUI DIREKSI UNIT</td>
        </tr>
        <tr>
            <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
        </tr>
        <tr>
            <td class="empty-space" style="border-bottom: none; border-top: none;"></td>
        </tr>
        <tr>
            <td class="header" style="border-top: none;">(...........................................)</td>
        </tr>
    </table>
  </div>
  <div v-else>
      Loading data...
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import logoImage from '@images/logo.png';

export default {
  data() {
    return {
      pengajuan: null,
      logoImage: logoImage,
    };
  },
  methods: {
fetchPengajuan() {
  const pengajuanId = this.$route.params.pengajuan_id; // Ambil ID dari route
  axios.get(`/api/pengajuan/${pengajuanId}`)
    .then(response => {
      this.pengajuan = response.data; // Simpan data ke variabel 'pengajuan'

      // Parse data JSON setelah data diterima
      this.pengajuan.tujuan_training = this.pengajuan.tujuan_training ? JSON.parse(this.pengajuan.tujuan_training) : [];
      this.pengajuan.harapan_training = this.pengajuan.harapan_training ? JSON.parse(this.pengajuan.harapan_training) : [];

      // Kondisi jika ttd ada
      if (this.pengajuan.ttd) {
        this.pengajuan.ttd = this.pengajuan.ttd.replace('public/', ''); // Mengubah jalur ttd agar dapat diakses
      }

      this.$nextTick(() => {
        // Trigger print once the content is fully loaded
        setTimeout(() => {
          window.print(); // Cetak dokumen
        }, 500);
      });
      
      window.onafterprint = () => {
        this.$router.go(-1); // Kembali ke halaman sebelumnya setelah cetak selesai
      };
    })
    .catch(error => {
      console.error('Error fetching pengajuan:', error);
    });
},

    filteredHarapan(harapanList) {
      return Array.isArray(harapanList)
        ? harapanList.filter(harapan => harapan !== null && harapan !== '')
        : harapanList !== null && harapanList !== ''
          ? [harapanList]
          : [];
    }
  },
  mounted() {
    this.fetchPengajuan();
  }
};
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  margin: 10px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 10px;
}
th, td {
  border: 1px solid black;
  padding: 4px;
  vertical-align: middle;
  font-size: 0.8em;
}
.header, .subheader {
  text-align: center;
  font-weight: bold;
}
.checkbox {
  width: 15px;
  height: 15px;
  border: 1px solid black;
  display: inline-block;
  vertical-align: middle;
  margin-right: 5px;
}
.colon {
    flex: 0;
}
.label-text {
    flex: 0 0 120px;
    text-align: left;
    padding-right: 10px;
}
.value-text {
    flex: 2;
    text-align: left;
    padding-left: 10px;
}
.form-section {
    margin-bottom: 10px;
}
.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}
.empty-space {
    height: 20px;
}
</style>
