<script setup>
import CitySearch from "@/Components/CitySearch.vue";
</script>

<template>
  <div class="p-6 flex flex-col items-center">
    <h2 class="text-2xl font-bold mb-3">Buscar ciudades</h2>
    <p class="mb-4 text-gray-500 text-sm text-center">
      Escribe el nombre de una ciudad y selecciona la opción correcta.
    </p>
    <div class="w-full max-w-md shadow-lg p-4 rounded-lg bg-white">
      <CitySearch />
    </div>
  </div>
</template>
