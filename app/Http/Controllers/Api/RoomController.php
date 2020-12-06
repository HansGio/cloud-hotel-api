<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Room;
use Illuminate\Http\Request;
use Validator;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all(); //mengambil semua data room

        if (count($rooms) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $rooms
            ], 200);
        } //return data semua room dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 404); //return message data room kosong
    }

    //method untuk menampilkan 1 data room (search)
    public function show($id)
    {
        $room = Room::find($id); //mencari data room berdasarkan id

        if (!is_null($room)) {
            return response([
                'message' => 'Retrieve Room Success',
                'data' => $room
            ], 200);
        } //return data room yang ditemukan dalam bentuk json

        return response([
            'message' => 'Room Not Found',
            'data' => null
        ], 404); //return message saat data room tidak ditemukan
    }

    //method untuk menambah 1 data room baru (create)
    public function store(Request $request)
    {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'room_type' => 'required',
            'image_path' => 'required',
            'desc' => 'required',
            'facilities' => 'required',
            'room_left' => 'required|numeric',
            'capacity' => 'required|numeric',
            'available_room' => 'required|numeric',
            'price_per_night' => 'required|numeric'
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 460); //return error invalid input

        $room = Room::create($storeData); //menambah data room baru
        return response([
            'message' => 'Add Room Success',
            'data' => $room,
        ], 200); //return data room baru dalam bentuk json
    }

    //method untuk menghapus 1 data room (delete)
    public function destroy($id)
    {
        $room = Room::find($id); //mencari data room berdasarkan id

        if (is_null($room)) {
            return response([
                'message' => 'Room Not Found',
                'data' => null
            ], 404);
        } //return message saat data room tidak ditemukan

        if ($room->delete()) {
            return response([
                'message' => 'Delete Room Success',
                'data' => $room,
            ], 200);
        } //return message saat berhasil menghapus data room
        return response([
            'message' => 'Delete Room Failed',
            'data' => null,
        ], 400); //return message saat gagal menghapus data room
    }

    //method untuk mengubah 1 data room (update)
    public function update(Request $request, $id)
    {
        $room = Room::find($id); //mencari data room berdasarkan id
        if (is_null($room)) {
            return response([
                'message' => 'Room Not Found',
                'data' => null
            ], 404);
        } //return message saat data room tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'room_type' => 'required',
            'image_path' => 'required',
            'desc' => 'required',
            'facilities' => 'required',
            'room_left' => 'required|numeric',
            'capacity' => 'required|numeric',
            'available_room' => 'required|numeric',
            'price_per_night' => 'required|numeric'
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $room->room_id = $updateData['room_id'];
        $room->user_id = $updateData['user_id'];
        $room->nights = $updateData['nights'];
        $room->check_in = $updateData['check_in'];
        $room->check_out = $updateData['check_out'];
        $room->total = $updateData['total'];

        if ($room->save()) {
            return response([
                'message' => 'Update Room Success',
                'data' => $room,
            ], 200);
        } //return data room yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update Room Failed',
            'data' => null,
        ], 400); //return message saat room gagal di edit
    }
}
