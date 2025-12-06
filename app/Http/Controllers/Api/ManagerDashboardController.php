<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\Workout;
use App\Models\FocusArea;

class ManagerDashboardController extends BaseController
{
    /**
     * Get Dashboard Statistics
     */
    public function index()
    {
        try {
            $data = [
                'total_users'      => User::count(),
                'total_exercises'  => Exercise::count(),
                'total_equipments' => Equipment::count(),
                'total_categories' => Category::count(),
                'total_workouts'   => Workout::count(),
                'total_focus_areas'=> FocusArea::count(),
            ];

            $this->addSuccessResultKeyValue('data', $data);
            $this->addSuccessResultKeyValue('message', 'Dashboard statistics fetched successfully.');

            return $this->sendSuccessResult();

        } catch (\Exception $e) {

            $this->addFailResultKeyValue('message', 'Something went wrong.');
            $this->addFailResultKeyValue('error', $e->getMessage());
            return $this->sendFailResult();
        }
    }
}
