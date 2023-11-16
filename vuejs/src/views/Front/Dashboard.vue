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
                dot-color="success"
                size="x-small"
            >
              <!-- 👉 Header -->
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  Get on the flight
                </span>
                <span class="app-timeline-meta">Wednesday</span>
              </div>

              <!-- 👉 Content -->
              <p class="app-timeline-text mb-1">
                <span>Charles de Gaulle Airport, Paris</span>
                <VIcon
                    size="20"
                    icon="mdi-arrow-right"
                    class="mx-2"
                />
                <span>Heathrow Airport, London</span>
              </p>
            </VTimelineItem>

            <VTimelineItem
                size="x-small"
                dot-color="success"
            >
              <!-- 👉 Header -->
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  Design Review
                </span>
                <span class="app-timeline-meta">September, 20</span>
              </div>

              <!-- 👉 Content -->
              <p>
                Weekly review of freshly prepared design for our new application.
              </p>
              <div class="d-flex align-center">

              </div>
            </VTimelineItem>

            <VTimelineItem
                size="x-small"
                dot-color="success"
            >
              <!-- 👉 Header -->
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  Design Review
                </span>
                <span class="app-timeline-meta">September, 20</span>
              </div>

              <!-- 👉 Content -->
              <p>
                Weekly review of freshly prepared design for our new application.
              </p>
              <div class="d-flex align-center">

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