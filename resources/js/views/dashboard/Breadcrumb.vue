<template>
  <nav class="breadcrumb">
    <v-breadcrumbs>
      <template v-for="(item, index) in breadcrumbItems" :key="index">
        <v-breadcrumbs-item
          :to="item.link"
          :exact="index === breadcrumbItems.length - 1"
        >
          {{ item.text }}
        </v-breadcrumbs-item>
        <!-- Menambahkan garis miring sebagai pemisah, kecuali item terakhir -->
        <span v-if="index < breadcrumbItems.length - 1"> / </span>
      </template>
    </v-breadcrumbs>
  </nav>
</template>

<script>
export default {
  name: 'Breadcrumb',
  props: {
    items: {
      type: Array,
      required: true,
    },
  },
  computed: {
    breadcrumbItems() {
      return this.items.map(item => ({
        text: item.text,
        link: item.link,
      }));
    },
  },
};
</script>

<style scoped>
.breadcrumb {
  margin: 16px 0; /* Menambahkan margin di atas dan bawah */
}
.v-breadcrumbs-item {
  text-transform: capitalize; /* Menjadikan teks huruf kecil */
}
</style>
