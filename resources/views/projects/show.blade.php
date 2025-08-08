@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Project Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $project->color }}"></div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
                    <p class="text-gray-600">{{ $project->description }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('tasks.create', $project) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Task
                </a>
                <a href="{{ route('projects.edit', $project) }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Vue.js Kanban Board -->
    <div id="kanban-board"></div>
</div>

<script>
const { createApp, h } = Vue;

createApp({
    data() {
        return {
            project: @json($project),
            tasks: [],
            showModal: false,
            selectedTask: null,
            draggedTask: null
        }
    },
    computed: {
        todoTasks() {
            return this.tasks.filter(task => task.status === 'todo').sort((a, b) => a.order - b.order);
        },
        inProgressTasks() {
            return this.tasks.filter(task => task.status === 'in_progress').sort((a, b) => a.order - b.order);
        },
        reviewTasks() {
            return this.tasks.filter(task => task.status === 'review').sort((a, b) => a.order - b.order);
        },
        completedTasks() {
            return this.tasks.filter(task => task.status === 'completed').sort((a, b) => a.order - b.order);
        }
    },
    methods: {
        async loadTasks() {
            try {
                const response = await fetch(`/projects/${this.project.id}/tasks`);
                this.tasks = await response.json();
            } catch (error) {
                console.error('Error loading tasks:', error);
            }
        },
        handleDragStart(event, task) {
            this.draggedTask = task;
            event.dataTransfer.effectAllowed = 'move';
        },
        async handleDrop(event, newStatus) {
            event.preventDefault();
            
            if (!this.draggedTask) return;

            const taskId = this.draggedTask.id;

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
                            order: 0
                        }]
                    })
                });

                if (response.ok) {
                    // Update local state
                    const taskIndex = this.tasks.findIndex(t => t.id === taskId);
                    if (taskIndex !== -1) {
                        this.tasks[taskIndex].status = newStatus;
                    }
                }
            } catch (error) {
                console.error('Error updating task order:', error);
            }

            this.draggedTask = null;
        },
        async openTaskModal(taskId) {
            try {
                const response = await fetch(`/tasks/${taskId}/modal`);
                this.selectedTask = await response.json();
                this.showModal = true;
            } catch (error) {
                console.error('Error loading task details:', error);
            }
        },
        closeTaskModal() {
            this.showModal = false;
            this.selectedTask = null;
        },
        async deleteTask(taskId) {
            if (!confirm('Are you sure you want to delete this task? This action cannot be undone.')) {
                return;
            }
            
            try {
                console.log('Attempting to delete task:', taskId);
                const response = await fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (response.ok) {
                    // Remove task from local state
                    this.tasks = this.tasks.filter(task => task.id !== taskId);
                    this.closeTaskModal();
                    
                    // Show success message
                    this.showSuccessMessage('Task deleted successfully!');
                } else {
                    const errorText = await response.text();
                    console.error('Response text:', errorText);
                    throw new Error(`Failed to delete task: ${response.status} ${response.statusText}`);
                }
            } catch (error) {
                console.error('Error deleting task:', error);
                this.showErrorMessage(`Failed to delete task: ${error.message}`);
            }
        },
        showSuccessMessage(message) {
            // Create a temporary success message
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
            successDiv.textContent = message;
            document.body.appendChild(successDiv);
            
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        },
        showErrorMessage(message) {
            // Create a temporary error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up';
            errorDiv.textContent = message;
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
        },
        getPriorityColor(priority) {
            const colors = {
                'urgent': 'bg-gradient-to-r from-red-500 to-red-600',
                'high': 'bg-gradient-to-r from-orange-500 to-orange-600',
                'medium': 'bg-gradient-to-r from-yellow-500 to-yellow-600',
                'low': 'bg-gradient-to-r from-green-500 to-green-600'
            };
            return colors[priority] || 'bg-gradient-to-r from-gray-500 to-gray-600';
        },
        getStatusColor(status) {
            const colors = {
                'todo': 'bg-gradient-to-r from-gray-400 to-gray-500',
                'in_progress': 'bg-gradient-to-r from-blue-500 to-blue-600',
                'review': 'bg-gradient-to-r from-yellow-500 to-orange-500',
                'completed': 'bg-gradient-to-r from-green-500 to-emerald-500'
            };
            return colors[status] || 'bg-gradient-to-r from-gray-500 to-gray-600';
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric' 
            });
        },
        formatStatus(status) {
            return status.replace('_', ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        },
        getStatusFromColumn(column) {
            const columnElement = column.closest('.kanban-column');
            if (columnElement.querySelector('h3').textContent.includes('To Do')) return 'todo';
            if (columnElement.querySelector('h3').textContent.includes('In Progress')) return 'in_progress';
            if (columnElement.querySelector('h3').textContent.includes('Review')) return 'review';
            if (columnElement.querySelector('h3').textContent.includes('Completed')) return 'completed';
            return null;
        },
        completedSubtasks() {
            if (!this.selectedTask || !this.selectedTask.subtasks) return 0;
            return this.selectedTask.subtasks.filter(subtask => subtask.is_completed).length;
        },
        isOverdue(task) {
            if (!task.due_date || task.status === 'completed') return false;
            return new Date(task.due_date) < new Date();
        },
        renderTaskCard(task, statusColor) {
            return h('div', {
                key: task.id,
                class: 'task-card rounded-xl shadow-sm border border-gray-200 p-4 cursor-move hover:shadow-lg transition-all duration-300 group',
                'data-task-id': task.id,
                draggable: true,
                onDragstart: (event) => this.handleDragStart(event, task)
            }, [
                h('div', { class: 'flex justify-between items-start mb-3' }, [
                    h('div', { class: 'flex items-center space-x-2' }, [
                        h('span', { class: `w-2 h-2 rounded-full ${statusColor}` }),
                        h('span', { class: 'text-xs text-gray-500' }, task.status.replace('_', ' '))
                    ]),
                    h('div', { class: 'flex items-center space-x-1' }, [
                        h('button', {
                            class: 'opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-400 hover:text-blue-600',
                            onClick: () => this.openTaskModal(task.id)
                        }, [
                            h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' }),
                                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z' })
                            ])
                        ]),
                        h('button', {
                            class: 'opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-400 hover:text-red-600',
                            onClick: (e) => { e.stopPropagation(); this.deleteTask(task.id); }
                        }, [
                            h('svg', { class: 'w-4 h-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' })
                            ])
                        ])
                    ])
                ]),
                h('h4', { class: 'font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-200' }, task.title),
                task.description ? h('p', { class: 'text-sm text-gray-600 mb-3 line-clamp-2' }, task.description) : null,
                h('div', { class: 'flex items-center justify-between mb-3' }, [
                    h('span', {
                        class: `inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold text-white shadow-sm ${this.getPriorityColor(task.priority)}`
                    }, task.priority.charAt(0).toUpperCase() + task.priority.slice(1))
                ]),
                task.due_date ? h('div', { class: 'flex items-center space-x-2 mb-3' }, [
                    h('svg', { class: 'w-4 h-4 text-gray-400', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
                    ]),
                    h('span', { class: 'text-xs text-gray-500' }, this.formatDate(task.due_date))
                ]) : null,
                task.assignee ? h('div', { class: 'flex items-center justify-between' }, [
                    h('div', { class: 'flex items-center space-x-2' }, [
                        h('div', { class: 'w-6 h-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center' }, [
                            h('span', { class: 'text-xs font-semibold text-white' }, task.assignee.name.charAt(0))
                        ]),
                        h('span', { class: 'text-xs text-gray-600' }, task.assignee.name)
                    ])
                ]) : null
            ]);
        },
        renderColumn(title, tasks, statusColor, bgColor, textColor) {
            return h('div', { class: 'kanban-column rounded-xl p-6 shadow-sm' }, [
                h('div', { class: 'flex items-center justify-between mb-6' }, [
                    h('div', { class: 'flex items-center space-x-3' }, [
                        h('div', { class: `w-3 h-3 bg-gradient-to-r ${bgColor} rounded-full` }),
                        h('h3', { class: 'text-lg font-semibold text-gray-900' }, title)
                    ]),
                    h('span', { class: `bg-gradient-to-r ${bgColor} text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm` }, tasks.length)
                ]),
                h('div', { class: 'space-y-4 min-h-[400px]' }, tasks.map(task => this.renderTaskCard(task, statusColor)))
            ]);
        },
        renderModal() {
            const task = this.selectedTask;
            const completedSubtasks = task.subtasks ? task.subtasks.filter(subtask => subtask.is_completed).length : 0;
            const isOverdue = task.due_date && task.status !== 'completed' && new Date(task.due_date) < new Date();

            return h('div', {
                class: 'fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50',
                onClick: () => this.closeTaskModal()
            }, [
                h('div', {
                    class: 'relative top-10 mx-auto p-6 max-w-2xl w-full',
                    onClick: (e) => e.stopPropagation()
                }, [
                    h('div', { class: 'bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 overflow-hidden' }, [
                        // Modal Header
                        h('div', { class: 'flex items-center justify-between p-6 border-b border-gray-200/50' }, [
                            h('div', { class: 'flex items-center space-x-3' }, [
                                h('div', { class: 'w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center' }, [
                                    h('svg', { class: 'w-6 h-6 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                        h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' })
                                    ])
                                ]),
                                h('div', {}, [
                                    h('h3', { class: 'text-xl font-bold text-gray-900' }, 'Task Details'),
                                    h('p', { class: 'text-sm text-gray-500' }, 'View and manage task information')
                                ])
                            ]),
                            h('button', {
                                class: 'text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200',
                                onClick: () => this.closeTaskModal()
                            }, [
                                h('svg', { class: 'w-6 h-6', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M6 18L18 6M6 6l12 12' })
                                ])
                            ])
                        ]),
                        // Modal Content
                        h('div', { class: 'p-6' }, [
                            h('div', { class: 'space-y-6' }, [
                                // Task Header
                                h('div', { class: 'flex items-start justify-between' }, [
                                    h('div', { class: 'flex-1' }, [
                                        h('h2', { class: 'text-2xl font-bold text-gray-900 mb-2' }, task.title),
                                        task.description ? h('p', { class: 'text-gray-600 leading-relaxed' }, task.description) : null
                                    ]),
                                    h('div', { class: 'flex items-center space-x-3 ml-4' }, [
                                        h('span', {
                                            class: `inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-white shadow-sm ${this.getPriorityColor(task.priority)}`
                                        }, task.priority.charAt(0).toUpperCase() + task.priority.slice(1)),
                                        h('span', {
                                            class: `inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold text-white shadow-sm ${this.getStatusColor(task.status)}`
                                        }, this.formatStatus(task.status))
                                    ])
                                ]),
                                // Task Details Grid
                                h('div', { class: 'grid grid-cols-1 md:grid-cols-2 gap-6' }, [
                                    // Project Info
                                    h('div', { class: 'bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 border border-blue-100' }, [
                                        h('div', { class: 'flex items-center space-x-3 mb-3' }, [
                                            h('div', { class: 'w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center' }, [
                                                h('svg', { class: 'w-4 h-4 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10' })
                                                ])
                                            ]),
                                            h('h3', { class: 'font-semibold text-gray-900' }, 'Project')
                                        ]),
                                        h('p', { class: 'text-gray-700 font-medium' }, task.project.name),
                                        h('p', { class: 'text-sm text-gray-500' }, task.project.status)
                                    ]),
                                    // Due Date
                                    task.due_date ? h('div', { class: 'bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-4 border border-orange-100' }, [
                                        h('div', { class: 'flex items-center space-x-3 mb-3' }, [
                                            h('div', { class: 'w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-lg flex items-center justify-center' }, [
                                                h('svg', { class: 'w-4 h-4 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' })
                                                ])
                                            ]),
                                            h('h3', { class: 'font-semibold text-gray-900' }, 'Due Date')
                                        ]),
                                        h('p', { class: 'text-gray-700 font-medium' }, this.formatDate(task.due_date)),
                                        h('p', { class: 'text-sm text-gray-500' }, isOverdue ? 'Overdue' : 'On time')
                                    ]) : null
                                ]),
                                // Assignee and Creator
                                h('div', { class: 'grid grid-cols-1 md:grid-cols-2 gap-6' }, [
                                    task.assignee ? h('div', { class: 'bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100' }, [
                                        h('div', { class: 'flex items-center space-x-3 mb-3' }, [
                                            h('div', { class: 'w-8 h-8 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center' }, [
                                                h('svg', { class: 'w-4 h-4 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
                                                ])
                                            ]),
                                            h('h3', { class: 'font-semibold text-gray-900' }, 'Assigned To')
                                        ]),
                                        h('div', { class: 'flex items-center space-x-3' }, [
                                            h('div', { class: 'w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center' }, [
                                                h('span', { class: 'text-sm font-semibold text-white' }, task.assignee.name.charAt(0))
                                            ]),
                                            h('div', {}, [
                                                h('p', { class: 'text-gray-700 font-medium' }, task.assignee.name),
                                                h('p', { class: 'text-sm text-gray-500' }, task.assignee.email)
                                            ])
                                        ])
                                    ]) : null,
                                    h('div', { class: 'bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100' }, [
                                        h('div', { class: 'flex items-center space-x-3 mb-3' }, [
                                            h('div', { class: 'w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center' }, [
                                                h('svg', { class: 'w-4 h-4 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })
                                                ])
                                            ]),
                                            h('h3', { class: 'font-semibold text-gray-900' }, 'Created By')
                                        ]),
                                        h('div', { class: 'flex items-center space-x-3' }, [
                                            h('div', { class: 'w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center' }, [
                                                h('span', { class: 'text-sm font-semibold text-white' }, task.creator.name.charAt(0))
                                            ]),
                                            h('div', {}, [
                                                h('p', { class: 'text-gray-700 font-medium' }, task.creator.name),
                                                h('p', { class: 'text-sm text-gray-500' }, this.formatDate(task.created_at))
                                            ])
                                        ])
                                    ])
                                ]),
                                // Subtasks
                                task.subtasks && task.subtasks.length > 0 ? h('div', { class: 'bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl p-4 border border-gray-100' }, [
                                    h('div', { class: 'flex items-center justify-between mb-4' }, [
                                        h('div', { class: 'flex items-center space-x-3' }, [
                                            h('div', { class: 'w-8 h-8 bg-gradient-to-r from-gray-500 to-slate-600 rounded-lg flex items-center justify-center' }, [
                                                h('svg', { class: 'w-4 h-4 text-white', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                                                    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' })
                                                ])
                                            ]),
                                            h('h3', { class: 'font-semibold text-gray-900' }, 'Subtasks')
                                        ]),
                                        h('span', { class: 'text-sm text-gray-500' }, `${completedSubtasks}/${task.subtasks.length} completed`)
                                    ]),
                                    h('div', { class: 'space-y-2' }, task.subtasks.map(subtask => 
                                        h('div', { class: 'flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-100' }, [
                                            h('div', {
                                                class: `w-4 h-4 rounded-full border-2 flex items-center justify-center ${subtask.is_completed ? 'bg-green-500 border-green-500' : 'border-gray-300'}`
                                            }, subtask.is_completed ? [
                                                h('svg', { class: 'w-2.5 h-2.5 text-white', fill: 'currentColor', viewBox: '0 0 20 20' }, [
                                                    h('path', { 'fill-rule': 'evenodd', d: 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z', 'clip-rule': 'evenodd' })
                                                ])
                                            ] : null),
                                            h('span', {
                                                class: `text-sm ${subtask.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}`
                                            }, subtask.title)
                                        ])
                                    ))
                                ]) : null,
                                // Actions
                                h('div', { class: 'flex items-center justify-end space-x-3 pt-6 border-t border-gray-200' }, [
                                    h('button', {
                                        class: 'px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors duration-200',
                                        onClick: () => this.closeTaskModal()
                                    }, 'Close'),
                                    h('button', {
                                        class: 'px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-medium hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm',
                                        onClick: () => this.deleteTask(task.id)
                                    }, 'Delete Task'),
                                    h('a', {
                                        href: `/tasks/${task.id}/edit`,
                                        class: 'px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 transition-all duration-200 shadow-sm'
                                    }, 'Edit Task')
                                ])
                            ])
                        ])
                    ])
                ])
            ]);
        }
    },
    render() {
        const children = [
            h('div', { class: 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6' }, [
                this.renderColumn('To Do', this.todoTasks, 'bg-gray-400', 'from-gray-500 to-gray-600', 'text-gray-700'),
                this.renderColumn('In Progress', this.inProgressTasks, 'bg-blue-500', 'from-blue-600 to-blue-700', 'text-blue-700'),
                this.renderColumn('Review', this.reviewTasks, 'bg-yellow-500', 'from-orange-500 to-orange-600', 'text-orange-700'),
                this.renderColumn('Completed', this.completedTasks, 'bg-green-500', 'from-green-600 to-emerald-600', 'text-green-700')
            ])
        ];

        // Add modal if showModal is true
        if (this.showModal && this.selectedTask) {
            children.push(this.renderModal());
        }

        return h('div', {}, children);
    },
    mounted() {
        this.loadTasks();
        
        // Setup drag and drop for columns
        this.$nextTick(() => {
            const columns = document.querySelectorAll('.space-y-4');
            columns.forEach(column => {
                column.addEventListener('dragover', (e) => e.preventDefault());
                column.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const status = this.getStatusFromColumn(column);
                    if (status) {
                        this.handleDrop(e, status);
                    }
                });
            });
        });
    }
}).mount('#kanban-board');
</script>
@endsection
