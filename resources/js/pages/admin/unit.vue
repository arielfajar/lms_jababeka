<template>
  <div>
    <!-- Search and Show Entries Controls -->
    <div class="d-flex justify-space-between mb-4">
      <v-text-field
        v-model="searchQuery"
        label="Search"
        solo
        clearable
        style="max-width: 400px;"
      ></v-text-field>

      <v-select
        v-model="entriesPerPage"
        :items="[5, 10, 25, 50, 100]"
        label="Show"
        solo
        style="max-width: 80px;"
      ></v-select>
    </div>

    <v-table>
      <thead>
        <tr>
          <th @click="sort('index')" style="cursor: pointer;">No</th>
          <th @click="sort('unit_nama')" style="cursor: pointer;">Unit Name</th>
          <th @click="sort('unit_slug')" style="cursor: pointer;">Slug</th>
          <th @click="sort('divisi')" style="cursor: pointer;">Divisi</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(unit, index) in filteredAndPaginatedUnits" :key="unit.unit_id">
          <td>{{ (currentPage - 1) * entriesPerPage + index + 1 }}</td>
          <td>{{ unit.unit_nama }}</td>
          <td>{{ unit.unit_slug }}</td>
          <td>{{ unit.divisi }}</td>
          <td>
            <v-btn icon @click="openEditModal(unit)">
              <v-icon>bx-pencil</v-icon>
            </v-btn>
            <v-btn icon @click="deleteUnit(unit.unit_id)">
              <v-icon>bx-trash</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>

    <v-pagination
      v-model="currentPage"
      :length="pageCount"
      @input="paginate"
    ></v-pagination>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      units: [], // To store the fetched units
      currentPage: 1,
      entriesPerPage: 10, // Default to 10 entries per page
      searchQuery: '', // To store the search input
      sortKey: '',
      sortDirection: 'asc',
    };
  },
  computed: {
    filteredAndPaginatedUnits() {
      // Filter units based on search query
      const filteredUnits = this.units.filter(unit => {
        return (
          unit.unit_nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          unit.unit_slug.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          unit.divisi.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });

      // Paginate the filtered results
      return filteredUnits.slice(
        (this.currentPage - 1) * this.entriesPerPage,
        this.currentPage * this.entriesPerPage
      );
    },
    pageCount() {
      // Calculate page count based on filtered units
      const filteredUnits = this.units.filter(unit => {
        return (
          unit.unit_nama.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          unit.unit_slug.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          unit.divisi.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      });
      return Math.ceil(filteredUnits.length / this.entriesPerPage);
    },
  },
  created() {
    this.fetchUnits();
  },
  methods: {
    async fetchUnits() {
      try {
        const response = await axios.get('/api/units');
        this.units = response.data;
      } catch (error) {
        console.error('Error fetching units:', error);
      }
    },
    sort(key) {
      this.sortDirection = this.sortKey === key && this.sortDirection === 'asc' ? 'desc' : 'asc';
      this.sortKey = key;

      this.units.sort((a, b) => {
        if (this.sortDirection === 'asc') {
          return a[key] > b[key] ? 1 : -1;
        } else {
          return a[key] < b[key] ? 1 : -1;
        }
      });
    },
    paginate() {
      // Pagination handler (automatically handled by Vuetify)
    },
    openEditModal(unit) {
      // Handle edit functionality
      console.log('Edit unit:', unit);
    },
    async deleteUnit(id) {
      try {
        await axios.delete(`/api/units/${id}`);
        this.fetchUnits(); // Refresh the unit list
      } catch (error) {
        console.error('Error deleting unit:', error);
      }
    },
  },
};
</script>
