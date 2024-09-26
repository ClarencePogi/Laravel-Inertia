<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index() {
        return Inertia::render('Tasks', ['tasks' => Task::all()]);
    }

    public function store(Request $request) {
        $validated = $this->validateTask($request);


        $date = (string) explode('T', $request->due_date)[0];
        $validated['due_date'] = $date;

        Task::create($validated);

        return redirect()->back()->with('success', 'Successfully add task!');
    }

    public function delete($id) {
        Task::findOrFail($id)->delete();

    }

    public function update(Request $request) {
        $validated = $this->validateTask($request);

        $date = (string) explode('T', $request->due_date)[0];
        $validated['due_date'] = $date;

        $task = Task::findOrFail($request->id)->update($validated);
    }

    function validateTask($task) {
        $validated = $task->validate([
            'title' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:100'],
            'due_date' => ['required', 'date'],
            'status' => ['required', 'in:Incomplete,Complete'],
        ]);

        $status = $validated['status'] == 'Complete' ? 1 : 0;

        $validated['status'] = $status;

        return $validated;
    }
}
