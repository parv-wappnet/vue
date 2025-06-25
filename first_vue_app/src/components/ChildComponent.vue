<template>
  <div class="child">
    <!-- Slots -->
    <slot name="header"></slot>
    <p>Count from Parent: {{ count }}</p>
    <button @click="$emit('increment')">Increment from Child</button>
    <!-- Scoped Slot -->
    <slot :childData="childMessage"></slot>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'ChildComponent',
  props: {
    count: {
      type: Number,
      required: true,
      validator: (value) => value >= 0,
    },
  },
  setup() {
    const childMessage = ref('Hello from Child!');
    return { childMessage };
  },
};
</script>

<style scoped>
.child {
  border: 1px solid #ccc;
  padding: 10px;
  margin: 10px;
}
</style>