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

      <h4 class="mb-3">Source en provenance d'un document</h4>
      <VCol cols="12">
        <VFileInput
            v-model="fileRaw"
            show-size
            label="Fichier brute"
        />
      </VCol>

      <VCol cols="12">
        <VFileInput
            v-model="fileTxt"
            show-size
            label="Fichier texte"
        />
      </VCol>

      <!--
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
      -->

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
const fileTxt = ref('')
const fileRaw = ref('')
const url = ref('')
const cssSelector = ref('')

const postDocument = async () => {
  const data = {
    title: title.value,
  }

  if (fileTxt.value && fileTxt.value[0] && fileTxt.value[0] instanceof File) {
    data.filename = fileRaw.value[0].name
    data.contentTxt = await toBase64(fileTxt.value[0])
    data.contentTxt = data.contentTxt.split(',')[1]
    data.contentRaw = await toBase64(fileRaw.value[0])
    data.contentRaw = data.contentRaw.split(',')[1]
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