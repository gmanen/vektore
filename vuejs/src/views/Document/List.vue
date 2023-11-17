<template>
  <VCard title="Listing des documents">
    <VCardText>
      Retrouvez ici la liste complète de tous les documents.
    </VCardText>

    <VCardText>
      <VBtn color="primary" size="small" :to="{ name: 'DocumentCreate' }">
        Ajouter un document
      </VBtn>
    </VCardText>

    <VTable>
      <thead>
      <tr>
        <th class="text-uppercase">
          ID
        </th>
        <th class="text-uppercase">
          Title
        </th>
        <th class="text-uppercase">
          Type
        </th>
      </tr>
      </thead>

      <tbody>
      <tr
          v-for="item in documents"
          :key="item.document"
      >
        <td>
          {{ item.id }}
        </td>
        <td>
          {{ item.title }}
        </td>
        <td>
          {{ item.type }}
        </td>
      </tr>
      <tr v-if="documents.length == 0">
        <td>
          Il n'y a aucun document
        </td>
      </tr>
      </tbody>
    </VTable>
    <br>
  </VCard>
</template>

<script setup>
import axios from "@axios";
import config from "@/config";
import { useStore } from 'vuex';
import { ref } from 'vue';

const store = useStore();
const router = useRouter()
let documents = ref([])

onBeforeMount(() => {
  getDocuments()
})

const getDocuments = () => {
  axios
      .get(config.apiUrl + '/documents', {})
      .then(response => {
        if (response.status === 200) {
          documents.value = response.data
        }
      })
      .catch(error => {
        console.log(error)
      })
}
</script>