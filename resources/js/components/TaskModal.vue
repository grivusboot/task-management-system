<template>
  <div 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50"
    @click="$emit('close')"
  >
    <div class="relative top-10 mx-auto p-6 max-w-2xl w-full" @click.stop>
      <div class="bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200/50">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-gray-900">Task Details</h3>
              <p class="text-sm text-gray-500">View and manage task information</p>
            </div>
          </div>
          <button 
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        <!-- Modal Content -->
        <div class="p-6" v-if="task">
          <div class="space-y-6">
            <!-- Task Header -->
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ task.title }}</h2>
                <p v-if="task.description" class="text-gray-600 leading-relaxed">{{ task.description }}</p>
              </div>
              <div class="flex items-center space-x-3 ml-4">
                <span 
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-white shadow-sm"
                  :class="priorityColor"
                >
                  {{ task.priority.charAt(0).toUpperCase() + task.priority.slice(1) }}
                </span>
                <span 
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-white shadow-sm"
                  :class="statusColor"
                >
                  {{ task.status.replace('_', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ') }}
                </span>
              </div>
            </div>
            
            <!-- Task Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Project Info -->
              <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border border-blue-100">
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900">Project</h3>
                </div>
                <p class="text-gray-700 font-medium">{{ task.project.name }}</p>
                <p class="text-sm text-gray-500">{{ task.project.status }}</p>
              </div>
              
              <!-- Due Date -->
              <div v-if="task.due_date" class="bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 border border-orange-100">
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900">Due Date</h3>
                </div>
                <p class="text-gray-700 font-medium">{{ formatDate(task.due_date) }}</p>
                <p class="text-sm text-gray-500">
                  {{ isOverdue ? 'Overdue' : 'On time' }}
                </p>
              </div>
            </div>
            
            <!-- Assignee and Creator -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div v-if="task.assignee" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900">Assigned To</h3>
                </div>
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-semibold text-white">{{ task.assignee.name.charAt(0) }}</span>
                  </div>
                  <div>
                    <p class="text-gray-700 font-medium">{{ task.assignee.name }}</p>
                    <p class="text-sm text-gray-500">{{ task.assignee.email }}</p>
                  </div>
                </div>
              </div>
              
              <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                <div class="flex items-center space-x-3 mb-3">
                  <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900">Created By</h3>
                </div>
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-semibold text-white">{{ task.creator.name.charAt(0) }}</span>
                  </div>
                  <div>
                    <p class="text-gray-700 font-medium">{{ task.creator.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatDate(task.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Subtasks -->
            <div v-if="task.subtasks && task.subtasks.length > 0" class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-4 border border-gray-100">
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-gradient-to-r from-gray-500 to-slate-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900">Subtasks</h3>
                </div>
                <span class="text-sm text-gray-500">{{ completedSubtasks }}/{{ task.subtasks.length }} completed</span>
              </div>
              <div class="space-y-2">
                <div 
                  v-for="subtask in task.subtasks" 
                  :key="subtask.id"
                  class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-100"
                >
                  <div 
                    class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                    :class="subtask.is_completed ? 'bg-green-500 border-green-500' : 'border-gray-300'"
                  >
                    <svg v-if="subtask.is_completed" class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <span 
                    class="text-sm"
                    :class="subtask.is_completed ? 'line-through text-gray-400' : 'text-gray-700'"
                  >
                    {{ subtask.title }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
              <button 
                @click="$emit('close')"
                class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200"
              >
                Close
              </button>
              <a 
                :href="`/tasks/${task.id}/edit`"
                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-sm"
              >
                Edit Task
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'TaskModal',
  props: {
    task: {
      type: Object,
      required: true
    }
  },
  emits: ['close'],
  setup(props) {
    // Computed properties
    const priorityColor = computed(() => {
      const colors = {
        'urgent': 'bg-gradient-to-r from-red-500 to-red-600',
        'high': 'bg-gradient-to-r from-orange-500 to-orange-600',
        'medium': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
        'low': 'bg-gradient-to-r from-green-500 to-green-600'
      }
      return colors[props.task.priority] || 'bg-gradient-to-r from-gray-500 to-gray-600'
    })

    const statusColor = computed(() => {
      const colors = {
        'todo': 'bg-gradient-to-r from-gray-400 to-gray-500',
        'in_progress': 'bg-gradient-to-r from-blue-500 to-blue-600',
        'review': 'bg-gradient-to-r from-yellow-500 to-orange-500',
        'completed': 'bg-gradient-to-r from-green-500 to-emerald-500'
      }
      return colors[props.task.status] || 'bg-gradient-to-r from-gray-500 to-gray-600'
    })

    const completedSubtasks = computed(() => {
      if (!props.task.subtasks) return 0
      return props.task.subtasks.filter(subtask => subtask.is_completed).length
    })

    const isOverdue = computed(() => {
      if (!props.task.due_date || props.task.status === 'completed') return false
      return new Date(props.task.due_date) < new Date()
    })

    // Methods
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      })
    }

    return {
      priorityColor,
      statusColor,
      completedSubtasks,
      isOverdue,
      formatDate
    }
  }
}
</script>



