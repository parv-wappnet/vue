<!-- src/components/ChildComponent.vue -->
<template>
    <div>
        <h2>Child Component</h2>
        <p>App Title (via inject): {{ appTitle }}</p>
    </div>
</template>

<script>
import { inject } from 'vue';

export default {
    name: 'ChildComponent',
    setup() {
        // Inject to access provided data from parent component
        const appTitle = inject('appTitle');
        return { appTitle };
    },
};
</script>

<style>
div {
    border: 1px solid #ccc;
    padding: 10px;
    margin: 10px;
}
</style>