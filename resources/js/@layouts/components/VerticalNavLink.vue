<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useTheme } from 'vuetify'; // Import untuk mendeteksi tema

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
});

const route = useRoute();
const theme = useTheme(); // Instance Vuetify theme

// Menentukan apakah item link aktif
const isActive = computed(() => {
  const currentPath = route.path;
  return props.item.to ? currentPath.startsWith(props.item.to) : false;
});

// Menghitung opacity berdasarkan tema
const activeLinkOpacity = computed(() => {
  return theme.global.current.value.dark ? 1 : 0.16;
});
</script>

<template>
  <li class="nav-link" :class="{ disabled: item.disable }">
    <Component
      :is="item.to ? 'RouterLink' : 'a'"
      :to="item.to"
      :href="item.href"
      :target="item.target"
      :class="{ 'active-link': isActive }" 
      :style="{ '--v-activated-opacity': activeLinkOpacity }"
    >
      <VIcon :icon="item.icon || 'bxs-circle'" class="nav-item-icon" />
      <span class="nav-item-title">{{ item.title }}</span>
      <span class="nav-item-badge" :class="item.badgeClass">
        {{ item.badgeContent }}
      </span>
    </Component>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a {
    display: flex;
    align-items: center;
    cursor: pointer;
  }
  .nav-link .active-link {
    background-color: rgba(var(--v-theme-primary), var(--v-activated-opacity));
    box-shadow: none;
    color: rgb(var(--v-theme-accent));
    font-weight: bold;
    border-radius: 8px;
  }
}
</style>
