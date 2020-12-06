<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    //method untuk menampilkan semua data reservation (read)
    public function index()
    {
        $reservations = Reservation::all(); //mengambil semua data reservation

        if (count($reservations) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $reservations
            ], 200);
        } //return data semua reservation dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null
        ], 404); //return message data reservation kosong
    }

    //method untuk menampilkan 1 data reservation (search)
    public function show($id)
    {
        $reservation = Reservation::find($id); //mencari data reservation berdasarkan id

        if (!is_null($reservation)) {
            return response([
                'message' => 'Retrieve Reservation Success',
                'data' => $reservation
            ], 200);
        } //return data reservation yang ditemukan dalam bentuk json

        return response([
            'message' => 'Reservation Not Found',
            'data' => null
        ], 404); //return message saat data reservation tidak ditemukan
    }

    public function showByUserId($userId)
    {
        $reservation = Reservation::where('user_id', $userId); //mencari data reservation berdasarkan id

        if (!is_null($reservation)) {
            return response([
                'message' => 'Retrieve Reservation Success',
                'data' => $reservation
            ], 200);
        } //return data reservation yang ditemukan dalam bentuk json

        return response([
            'message' => 'Reservation Not Found',
            'data' => null
        ], 404); //return message saat data reservation tidak ditemukan
    }

    //method untuk menambah 1 data reservation baru (create)
    public function store(Request $request)
    {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'room_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'nights' => 'required|numeric',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'total' => 'required|numeric'
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 460); //return error invalid input

        $reservation = Reservation::create($storeData); //menambah data reservation baru
        return response([
            'message' => 'Add Reservation Success',
            'data' => $reservation,
        ], 200); //return data reservation baru dalam bentuk json
    }

    //method untuk menghapus 1 data reservation (delete)
    public function destroy($id)
    {
        $reservation = Reservation::find($id); //mencari data reservation berdasarkan id

        if (is_null($reservation)) {
            return response([
                'message' => 'Reservation Not Found',
                'data' => null
            ], 404);
        } //return message saat data reservation tidak ditemukan

        if ($reservation->delete()) {
            return response([
                'message' => 'Delete Reservation Success',
                'data' => $reservation,
            ], 200);
        } //return message saat berhasil menghapus data reservation
        return response([
            'message' => 'Delete Reservation Failed',
            'data' => null,
        ], 400); //return message saat gagal menghapus data reservation
    }

    //method untuk mengubah 1 data reservation (update)
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id); //mencari data reservation berdasarkan id
        if (is_null($reservation)) {
            return response([
                'message' => 'Reservation Not Found',
                'data' => null
            ], 404);
        } //return message saat data reservation tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'room_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'nights' => 'required|numeric',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'total' => 'required|numeric'
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $reservation->room_id = $updateData['room_id'];
        $reservation->user_id = $updateData['user_id'];
        $reservation->nights = $updateData['nights'];
        $reservation->check_in = $updateData['check_in'];
        $reservation->check_out = $updateData['check_out'];
        $reservation->total = $updateData['total'];

        if ($reservation->save()) {
            return response([
                'message' => 'Update Reservation Success',
                'data' => $reservation,
            ], 200);
        } //return data reservation yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update Reservation Failed',
            'data' => null,
        ], 400); //return message saat reservation gagal di edit
    }
}
