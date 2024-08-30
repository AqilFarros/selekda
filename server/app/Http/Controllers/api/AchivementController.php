<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Achivement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AchivementController extends Controller
{
    public function index()
    {
        try {
            $achivement = Achivement::latest()->get();
            return ResponseFormatter::success($achivement, 'List Data Of achivement');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Achivement Data Failed', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'image' => 'required|image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/portfolio', $image->hashName());

            $data = $request->all();
            $data['image'] = $image->hashName();

            $achivement = Achivement::create($data);

            return ResponseFormatter::success($achivement, 'Achivement Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store Achivement', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $achivement = Achivement::findOrFail($id);
            return ResponseFormatter::success($achivement, 'Achivement Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data Achivement Failed', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'description' => 'required',
                'title' => 'required',
                'image' => 'image|mimes:jpg,jpeg,png|max:2048'
            ]);

            $achivement = Achivement::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $achivement->update($data);
            } else {
                Storage::disk('local')->delete('public/portfolio/' . basename($achivement->image));

                $image = $request->file('image');
                $image->storeAs('public/portfolio', $image->hashName());
                $data['image'] = $image->hashName();

                $achivement->update($data);
            }

            return ResponseFormatter::success($achivement, 'Achivement Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store Achivement', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $achivement = Achivement::findOrFail($id);

            Storage::disk('local')->delete('public/portfolio/' . basename($achivement->image));

            $achivement->delete();

            return ResponseFormatter::success(null, 'achivement Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
