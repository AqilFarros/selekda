<?php

namespace App\Http\Controllers\api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index()
    {
        try {
            $service = Service::latest()->get();
            return ResponseFormatter::success($service, 'List Data Of service');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get service Data Failed', 500);
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

            $service = Service::create($data);

            return ResponseFormatter::success($service, 'service Successfully Created');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Store service', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $service = Service::findOrFail($id);
            return ResponseFormatter::success($service, 'service Data');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Get Data service Failed', 500);
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

            $service = Service::findOrFail($id);

            $data = $request->all();

            if ($request->file('image') == '') {
                $service->update($data);
            } else {
                Storage::disk('local')->delete('public/portfolio/' . basename($service->image));

                $image = $request->file('image');
                $image->storeAs('public/portfolio', $image->hashName());
                $data['image'] = $image->hashName();

                $service->update($data);
            }

            return ResponseFormatter::success($service, 'service Data Has Been Successfully Updated');
        } catch (\Exception $th) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $th,
            ], 'Failed To Store service', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $service = Service::findOrFail($id);

            Storage::disk('local')->delete('public/portfolio/' . basename($service->image));

            $service->delete();

            return ResponseFormatter::success(null, 'service Data Has Been Successfully Deleted');
        } catch (\Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something Went Wrong',
                'error' => $error
            ], 'Failed To Delete Data');
        }
    }
}
