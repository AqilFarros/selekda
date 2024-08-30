<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Blog;
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
            $blog = Blog::latest()->get();
            return ResponseFormatter::success($blog, 'List Data Of Blog');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Blog Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|unique:blogs',
                'description' => 'required',
                'author' => 'required',
                'tag' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/blog', $image->hashName());

            $data = $request->all();
            $data['image'] = $image->hashName();

            $blog = Blog::create($data);

            return ResponseFormatter::success($blog, 'Blog Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store Blog', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return ResponseFormatter::success($blog, 'Blog Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data Blog Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'title' => 'required|unique:blogs',
                'description' => 'required',
                'author' => 'required',
                'tag' => 'required',
                'image' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $blog = Blog::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $blog->update($data);
            } else {
                Storage::disk('local')->delete('public/blog/' . basename($blog->image));

                $image = $request->file('image');
                $image->storeAs('public/blog', $image->hashName());
                $data['image'] = $image->hashName();

                $blog->update($data);
            }

            return ResponseFormatter::success($blog, 'Blog Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store Blog', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $blog = Blog::findOrFail($id);

            Storage::disk('local')->delete('public/blog/' . basename($blog->image));

            $blog->delete();

            return ResponseFormatter::success(null, 'Blog Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
