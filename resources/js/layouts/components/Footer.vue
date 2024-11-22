<template>
  <div class="h-100 d-flex align-center justify-md-space-between justify-center no-print">
    <!-- 👉 Footer: left content -->
    <span class="d-flex align-center">
      &copy;
      {{ new Date().getFullYear() }}
      &nbsp; <strong>PT. Jababeka Tbk</strong>
    </span>
    <!-- 👉 Footer: right content -->
    
  </div>
</template>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
