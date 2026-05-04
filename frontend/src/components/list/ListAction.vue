<script lang="ts" setup>
import BaseIconBtn from '@/components/BaseIconBtn.vue'
</script>

<template>
    <!-- edit icon button -->
    <base-icon-btn icon="mdi-pencil" density="compact" variant="tonal" :tooltip="'common.btn.edit'"></base-icon-btn>

    <!-- delete icon button -->
    <base-icon-btn class="ml-5" icon="mdi-delete" density="compact" variant="tonal" color="error"
        :tooltip="'common.btn.delete'"></base-icon-btn>
</template>