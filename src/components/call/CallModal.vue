<template>
  <div v-if="visible" class="call-overlay">
    <div class="video-wrapper">
      <video ref="remoteVideo" autoplay playsinline></video>
      <video ref="localVideo" autoplay playsinline muted></video>
    </div>
    <div class="call-controls">
      <button @click="endCall">End Call</button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onBeforeUnmount } from 'vue';
import { useCall } from '@components/call/useCall.js';

const props = defineProps({
  visible: Boolean,
  recipientId: Number,
  conversationId: String,
  onClose: Function,
});

const localVideo = ref(null);
const remoteVideo = ref(null);

const { startCall, endCall } = useCall({
  localVideo,
  remoteVideo,
  conversationId: props.conversationId,
  recipientId: props.recipientId,
  onClose: props.onClose,
});

// Watch for changes in the `visible` prop to start the call
watch(
  () => props.visible,
  async (newVisible) => {
    if (newVisible) {
      // Wait for the next DOM update cycle to ensure video elements are rendered
      await nextTick();
      if (localVideo.value && remoteVideo.value) {
        startCall();
      } else {
        console.error('Video elements are not ready');
      }
    }
  },
  { immediate: true } // Run immediately if `visible` is already true
);

// Clean up when the component is unmounted
onBeforeUnmount(endCall);
</script>

<style scoped>
.call-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.video-wrapper {
  position: relative;
}

video {
  width: 600px;
  max-width: 100%;
  border: 2px solid white;
  margin: 10px;
  border-radius: 10px;
}

.call-controls {
  margin-top: 20px;
}
</style>