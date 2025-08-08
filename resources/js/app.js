import './bootstrap';
import { createApp } from 'vue';
import KanbanBoard from './components/KanbanBoard.vue';

// Initialize Vue app for Kanban board
document.addEventListener('DOMContentLoaded', function() {
    const kanbanContainer = document.getElementById('kanban-board');
    if (kanbanContainer) {
        const app = createApp(KanbanBoard, {
            project: JSON.parse(kanbanContainer.dataset.project)
        });
        app.mount(kanbanContainer);
    }
});
