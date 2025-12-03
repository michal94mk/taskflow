<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(StoreCommentRequest $request, Task $task)
    {
        Gate::authorize('view', $task);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->validated()['content'],
        ]);

        $comment->load('user');

        return back()->with('success', 'Comment added successfully.');
    }

    /**
     * Update the specified comment.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        // Verify comment belongs to user
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $comment->update($request->validated());

        return back()->with('success', 'Comment updated successfully.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Verify comment belongs to user
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully.');
    }
}

