<?php

namespace App\Http\Controllers;

use App\Models\MapSettings;
use Illuminate\Http\Request;

class MapSettingsController extends Controller
{
    /**
     * get map settings for current user
     */
    public function map(int $user_id)
    {
        if (!isset($user_id) || $user_id == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $map = (MapSettings::where('user_id', $user_id)->get())->first();

        $map['latitude'] = (double) $map['latitude'];
        $map['longitude'] = (double) $map['longitude'];
        return response()->json([
            "success" => true,
            "data" => $map
        ]);
    }

    /**
     * Save map settings for current user 
     */
    public function update(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['id']) || $data['id'] == "") {
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

        if (!isset($data['show_joystick'])) {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if($data['show_joystick']){
            $err = [];
            if (!isset($data['latitude']) || $data['latitude'] == "") {
                $err[] = "Please enter default latitude.";
            }
            if (!isset($data['longitude']) || $data['longitude'] == "") {
                $err[] = "Please enter default longitude.";
            }

            if(count($err) > 0){
                return response()->json([
                    "success" => false,
                    "message" => implode(" ", $err)
                ]);
            }

            // replace comma with dot
            $data['latitude'] = str_replace(",", ".", $data['latitude']);
            $data['longitude'] = str_replace(",", ".", $data['longitude']);
        } else {
            if (!isset($data['precision']) || $data['precision'] == "") {
                return response()->json([
                    "success" => false,
                    "message" => "Please select GPS Precision."
                ]);
            }
        }

        MapSettings::where('id', $data['id'])->update($data);
        $getMapSettings = (MapSettings::find($data['id'])->get())->first();
        $getMapSettings['latitude'] = (double) $getMapSettings['latitude'];
        $getMapSettings['longitude'] = (double) $getMapSettings['longitude'];

        return response()->json([
            "success" => true,
            "message" => "Map Settings we updated successfully.",
            "data" => $getMapSettings
        ]);

    }
}
