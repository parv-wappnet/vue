<!-- src/App.vue -->
<template>
  <div>
    <h1>{{ title }}</h1>
    <p>Count: {{ count }}</p>
    <p>Double Count: {{ doubleCount }}</p>
    <input v-model="userInput" placeholder="Type something" />
    <p>User Input: {{ userInput }}</p>
    <button @click="increment">Increment Count</button>
    <button @click="changeTitle">Change Title</button>
    <p v-if="count > 5">Count is greater than 5!</p>

    <!-- Child Component -->
    <ChildComponent />
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUpdated, provide, getCurrentInstance } from 'vue';
import ChildComponent from './components/ChildComponent.vue';

export default {
  name: 'App',
  components: { ChildComponent },
  setup() {
    // Reactive Data
    const count = ref(0);
    const userInput = ref('');
    const title = ref('Vue 3 Instance Demo');

    // Computed Property
    const doubleCount = computed(() => count.value * 2);

    // Methods
    const increment = () => {
      count.value++;
    };
    const changeTitle = () => {
      title.value = 'Updated Title!';
    };

    // Watcher 
    //here newvalue(a) and oldvalue(b) are predefined parameters
    watch(count, (a, b) => {
      console.log(`Count changed from ${b} to ${a}`);
    });

    // Lifecycle Hooks
    onMounted(() => {
      console.log('App component mounted!');

      //access gloable plugin
      const { proxy } = getCurrentInstance();
      proxy.$sayHello();
    });
    onUpdated(() => {
      console.log('App component updated!');
    });

    // Provide (to pass data to child components)
    provide('appTitle', title);

    return { count, userInput, title, doubleCount, increment, changeTitle };
  },
};
</script>

<style>
div {
  padding: 20px;
  text-align: center;
}
</style>