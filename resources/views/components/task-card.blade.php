<div class="task-card rounded-xl shadow-sm border border-gray-200 p-4 cursor-move hover:shadow-lg transition-all duration-300 group" data-task-id="{{ $task->id }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $task->title }}</h4>
            @if($task->description)
                <p class="text-xs text-gray-500 mt-2 line-clamp-2 leading-relaxed">{{ Str::limit($task->description, 80) }}</p>
            @endif
            
            <!-- Priority indicator -->
            <div class="flex items-center mt-3">
                @switch($task->priority)
                    @case('urgent')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white priority-urgent shadow-sm">
                            <div class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 opacity-80"></div>
                            Urgent
                        </span>
                        @break
                    @case('high')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white priority-high shadow-sm">
                            <div class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 opacity-80"></div>
                            High
                        </span>
                        @break
                    @case('medium')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white priority-medium shadow-sm">
                            <div class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 opacity-80"></div>
                            Medium
                        </span>
                        @break
                    @default
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium text-white priority-low shadow-sm">
                            <div class="w-1.5 h-1.5 bg-white rounded-full mr-1.5 opacity-80"></div>
                            Low
                        </span>
                @endswitch
            </div>
            
            <!-- Due date -->
            @if($task->due_date)
                <div class="flex items-center mt-3">
                    <div class="w-4 h-4 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium {{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600' : 'text-gray-600' }}">
                        {{ $task->due_date->format('M j') }}
                    </span>
                </div>
            @endif
            
            <!-- Subtasks progress -->
            @if($task->subtasks->count() > 0)
                <div class="mt-3">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span class="font-medium">Subtasks</span>
                        <span class="font-semibold">{{ $task->subtasks->where('is_completed', true)->count() }}/{{ $task->subtasks->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        @php
                            $progress = $task->subtasks->count() > 0 ? ($task->subtasks->where('is_completed', true)->count() / $task->subtasks->count()) * 100 : 0;
                        @endphp
                        <div class="h-2 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Task actions -->
        <div class="flex items-center space-x-1 ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button onclick="openTaskModal({{ $task->id }})" class="text-gray-400 hover:text-blue-600 p-1.5 rounded-lg hover:bg-blue-50 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Assignee -->
    @if($task->assignee)
        <div class="flex items-center mt-4 pt-3 border-t border-gray-100">
            <div class="flex-shrink-0">
                <div class="w-7 h-7 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-sm">
                    <span class="text-xs font-semibold text-white">{{ substr($task->assignee->name, 0, 1) }}</span>
                </div>
            </div>
            <div class="ml-2">
                <p class="text-xs font-medium text-gray-900">{{ $task->assignee->name }}</p>
            </div>
        </div>
    @endif
    
    <!-- Status indicator -->
    <div class="absolute top-2 right-2">
        @switch($task->status)
            @case('todo')
                <div class="w-3 h-3 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full"></div>
                @break
            @case('in_progress')
                <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full"></div>
                @break
            @case('review')
                <div class="w-3 h-3 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full"></div>
                @break
            @case('completed')
                <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full"></div>
                @break
        @endswitch
    </div>
</div>
