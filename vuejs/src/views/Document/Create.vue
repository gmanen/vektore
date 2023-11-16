<template>
  <VCard title="Création d'un document">
    <VCardText>
      Un document peut-être un fichier ou une URL.
    </VCardText>

    <VCardText>
      <VCol cols="12">
        <VTextField
            v-model="title"
            label="Titre"
            type="text"
            placeholder="Titre"
        />
      </VCol>

      <VCol cols="12">
        <VFileInput
            v-model="file"
            show-size
            label="File input"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
            v-model="url"
            label="URL"
            type="text"
            placeholder="URL"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
            v-model="cssSelector"
            label="Sélecteur CSS"
            type="text"
            placeholder="Sélecteur CSS"
        />
      </VCol>

      <VCol cols="12">
        <VBtn color="primary" @click="postDocument()">Valider</VBtn>
      </VCol>
    </VCardText>
  </VCard>
</template>

<script setup>
import {useStore} from 'vuex';
import axios from "axios";

const store = useStore();
const router = useRouter()
const title = ref('')
const file = ref('')
const content = ref('')
const url = ref('')
const cssSelector = ref('')

const postDocument = async () => {
  const data = {
    title: title.value,
  }

  if (file.value && file.value[0] && file.value[0] instanceof File) {
    data.filename = file.value[0].name
    data.content = await toBase64(file.value[0])
    data.type = 'FILE'
  } else {
    data.url = url.value
    data.cssSelector = cssSelector.value
    data.type = 'URL'
  }

  console.log(data)

  /*
  axios
      .post(config.apiUrl + '/documents', data, {
      })
      .then(response => {
        if (response.status === 200) {
          router.push({ name: 'AdminDocumentList' })
        }
      })
      .catch(error => {
        console.log(error)
      })
    */
}

const toBase64 = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = () => resolve(reader.result);
  reader.onerror = error => reject(error);
});

</script>