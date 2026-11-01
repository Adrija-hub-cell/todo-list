<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $query = Task::query();

        if ($filter === 'active') {
            $query->where('is_completed', false);
        } elseif ($filter === 'completed') {
            $query->where('is_completed', true);
        }

        $tasks = $query->orderBy('is_completed')
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->latest('created_at')
            ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'filter' => in_array($filter, ['all', 'active', 'completed'], true) ? $filter : 'all',
            'activeCount' => Task::where('is_completed', false)->count(),
            'completedCount' => Task::where('is_completed', true)->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task added to your list.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        if ($request->has('toggle')) {
            $task->update(['is_completed' => ! $task->is_completed]);
            return back()->with('success', $task->is_completed ? 'Task completed. Nice work!' : 'Task moved back to your list.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        $task->update($data);

        return back()->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return back()->with('success', 'Task deleted.');
    }
}
