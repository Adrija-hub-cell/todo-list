<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TUDU — make room for what matters</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <a class="brand" href="{{ route('tasks.index') }}"><span class="brand-mark">t.</span><span>TUDU</span></a>
        <div class="side-label">WORKSPACE</div>
        <nav class="nav-list" aria-label="Task filters">
            <a class="nav-item {{ $filter === 'all' ? 'selected' : '' }}" href="{{ route('tasks.index', ['filter' => 'all']) }}"><span class="nav-icon">▦</span> All tasks <span class="count">{{ $activeCount + $completedCount }}</span></a>
            <a class="nav-item {{ $filter === 'active' ? 'selected' : '' }}" href="{{ route('tasks.index', ['filter' => 'active']) }}"><span class="nav-icon">◷</span> In progress <span class="count">{{ $activeCount }}</span></a>
            <a class="nav-item {{ $filter === 'completed' ? 'selected' : '' }}" href="{{ route('tasks.index', ['filter' => 'completed']) }}"><span class="nav-icon">✓</span> Completed <span class="count">{{ $completedCount }}</span></a>
        </nav>
        <div class="sidebar-bottom"><div class="mini-spark">✳</div><strong>One thing at a time.</strong><p>You’re doing better than you think.</p></div>
    </aside>

    <main class="main">
        <header class="topbar"><div class="breadcrumb">My workspace <span>/</span> <strong>{{ ucfirst($filter) }} tasks</strong></div><div class="avatar">A</div></header>
        <section class="content">
            <div class="welcome"><div><div class="eyebrow">YOUR DAY, YOUR WAY</div><h1>{{ $filter === 'completed' ? 'Look at you go.' : 'Make it a good one.' }}</h1><p>A little progress each day adds up to big results.</p></div><div class="date-chip"><span>✦</span> {{ now()->format('l, M j') }}</div></div>

            @if (session('success')) <div class="toast">{{ session('success') }}</div> @endif
            @if ($errors->any()) <div class="error-box">{{ $errors->first() }}</div> @endif

            <form class="composer" action="{{ route('tasks.store') }}" method="POST">
                @csrf
                <div class="composer-main"><span class="add-icon">+</span><input name="title" type="text" placeholder="What needs to get done?" value="{{ old('title') }}" maxlength="120" required aria-label="Task title"><button class="add-button" type="submit">Add task <span>↗</span></button></div>
                <div class="composer-options"><input name="notes" type="text" placeholder="Add a note (optional)" maxlength="1000" aria-label="Task note"><label class="option"><span>◷</span><input name="due_date" type="date" aria-label="Due date"></label><label class="option"><span>⚑</span><select name="priority" aria-label="Priority"><option value="normal">Normal</option><option value="high">High priority</option><option value="low">Low priority</option></select></label></div>
            </form>

            <div class="list-heading"><div><h2>{{ $filter === 'completed' ? 'Done and dusted' : 'Your tasks' }}</h2><span class="subheading">{{ $filter === 'completed' ? $completedCount.' completed' : $activeCount.' things on your plate' }}</span></div><div class="list-view">☷ <span>List</span></div></div>

            <div class="task-list">
                @forelse ($tasks as $task)
                    <article class="task-card {{ $task->is_completed ? 'is-done' : '' }}">
                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="check-form">@csrf @method('PATCH')<input type="hidden" name="toggle" value="1"><button class="checkbox {{ $task->is_completed ? 'checked' : '' }}" type="submit" aria-label="{{ $task->is_completed ? 'Mark as active' : 'Mark as completed' }}">{{ $task->is_completed ? '✓' : '' }}</button></form>
                        <div class="task-copy"><div class="task-title">{{ $task->title }}</div>@if ($task->notes)<div class="task-note">{{ $task->notes }}</div>@endif<div class="task-meta">@if ($task->due_date)<span class="due {{ !$task->is_completed && $task->due_date->isPast() ? 'overdue' : '' }}">◷ {{ $task->due_date->format('M j') }}</span>@endif<span class="priority {{ $task->priority }}"><i></i>{{ ucfirst($task->priority) }}</span></div></div>
                        <div class="task-actions">
                            <button class="icon-button" type="button" aria-label="Edit task" onclick="openEdit({{ $task->id }})">✎</button>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Delete this task?')">@csrf @method('DELETE')<button class="icon-button delete" type="submit" aria-label="Delete task">×</button></form>
                        </div>
                        <dialog id="edit-{{ $task->id }}" class="edit-dialog"><form action="{{ route('tasks.update', $task) }}" method="POST">@csrf @method('PATCH')<div class="dialog-title">Edit task <button type="button" class="icon-button" onclick="this.closest('dialog').close()">×</button></div><label>Task<input name="title" value="{{ $task->title }}" maxlength="120" required></label><label>Note<input name="notes" value="{{ $task->notes }}" maxlength="1000"></label><div class="dialog-row"><label>Due date<input name="due_date" type="date" value="{{ $task->due_date?->format('Y-m-d') }}"></label><label>Priority<select name="priority"><option value="low" @selected($task->priority === 'low')>Low</option><option value="normal" @selected($task->priority === 'normal')>Normal</option><option value="high" @selected($task->priority === 'high')>High</option></select></label></div><button class="add-button save-button" type="submit">Save changes <span>↗</span></button></form></dialog>
                    </article>
                @empty
                    <div class="empty-state"><div class="empty-icon">✳</div><h3>{{ $filter === 'completed' ? 'Nothing completed yet' : 'Your list is clear' }}</h3><p>{{ $filter === 'completed' ? 'Finish a task and it will show up here.' : 'Add a task above and give your day a little direction.' }}</p></div>
                @endforelse
            </div>
            @if ($activeCount > 0 && $filter !== 'completed')<div class="gentle-note"><span>✧</span> You’ve got this. Pick one task and get started.</div>@endif
        </section>
    </main>
</div>
<script>function openEdit(id){document.getElementById('edit-'+id).showModal()}</script>
</body>
</html>
