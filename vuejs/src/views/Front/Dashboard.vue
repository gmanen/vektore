<template>
  <div>
    <VCard
      class="mb-3"
      title="Comment puis-je vous aider aujourd'hui ?"
    >
      <VCardText>
        <VCol cols="12">
          <div
              v-for="item in qaItems"
              :key="item.qaItem"
              class="mb-5"
          >
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
                  {{ item.question }}
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

                <div>
                  <div style="width: 100px">
                    <VProgressLinear
                        indeterminate
                        rounded
                        color="primary"
                        v-if="item.searching"
                    />
                  </div>
                  <span v-if="!item.searching">{{ item.answer }}</span>
                </div>
              </VTimelineItem>
            </VTimeline>
          </div>
        </VCol>
      </VCardText>

      <VForm @submit.prevent="postQuestion">
        <VCardText>
          <VCol cols="12">
            <VTextarea
                v-model="content"
                prepend-inner-icon="mdi-comment-outline"
                label="FiduSens"
                rows="1"
                auto-grow
                @keydown.enter="handleEnter"
            />
          </VCol>

          <VCol cols="12">
            <VBtn
                :loading="loadings[0]"
                :disabled="loadings[0]"
                class="float-right"
                color="primary"
                icon="mdi-cloud-upload-outline"
                @click="postQuestion()"
            />
          </VCol>

          <div class="clear"></div>
        </VCardText>
      </VForm>
    </VCard>
  </div>
</template>

<script setup>
import axios from "axios";
import config from "@/config";

const loadings = ref([])
const qaItems = ref([])
const content = ref('')
const partialAnswer = ref('')
const searching = ref(false)

const handleEnter = (event) => {
  if (event.shiftKey) {
    return;
  }

  event.preventDefault();

  if (content.value.trim()) {
    postQuestion();
  }
}

const postQuestion = async () => {
  loadings.value[0] = true

  const data = {
    content: content.value,
  }

  qaItems.value.push({ question: content.value, answer: partialAnswer.value, searching: true })

  content.value = ''

  axios.post(config.apiUrl + '/question', data, {
    onDownloadProgress: (progressEvent) => {
      qaItems.value[qaItems.value.length - 1].searching = false
      qaItems.value[qaItems.value.length - 1].answer = progressEvent.event.currentTarget.response
    }})
    .then(() => {
      partialAnswer.value = ''

      loadings.value[0] = false

      qaItems.value[qaItems.value.length - 1].searching = false
    })
    .catch(error => {
      loadings.value[0] = false
    })
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