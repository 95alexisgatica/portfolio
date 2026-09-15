<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(): JsonResponse
    {
        $comments = Comment::query()->latest()->get();

        return response()->json([
            'comments' => $comments,
            'unread_count' => $comments->where('is_read', false)->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
        ]);

        $comment = Comment::query()->create([
            'user_id' => $request->user()->id,
            'name' => $request->user()->nickname,
            'message' => $data['message'],
        ]);

        return response()->json($comment, 201);
    }

    public function markRead(Comment $comment): JsonResponse
    {
        $comment->update(['is_read' => true]);

        return response()->json($comment);
    }
}
