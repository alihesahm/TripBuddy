<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\StorePlaceRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppointmentController extends Controller
{
    public function index()
    {
        request()->validate([
            'date'=>['nullable','date']
        ]);
        $appointment = currentUser()->appointment()
            ->when(request()->date,function (Builder $query){
                $query->whereDate('date',Carbon::parse(request()->date));
            })
            ->with(['place','place.media'])->get();
        if ($appointment->isEmpty()){
            return sendFailedResponse('no appointment found',status_code: 200);
        }
        return AppointmentResource::collection($appointment);
    }
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        $appointment = currentUser()->appointment()
            ->whereDate('date',Carbon::parse($data['date']))
            ->where('place_id',$data['place_id'])
            ->first();
        if($appointment){
            return sendFailedResponse('you already have appointment',status_code: 200);
        }
        $data['date'] = Carbon::parse($data['date']);
        $user = currentUser();
        $user->appointment()->create($data);
        return sendSuccessResponse('Appointment added successfully');
    }

    public function delete(Appointment $appointment)
    {
        if(!currentUser()->is_admin || $appointment->user_id != currentUser()->id){
           throw new AccessDeniedHttpException();
        }
        $appointment->delete();
        return sendSuccessResponse('Appointment deleted successfully');
    }

    public function show(Appointment $appointment)
    {
        if($appointment->user_id != currentUser()->id){
            throw new AccessDeniedHttpException();
        }
        return AppointmentResource::make($appointment);
    }

}
