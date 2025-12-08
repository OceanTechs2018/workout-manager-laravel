<?php

namespace App\Http\Controllers\Api;

use App\Constants\Columns;
use App\Constants\Keys;
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
                'total_users' => User::count(),
                'total_exercises' => Exercise::count(),
                'total_equipments' => Equipment::count(),
                'total_categories' => Category::count(),
                'total_workouts' => Workout::count(),
                'total_focus_areas' => FocusArea::count(),
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

    public function userCreationStats(Request $request)
    {
        try {

            // Extract inputs
            $year = $request->input('year');
            $startMonth = $request->input('start_month');
            $endMonth = $request->input('end_month');

            // ==========================
            // SET DEFAULT VALUES
            // ==========================
            $currentYear = now()->year;

            // If year not given → use current year
            $year = $year ?? $currentYear;

            // If months not given → full year
            if (!$startMonth && !$endMonth) {
                $startMonth = 1;
                $endMonth = 12;
            }

            // If start month only → end default = 12
            if ($startMonth && !$endMonth) {
                $endMonth = 12;
            }

            // If end month only → start default = 1
            if ($endMonth && !$startMonth) {
                $startMonth = 1;
            }

            // ==========================
            // FETCH DATA
            // ==========================
            $result = [];

            for ($m = $startMonth; $m <= $endMonth; $m++) {

                $count = User::whereYear(Columns::created_at, $year)
                    ->whereMonth(Columns::created_at, $m)
                    ->count();

                $monthName = date("F", mktime(0, 0, 0, $m, 10));

                $result[] = [
                    'month_name' => "{$monthName} - {$year}",   // <-- Updated here
                    'user_create_count' => $count,
                ];
            }

            // Response
            $this->addSuccessResultKeyValue(Keys::DATA, $result);
            $this->addSuccessResultKeyValue(Keys::MESSAGE, 'User creation statistics fetched successfully.');

            return $this->sendSuccessResult();

        } catch (\Exception $e) {

            $this->addFailResultKeyValue(Keys::MESSAGE, 'Something went wrong.');
            $this->addFailResultKeyValue(Keys::ERROR, $e->getMessage());
            return $this->sendFailResult();
        }
    }
}
