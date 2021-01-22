<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * get all fields for current user
     */
    public function allFields(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['status']) || $data['status'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        if (!isset($data['column']) || $data['column'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $fields = Field::where('user_id', $data['user_id'])->where($data['column'], $data['status'])->orderBy('field_name', 'ASC')->get();

        return response()->json([
            "success" => true,
            "data" => $fields->all()
        ]);
    }

    /**
     * add new field
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
            $err[] = "";
        }

        if (!isset($data['seed_id']) || $data['seed_id'] == "") {
            $err[] = "";
        }

        if (!isset($data['seed_name']) || $data['seed_name'] == "") {
            $err[] = "";
        }

        if (!isset($data['seed_company_id']) || $data['seed_company_id'] == "") {
            $err[] = "";
        }

        if (!isset($data['seed_company_name']) || $data['seed_company_name'] == "") {
            $err[] = "";
        }

        if (!isset($data['seeding_status']) || $data['seeding_status'] == "") {
            $err[] = "";
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

        return response()->json([
            "success" => true,
            "message" => "not implemented"
        ]);
    }
}
