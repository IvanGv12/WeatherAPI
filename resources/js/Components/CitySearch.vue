<script setup>
import { ref, onMounted } from "vue";
import algoliasearch from "algoliasearch/lite";
import { autocomplete } from "@algolia/autocomplete-js";
import "@algolia/autocomplete-theme-classic";
import axios from "axios";

const selected = ref(null);

// Inicializar Algolia
const client = algoliasearch(
  import.meta.env.VITE_ALGOLIA_APP_ID,
  import.meta.env.VITE_ALGOLIA_API_KEY // Search-Only Key
);

onMounted(() => {
  autocomplete({
    container: "#city-search",
    placeholder: "Buscar ciudad...",
    openOnFocus: true,
    getSources() {
      return [
        {
          sourceId: "cities",
          getItems({ query }) {
            if (!query) return [];
            return client
              .initIndex("cities")
              .search(query)
              .then(res => res.hits);
          },
          templates: {
            item({ item }) {
              return `
                <div class="p-2 hover:bg-gray-100 cursor-pointer rounded">
                  <strong>${item.name}</strong> (${item.country || "N/A"})
                </div>
              `;
            }
          }
        }
      ];
    },
    onSubmit({ state }) {
      const value = state.query;
      if (!value) return;

      axios.post("/save-city", {
        name: value,
        country: null
      })
      .then(() => {
        selected.value = value;
        console.log("Ciudad guardada:", value);
      })
      .catch(err => {
        console.error("Error al guardar la ciudad:", err);
      });
    }
  });
});
</script>

<template>
  <div class="w-full">
    <div id="city-search"></div>

    <div v-if="selected" class="mt-4 p-4 border rounded bg-gray-100">
      Ciudad seleccionada: <strong>{{ selected }}</strong>
    </div>
  </div>
</template>

<style>
#city-search .aa-InputWrapper {
  width: 100%;
}
.aa-Input {
  padding: 0.5rem;
  font-size: 1rem;
}
</style>
