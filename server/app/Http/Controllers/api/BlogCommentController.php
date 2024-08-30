<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $blogComment = BlogComment::latest()->get();
            return ResponseFormatter::success($blogComment, 'List Data Of blogComment');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get blogComment Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'subject' => 'required',
                'website' => 'required',
                'comment' => 'required'
            ]);

            $data = $request->all();

            $blogComment = BlogComment::create($data);

            return ResponseFormatter::success($blogComment, 'blogComment Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store blogComment', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $blogComment = BlogComment::findOrFail($id);
            return ResponseFormatter::success($blogComment, 'blogComment Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data blogComment Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'subject' => 'required',
                'website' => 'required',
                'comment' => 'required'
            ]);

            $blogComment = BlogComment::findOrFail($id);

            $data = $request->all();

            $blogComment->update($data);

            return ResponseFormatter::success($blogComment, 'blogComment Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store blogComment', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $blogComment = BlogComment::findOrFail($id);

            $blogComment->delete();

            return ResponseFormatter::success(null, 'blogComment Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
