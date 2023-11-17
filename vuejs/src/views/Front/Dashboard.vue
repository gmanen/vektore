<template>
  <div>
    <VCard
      class="mb-6"
      title="Comment puis-je vous aider aujourd'hui ?"
    >
      <VCardText>
        <VCol cols="12">
          <VTimeline
              side="end"
              align="start"
              line-inset="8"
              truncate-line="both"
              density="compact"
          >
            <VTimelineItem
                size="x-small"
                dot-color="info"
            >
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  John Doe
                </span>
                <span class="app-timeline-meta">20/09/2023</span>
              </div>

              <p></p>

              <div class="d-flex align-center">
                Donne moi l'url de l'env de recette du projet eCoffre
              </div>
            </VTimelineItem>

            <VTimelineItem
                size="x-small"
                dot-color="success"
            >
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  FiduSens
                </span>
                <span class="app-timeline-meta">20/09/2023</span>
              </div>

              <p></p>

              <div class="d-flex align-center">
                Biensur, la voici : https://recette.ecoffre.com
              </div>
            </VTimelineItem>

            <VTimelineItem
                size="x-small"
                dot-color="info"
            >
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  John Doe
                </span>
                <span class="app-timeline-meta">20/09/2023</span>
              </div>

              <p></p>

              <div class="d-flex align-center">
                Aupres de qui puis-je demander une des informations lié au projet HAB/AUT ?
              </div>
            </VTimelineItem>

            <VTimelineItem
                size="x-small"
                dot-color="success"
            >
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  FiduSens
                </span>
                <span class="app-timeline-meta">20/09/2023</span>
              </div>

              <p></p>

              <div class="d-flex align-center">
                Vous pouvez vous rapprocher du chef de projet : XXX YYY ou du responsable technique : XXX YYY
              </div>
            </VTimelineItem>
          </VTimeline>
        </VCol>
      </VCardText>

      <VCardText>
        <VCol cols="12">
          <VTextarea
              prepend-inner-icon="mdi-comment-outline"
              label="FiduSens"
              rows="1"
              auto-grow
          />
        </VCol>

        <VCol cols="12">
          <VBtn
              :loading="loadings[0]"
              :disabled="loadings[0]"
              class="float-right"
              color="primary"
              icon="mdi-cloud-upload-outline"
              @click="load(0)"
          />
        </VCol>

        <div class="clear"></div>
      </VCardText>
    </VCard>
  </div>
</template>

<script setup>
const loadings = ref([])

const load = i => {
  loadings.value[i] = true
  setTimeout(() => {
    loadings.value[i] = false
  }, 3000)
}
</script>

<style lang="scss" scoped>
.custom-loader {
  display: flex;
  animation: loader 1s infinite;
}

@keyframes loader {
  from {
    transform: rotate(0);
  }

  to {
    transform: rotate(360deg);
  }
}
</style>