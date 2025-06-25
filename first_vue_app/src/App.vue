<template>
  <div class="container">
    <!-- Template Syntax: Interpolation and Directives -->
    <h1>{{ appTitle }}</h1>
    <p>Count: {{ count }} (Double: {{ doubleCount }})</p>
    <input v-model="newTodo" placeholder="Add a todo" @keyup.enter="addTodo" />
    <!-- v-for for list rendering -->
    <ul>
      <li v-for="(todo, index) in todos" :key="index" :class="{ completed: todo.completed }">
        <span v-if="!todo.editing">{{ todo.text }}</span>
        <input v-else v-model="todo.text" @blur="editTodo(index)" />
        <button @click="toggleTodo(index)">Toggle</button>
        <button @click="removeTodo(index)">Remove</button>
      </li>
    </ul>
    <!-- v-if and v-show -->
    <p v-if="todos.length > 3">You have many todos!</p>
    <p v-show="count > 5">Count exceeds 5!</p>
    <!-- Dynamic attribute binding -->
    <button :disabled="todos.length === 0" @click="clearTodos">Clear Todos</button>
    <!-- Child Component with Props and Slots -->
    <ChildComponent :count="count" @increment="increment">
      <template v-slot:header>
        <h2>Child Header (via slot)</h2>
      </template>
      <template v-slot:default="{ childData }">
        <p>Child Data: {{ childData }}</p>
      </template>
    </ChildComponent>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted, onUpdated, onUnmounted , watchEffect } from 'vue';
import ChildComponent from './components/ChildComponent.vue';

export default {
  name: 'App',
  components: { ChildComponent },
  setup() {
    // Reactivity: ref and reactive
    const count = ref(0);
    const newTodo = ref('');
    const todos = reactive([]);
    const appTitle = ref('Vue 3 Concepts Demo');

    // Computed Property
    const doubleCount = computed(() => count.value * 2);

    // Methods
    const increment = () => {
      count.value++;
    };
    const addTodo = () => {
      if (newTodo.value.trim()) {
        todos.push({ text: newTodo.value, completed: false, editing: false });
        newTodo.value = '';
      }
    };
    const removeTodo = (index) => {
      todos.splice(index, 1);
    };
    const toggleTodo = (index) => {
      todos[index].completed = !todos[index].completed;
    };
    const editTodo = (index) => {
      todos[index].editing = false;
    };
    const clearTodos = () => {
      todos.length = 0;
    };

    // Watcher
    watch(count, (newValue) => {
      console.log(`Count updated to: ${newValue}`);
    });
    watchEffect(() => {
      console.log(`Todos changed, length: ${todos.length}`);
    });

    // Lifecycle Hooks
    onMounted(() => {
      console.log('App mounted!');
    });
    onUpdated(() => {
      console.log('App updated!');
    });
    onUnmounted(() => {
      console.log('App unmounted!');
    });

    return {
      count,
      newTodo,
      todos,
      appTitle,
      doubleCount,
      increment,
      addTodo,
      removeTodo,
      toggleTodo,
      editTodo,
      clearTodos,
    };
  },
};
</script>

<style scoped>
.container {
  padding: 20px;
  text-align: center;
}
.completed {
  text-decoration: line-through;
  color: gray;
}
</style>