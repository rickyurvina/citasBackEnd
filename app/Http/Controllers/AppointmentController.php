<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json(Appointment::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreAppointmentRequest $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        $fields = $request->validate(([
            'name' => 'required',
            'owner' => 'required|unique:appointments',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'date' => 'nullable|date',
            'symptom' => 'nullable',
        ]));

        try {
            DB::beginTransaction();
            $appointment = Appointment::create([
                'name' => $fields['name'],
                'owner' => $fields['owner'],
                'email' => $fields['email'],
                'phone' => $fields['phone'],
                'date' => $fields['date'] ?? null,
                'symptom' => $fields['symptom'],
            ]);
            DB::commit();
            return response()->json($appointment);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Appointment $appointment
     * @return JsonResponse
     */
    public function show(int $id)
    {
        $appointment = Appointment::find($id);
        $appointments=$appointment->owner->appointments;
        $codeName=$appointment->owner->concatCodeName();

        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateAppointmentRequest $request
     * @param \App\Models\Appointment $appointment
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $fields = $request->validate(([
            'name' => 'required',
            'owner' => 'required|unique:appointments',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'date' => 'nullable|date',
            'symptom' => 'nullable',
        ]));

        try {
            DB::beginTransaction();
            $appointment = Appointment::where('id', $fields['id'])->update([
                'id' => $fields['id'],
                'name' => $fields['name'],
                'owner' => $fields['owner'],
                'email' => $fields['email'],
                'phone' => $fields['phone'],
                'date' => $fields['date'],
                'symptom' => $fields['symptom'],
            ]);
            DB::commit();
            return response()->json($appointment);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Appointment $appointment
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        //
        try {
            DB::beginTransaction();
            $appointment = Appointment::find($id);
            $appointment->delete();
            DB::commit();
            return response()->json($appointment);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage());
        }
    }
}
