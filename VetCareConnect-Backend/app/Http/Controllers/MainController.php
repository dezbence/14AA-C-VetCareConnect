<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cure_type;
use App\Models\Cure;
use App\Models\Opening;
use App\Models\Special_opening;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Vet;

use Illuminate\Support\Facades\DB;

class MainController extends Controller
{
    public function getOwnerData() {

        $ownerData = Owner::select('name', 'email', 'address', 'phone')
        ->get();

        return response()->json($ownerData, 200);
    }

    public function getVetData() {

        $vetData = Vet::select('name', 'email', 'address', 'phone')
        ->get();

        return response()->json($vetData, 200);
    }

    public function getOpenings() {

        $openings = Vet::with('openings', 'special_openings')
        ->get();

        return response()->json($openings, 200);
    }

    public function getPets()
    {
        $pets = Pet::with('owner')
        ->where('owner_id', '=', '1')
        ->get();

        $return = array();

        foreach ($pets as $pet) {
            array_push($return, $pet['owner']);
        }


        return response()->json($return, 200);
    }

    public function getOwnerAppointments()
    {
        //petname, curename, address, date
        $appointments = Owner::with(['pets' => [
            'cures' => [
                'vet'
                ]
        ]])
            ->get();

        $appointments2 = Owner::with('pets.cures.vet')
            ->get();

        $appointments3 = Owner::select('id', 'name')
            // ->where('name','=','Nagy Lajos')
            // ->with('pets:name,owner_id.cures')
            // ->with('pets.cures:id,pet_id,vet_id,cure_type_id')
            // ->with('pet')
            ->get();

        $appointments4 = Owner::with('pets.cures.cure_type')
            ->get();

        $return = array();



        return response()->json($appointments4, 200);
    }

    public function getOwnerAppointmentsSQL()
    {
        $appoinments = DB::table('Owner')
            ->join('pet', 'owner.id', '=', 'pet.owner_id')
            ->join('cure', 'pet.id', '=', 'cure.pet_id')
            ->join('vet', 'vet.id', '=', 'cure.vet_id')
            ->join('cure_type', 'cure_type.id', '=', 'cure.cure_type_id')
            ->select('owner.email', 'pet.name', 'cure.date', 'cure_type.type', 'vet.address')
            ->get();

        return response()->json($appoinments, 200);
    }

    public function getOwnerAppointmentsORM()
    {
        $appoinments = Cure::with('cure_type', 'vet', 'pet.owner')
            ->get();

        $return = [];
        foreach ($appoinments as $appoinment) {
            $return[] = [
                'ownerId' => $appoinment->pet->owner->id,
                'petName' => $appoinment->pet->name,
                'cureType' => $appoinment->cure_type->type,
                'vetName' => $appoinment->vet->name,
                'vetAddress' => $appoinment->vet->address,
                'cureDate' => $appoinment->date

                // 'petName' => $appoinment->pets->species
                // 'cureDate' => $appoinment->pets->cures->date
             ];
         }

        return response()->json($return, 200);
    }
}
