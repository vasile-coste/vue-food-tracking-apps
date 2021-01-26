<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\FieldAction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FieldController extends Controller
{
    /**
     * get all fields for current user
     */
    public function allFields(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['action']) || $data['action'] == "") {
            return response()->json([
                "success" => false,
                "message" => "No action is selected."
            ]);
        }

        $fieldsTemplate = Field::where('user_id', $data['user_id']);
        $fields = null;
        switch ($data['action']) {
            case 'seeding':
                $fields = $fieldsTemplate->where('seeding_status', '!=', 'completed');
                break;
            case 'fertilizing':
                $fields = $fieldsTemplate->where('seeding_status', 'completed')
                    ->where(function ($query) {
                        $query->orWhere('fertilizing_status', null)
                            ->orWhere('fertilizing_status', 'in_progress');
                    });
                break;
            case 'harvesting':
                $fields = $fieldsTemplate->where('fertilizing_status', 'completed')
                    ->where(function ($query) {
                        $query->orWhere('harvesting_status', null)
                            ->orWhere('harvesting_status', 'in_progress');
                    });
                break;
            default:
                return response()->json([
                    "success" => false,
                    "message" => "Something is missing, please try again later."
                ]);
        }

        return response()->json([
            "success" => true,
            "data" => ($fields->orderBy('field_name', 'ASC')->get())->all()
        ]);
    }

    /**
     * add new field
     * used in seeding action, for the rest of the action we will use update
     */
    public function addField(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];

        if (!isset($data['field_name']) || $data['field_name'] == "") {
            $err[] = "Please select or enter a field.";
        }

        if (
            !isset($data['seed_id']) || $data['seed_id'] == ""
            || !isset($data['seed_name']) || $data['seed_name'] == ""
        ) {
            $err[] = "Please select a seed.";
        }

        if (
            !isset($data['seed_company_id']) || $data['seed_company_id'] == ""
            || !isset($data['seed_company_name']) || $data['seed_company_name'] == ""
        ) {
            $err[] = "Please select a company.";
        }

        if (!isset($data['seeding_status']) || $data['seeding_status'] == "") {
            $err[] = "Action status not found.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        // add field
        $save = new Field($data);
        $save->save();

        // get the field obj
        $field = Field::find($save->id);

        return response()->json([
            "success" => true,
            "data" => $field
        ]);
    }

    /**
     * update field
     * TODO: add functionality for fertilizing and harvesting
     */
    public function updateField(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['action_name']) || $data['action_name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "not implemented"
        ]);
    }

    /**
     * get locations to display them on map
     */
    public function locations(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['field_id']) || $data['field_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $getLocations = FieldAction::where('field_id', $data['field_id']);
        if (isset($data['action_name']) && $data['action_name'] != "") {
            $getLocations->where('action_name', $data['action_name']);
        }

        $locationData = ($getLocations->get())->map(function ($item) {
            return json_decode($item['location']);
        })->flatten()->all();

        return response()->json([
            "success" => true,
            "data" => $locationData
        ]);
    }

    /**
     * add new location for current field
     */
    public function newLocation(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['field_id']) || $data['field_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['action_name']) || $data['action_name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['location']) || $data['location'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['completed'])) {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $updateField = $data['completed'];
        unset($data['completed']);

        /** update field to completed if user chose this option */
        if ($updateField) {
            Field::where('id', $data['field_id'])
                ->update([
                    $data['action_name'] . '_status' => 'completed'
                ]);
        }

        $data['location'] = (new Collection($data['location']))->toJson();
        (new FieldAction($data))->save();

        return response()->json([
            "success" => true,
            "message" => "Action was saved."
        ]);
    }
}
