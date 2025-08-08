<template>
  <div 
    class="task-card rounded-xl shadow-sm border border-gray-200 p-4 cursor-move hover:shadow-lg transition-all duration-300 group"
    :data-task-id="task.id"
    @dragstart="$emit('dragstart', $event, task)"
    draggable="true"
  >
    <!-- Status Indicator -->
    <div class="flex justify-between items-start mb-3">
      <div class="flex items-center space-x-2">
        <span 
          class="w-2 h-2 rounded-full"
          :class="statusColor"
        ></span>
        <span class="text-xs text-gray-500">{{ task.status.replace('_', ' ') }}</span>
      </div>
      <button 
        @click="$emit('view-task', task.id)"
        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-400 hover:text-blue-600"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
      </button>
    </div>

    <!-- Task Title -->
    <h4 class="font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-200">
      {{ task.title }}
    </h4>

    <!-- Task Description -->
    <p v-if="task.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
      {{ task.description }}
    </p>

    <!-- Priority Badge -->
    <div class="flex items-center justify-between mb-3">
      <span 
        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold text-white shadow-sm"
        :class="priorityColor"
      >
        {{ task.priority.charAt(0).toUpperCase() + task.priority.slice(1) }}
      </span>
    </div>

    <!-- Due Date -->
    <div v-if="task.due_date" class="flex items-center space-x-2 mb-3">
      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <span class="text-xs text-gray-500">
        {{ formatDate(task.due_date) }}
      </span>
    </div>

    <!-- Subtasks Progress -->
    <div v-if="task.subtasks && task.subtasks.length > 0" class="mb-3">
      <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
        <span>Subtasks</span>
        <span>{{ completedSubtasks }}/{{ task.subtasks.length }}</span>
      </div>
      <div class="w-full bg-gray-200 rounded-full h-2">
        <div 
          class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: `${subtaskProgress}%` }"
        ></div>
      </div>
    </div>

    <!-- Assignee -->
    <div v-if="task.assignee" class="flex items-center justify-between">
      <div class="flex items-center space-x-2">
        <div class="w-6 h-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
          <span class="text-xs font-semibold text-white">
            {{ task.assignee.name.charAt(0) }}
          </span>
        </div>
        <span class="text-xs text-gray-600">{{ task.assignee.name }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'TaskCard',
  props: {
    task: {
      type: Object,
      required: true
    }
  },
  emits: ['dragstart', 'view-task'],
  setup(props) {
    // Computed properties
    const statusColor = computed(() => {
      const colors = {
        'todo': 'bg-gray-400',
        'in_progress': 'bg-blue-500',
        'review': 'bg-yellow-500',
        'completed': 'bg-green-500'
      }
      return colors[props.task.status] || 'bg-gray-400'
    })

    const priorityColor = computed(() => {
      const colors = {
        'urgent': 'bg-gradient-to-r from-red-500 to-red-600',
        'high': 'bg-gradient-to-r from-orange-500 to-orange-600',
        'medium': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        'low': 'bg-gradient-to-r from-green-500 to-green-600'
      }
      return colors[props.task.priority] || 'bg-gradient-to-r from-gray-500 to-gray-600'
    })

    const completedSubtasks = computed(() => {
      if (!props.task.subtasks) return 0
      return props.task.subtasks.filter(subtask => subtask.is_completed).length
    })

    const subtaskProgress = computed(() => {
      if (!props.task.subtasks || props.task.subtasks.length === 0) return 0
      return (completedSubtasks.value / props.task.subtasks.length) * 100
    })

    // Methods
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric' 
      })
    }

    return {
      statusColor,
      priorityColor,
      completedSubtasks,
      subtaskProgress,
      formatDate
    }
  }
}
</script>

<style scoped>
.task-card {
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(229, 231, 235, 0.5);
}

.task-card:hover {
  background: rgba(255, 255, 255, 1);
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>



