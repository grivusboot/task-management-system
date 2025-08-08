<template>
  <div class="kanban-board">
    <!-- Kanban Columns -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Todo Column -->
      <div class="kanban-column rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full"></div>
            <h3 class="text-lg font-semibold text-gray-900">To Do</h3>
          </div>
          <span class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
            {{ todoTasks.length }}
          </span>
        </div>
        <div 
          ref="todoColumn"
          class="space-y-4 min-h-[400px]"
          @drop="handleDrop($event, 'todo')"
          @dragover.prevent
          @dragenter.prevent
        >
          <TaskCard 
            v-for="task in todoTasks" 
            :key="task.id" 
            :task="task"
            @view-task="openTaskModal"
            @dragstart="handleDragStart"
            draggable="true"
          />
        </div>
      </div>

      <!-- In Progress Column -->
      <div class="kanban-column rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
            <h3 class="text-lg font-semibold text-gray-900">In Progress</h3>
          </div>
          <span class="bg-gradient-to-r from-blue-100 to-blue-200 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
            {{ inProgressTasks.length }}
          </span>
        </div>
        <div 
          ref="inProgressColumn"
          class="space-y-4 min-h-[400px]"
          @drop="handleDrop($event, 'in_progress')"
          @dragover.prevent
          @dragenter.prevent
        >
          <TaskCard 
            v-for="task in inProgressTasks" 
            :key="task.id" 
            :task="task"
            @view-task="openTaskModal"
            @dragstart="handleDragStart"
            draggable="true"
          />
        </div>
      </div>

      <!-- Review Column -->
      <div class="kanban-column rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full"></div>
            <h3 class="text-lg font-semibold text-gray-900">Review</h3>
          </div>
          <span class="bg-gradient-to-r from-yellow-100 to-orange-100 text-orange-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
            {{ reviewTasks.length }}
          </span>
        </div>
        <div 
          ref="reviewColumn"
          class="space-y-4 min-h-[400px]"
          @drop="handleDrop($event, 'review')"
          @dragover.prevent
          @dragenter.prevent
        >
          <TaskCard 
            v-for="task in reviewTasks" 
            :key="task.id" 
            :task="task"
            @view-task="openTaskModal"
            @dragstart="handleDragStart"
            draggable="true"
          />
        </div>
      </div>

      <!-- Completed Column -->
      <div class="kanban-column rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <div class="flex items-center space-x-3">
            <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"></div>
            <h3 class="text-lg font-semibold text-gray-900">Completed</h3>
          </div>
          <span class="bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full shadow-sm">
            {{ completedTasks.length }}
          </span>
        </div>
        <div 
          ref="completedColumn"
          class="space-y-4 min-h-[400px]"
          @drop="handleDrop($event, 'completed')"
          @dragover.prevent
          @dragenter.prevent
        >
          <TaskCard 
            v-for="task in completedTasks" 
            :key="task.id" 
            :task="task"
            @view-task="openTaskModal"
            @dragstart="handleDragStart"
            draggable="true"
          />
        </div>
      </div>
    </div>

    <!-- Task Modal -->
    <TaskModal 
      v-if="showModal" 
      :task="selectedTask" 
      @close="closeTaskModal"
    />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import TaskCard from './TaskCard.vue'
import TaskModal from './TaskModal.vue'

export default {
  name: 'KanbanBoard',
  components: {
    TaskCard,
    TaskModal
  },
  props: {
    project: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const tasks = ref([])
    const showModal = ref(false)
    const selectedTask = ref(null)
    const draggedTask = ref(null)

    // Computed properties for filtered tasks
    const todoTasks = computed(() => 
      tasks.value.filter(task => task.status === 'todo').sort((a, b) => a.order - b.order)
    )
    
    const inProgressTasks = computed(() => 
      tasks.value.filter(task => task.status === 'in_progress').sort((a, b) => a.order - b.order)
    )
    
    const reviewTasks = computed(() => 
      tasks.value.filter(task => task.status === 'review').sort((a, b) => a.order - b.order)
    )
    
    const completedTasks = computed(() => 
      tasks.value.filter(task => task.status === 'completed').sort((a, b) => a.order - b.order)
    )

    // Load tasks
    const loadTasks = async () => {
      try {
        const response = await fetch(`/projects/${props.project.id}/tasks`)
        const data = await response.json()
        tasks.value = data
      } catch (error) {
        console.error('Error loading tasks:', error)
      }
    }

    // Handle drag start
    const handleDragStart = (event, task) => {
      draggedTask.value = task
      event.dataTransfer.effectAllowed = 'move'
    }

    // Handle drop
    const handleDrop = async (event, newStatus) => {
      event.preventDefault()
      
      if (!draggedTask.value) return

      const taskId = draggedTask.value.id
      const targetColumn = event.target.closest('[class*="space-y-4"]')
      
      if (!targetColumn) return

      // Calculate new order
      const tasksInColumn = targetColumn.querySelectorAll('[data-task-id]')
      const newOrder = Array.from(tasksInColumn).length

      try {
        const response = await fetch('/tasks/update-order', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            tasks: [{
              id: taskId,
              status: newStatus,
              order: newOrder
            }]
          })
        })

        if (response.ok) {
          // Update local state
          const taskIndex = tasks.value.findIndex(t => t.id === taskId)
          if (taskIndex !== -1) {
            tasks.value[taskIndex].status = newStatus
            tasks.value[taskIndex].order = newOrder
          }
        }
      } catch (error) {
        console.error('Error updating task order:', error)
      }

      draggedTask.value = null
    }

    // Modal functions
    const openTaskModal = async (taskId) => {
      try {
        const response = await fetch(`/tasks/${taskId}/modal`)
        const task = await response.json()
        selectedTask.value = task
        showModal.value = true
      } catch (error) {
        console.error('Error loading task details:', error)
      }
    }

    const closeTaskModal = () => {
      showModal.value = false
      selectedTask.value = null
    }

    onMounted(() => {
      loadTasks()
    })

    return {
      tasks,
      showModal,
      selectedTask,
      todoTasks,
      inProgressTasks,
      reviewTasks,
      completedTasks,
      handleDragStart,
      handleDrop,
      openTaskModal,
      closeTaskModal
    }
  }
}
</script>

<style scoped>
.kanban-column {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(229, 231, 235, 0.5);
}

.kanban-column:hover {
  background: rgba(255, 255, 255, 0.9);
  transform: translateY(-2px);
  transition: all 0.3s ease;
}
</style>



