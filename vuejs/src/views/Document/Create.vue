<template>
  <VCard title="Création d'un document">
    <VCardText>
      Un document peut se présenter sous la forme d'un fichier stocké localement ou d'une URL pointant vers une ressource en ligne.
    </VCardText>

    <div></div>

    <VCardText>
      <h4>Infos document</h4>
      <VCol cols="12">
        <VTextField
            v-model="title"
            label="Titre"
            type="text"
            placeholder="Titre"
        />
      </VCol>

      <br>

      <h4>Source en provenance d'un document</h4>
      <VCol cols="12">
        <VFileInput
            v-model="file"
            show-size
            label="Fichier"
        />
      </VCol>

      <br>

      <h4>Source en provenance d'un lien</h4>

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

      <br>

      <VCol cols="12">
        <VBtn color="primary" @click="postDocument()">Valider</VBtn>
      </VCol>
    </VCardText>
  </VCard>
</template>

<script setup>
import axios from "axios";
import config from "@/config";

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
    data.content = data.content.split(',')[1]
    data.type = 'file'
  } else {
    data.url = url.value
    data.cssSelector = cssSelector.value
    data.type = 'url'
  }

  axios
      .post(config.apiUrl + '/documents', data, {
      })
      .then(response => {
        if (response.status === 201) {
          router.push({ name: 'DocumentList' })
        }
      })
      .catch(error => {
        console.log(error)
      })
}

const toBase64 = (file) => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = () => resolve(reader.result);
  reader.onerror = error => reject(error);
});

</script>