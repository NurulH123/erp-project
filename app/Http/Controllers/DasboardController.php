<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DasboardController extends Controller
{
    public function salesReportDashboard(Request $request, $arg)
    {

        if ($arg == 'today') {

            // Ambil tanggal hari ini menggunakan Carbon
            $today = date("Y-m-d");
            // dd($today);

            // Generate daftar jam dalam sehari menggunakan Carbon
            $hoursInDay = [];
            for ($hour = 0; $hour < 24; $hour++) {

                $hoursInDay[] = $hour;

            }

            $omzet = [];
            $customers = [];
            foreach ($hoursInDay as $hours) {

                $omzetPerHour = Order::whereBetween('created_at', ["$today $hours:00:00", "$today $hours:59:59"])
                    ->where('special', '=', "false")
                    ->sum('total_price');
                // ->count();

                $customersPerHour = Order::whereBetween('created_at', ["$today $hours:00:00", "$today $hours:59:59"])
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $omzetPerHour;
                $customers[] = $customersPerHour;

            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $data['orders'] = Order::whereDate('created_at', $today)->where('special', '=', "false")->count();
            $data['omzet'] = Order::whereDate('created_at', $today)->where('special', '=', "false")->sum('total_price');
            $cstmr = Order::select('customer_id')->whereDate('created_at', $today)->where('special', '=', "false")->groupBy('customer_id')->get();
            $data['customer'] = $cstmr->count();

            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        } elseif ($arg == 'week') {

            $startOfWeek = Carbon::today()->startOfWeek();
            $endOfWeek = Carbon::today()->endOfWeek();

            // Mengisi rentang tanggal
            $dateRange = [];
            $currentDate = $startOfWeek;
            while ($currentDate <= $endOfWeek) {
                $dateRange[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // dd($dateRange);
            $tgl = $dateRange;
            $omzet = [];
            $customers = [];
            foreach ($dateRange as $dateRange) {
                $order = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->sum('total_price');


                $customer = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $order;
                $customers[] = $customer;
            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $startOfWeek = Carbon::today()->startOfWeek();
            $endOfWeek = Carbon::today()->endOfWeek();

            $endOfWeek->addDay(); // Tambahkan satu hari untuk mendapatkan akhir minggu


            $data['orders'] = Order::whereBetween('created_at', [$tgl[0] . ' 00:00:00', $tgl[6] . ' 23:59:59'])->where('special', '=', "false")->count();
            $data['omzet'] = Order::whereBetween('created_at', [$tgl[0] . ' 00:00:00', $tgl[6] . ' 23:59:59'])->where('special', '=', "false")->sum('total_price');
            $cstmr = Order::select('customer_id')->whereBetween('created_at', [$tgl[0] . ' 00:00:00', $tgl[6] . ' 23:59:59'])->where('special', '=', "false")->groupBy('customer_id')->get();
            $data['customer'] = $cstmr->count();


            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        } elseif ($arg == 'month') {

            // Tanggal awal dan akhir untuk satu bulan ini
            $startDate = Carbon::now()->startOfMonth(); // Mengambil tanggal awal bulan ini
            $endDate = Carbon::now()->endOfMonth(); // Mengambil tanggal akhir bulan ini
            $currentMonth = Carbon::now()->month;

            // Mengisi rentang tanggal
            $dateRange = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $dateRange[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // dd($dateRange);

            $omzet = [];
            $customers = [];
            foreach ($dateRange as $dateRange) {
                $order = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->sum('total_price');


                $customer = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $order;
                $customers[] = $customer;
            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $data['orders'] = Order::whereMonth('created_at', $currentMonth)->where('special', '=', "false")->count();
            $data['omzet'] = Order::whereMonth('created_at', $currentMonth)->where('special', '=', "false")->sum('total_price');
            $cstmr = Order::select('customer_id')->whereMonth('created_at', $currentMonth)->where('special', '=', "false")->groupBy('customer_id')->get();
            $data['customer'] = $cstmr->count();
            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        } elseif ($arg == 'year') {

            // $month = Carbon::now()->month;
            $year = Carbon::now()->year;

            $startDate = Carbon::now()->startOfYear(); // Tanggal awal tahun ini
            $endDate = Carbon::now()->endOfYear(); // Tanggal akhir tahun ini

            // Mengisi rentang tanggal
            $monthRange = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $monthRange[] = $currentDate->format('m');
                $currentDate->addMonth();
            }

            // Mengambil data order dari database

            $omzet = [];
            $customers = [];
            foreach ($monthRange as $month) {
                $order = Order::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('special', '=', "false")
                    ->sum('total_price');

                $customer = Order::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $order;
                $customers[] = $customer;
            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $data['orders'] = Order::whereYear('created_at', $year)->where('special', '=', "false")->count();
            $data['omzet'] = Order::whereYear('created_at', $year)->where('special', '=', "false")->sum('total_price');
            $cstmr = Order::select('customer_id')->whereYear('created_at', $year)->where('special', '=', "false")->groupBy('customer_id')->get();
            $data['customer'] = $cstmr->count();

            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        } elseif ($arg == 'custome') {

            $startOfWeek = Carbon::parse($request->startOfDate);
            $endOfWeek = Carbon::parse($request->endOfDate);

            // Mengisi rentang tanggal
            $dateRange = [];
            $currentDate = $startOfWeek;
            while ($currentDate <= $endOfWeek) {
                $dateRange[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }
            // $tgl = $dateRange;
            // dd($dateRange);

            $omzet = [];
            $customers = [];
            foreach ($dateRange as $dateRange) {
                $order = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->sum('total_price');


                $customer = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $order;
                $customers[] = $customer;
            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $tgl = [Carbon::parse($request->startOfDate), Carbon::parse($request->endOfDate)->addDay()];

            $data['orders'] = Order::whereBetween('created_at', $tgl)->where('special', '=', "false")->count();
            $data['omzet'] = Order::whereBetween('created_at', $tgl)->where('special', '=', "false")->sum('total_price');
            $cstmr = Order::select('customer_id')->whereBetween('created_at', $tgl)->where('special', '=', "false")->groupBy('customer_id')->get();
            $data['customer'] = $cstmr->count();

            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        } elseif ($arg == 'default') {

            // Tanggal awal dan akhir untuk satu bulan ini
            $startDate = Carbon::now()->startOfMonth(); // Mengambil tanggal awal bulan ini
            $endDate = Carbon::now()->endOfMonth(); // Mengambil tanggal akhir bulan ini


            // Mengisi rentang tanggal
            $dateRange = [];
            $currentDate = $startDate;
            while ($currentDate <= $endDate) {
                $dateRange[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // dd($dateRange);

            $omzet = [];
            $customers = [];
            foreach ($dateRange as $dateRange) {
                $order = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->sum('total_price');


                $customer = Order::whereDate('created_at', $dateRange)
                    ->where('special', '=', "false")
                    ->distinct('customer_id')
                    ->count('customer_id');

                $omzet[] = $order;
                $customers[] = $customer;
            }

            $sales['sales'] = $omzet;
            $sales['customers'] = $customers;

            $data['orders'] = Order::where('special', '=', "false")->count();
            $data['omzet'] = Order::where('special', '=', "false")->sum('total_price');
            $data['customers'] = Customer::all()->count();

            $out_data['sales'] = $sales;
            $out_data['sum'] = $data;

        }

        return response()->json(['data' => $out_data]);
    }


    public function csReportDashboard($arg)
    {
        if ($arg == 'today') {
            $customer_service = Role::where('name', 'Customer Service')->first();

            $today = date("Y-m-d");

            $data_cs = [];

            foreach ($customer_service->user as $value) {
                $pencapaian = Order::whereDate('created_at', $today)->where('user_id', $value['id'])->where('special', '=', "false")->sum('total_price');
                $cs = $value->username;

                $out['cs'] = $cs;
                $out['sales'] = $pencapaian;

                $data_cs[] = $out;

            }

        } elseif ($arg == 'month') {
            $customer_service = Role::where('name', 'Customer Service')->first();

            $month = Carbon::now()->month;

            $data_cs = [];

            foreach ($customer_service->user as $value) {
                $pencapaian = Order::whereMonth('created_at', $month)->where('user_id', $value['id'])->where('special', '=', "false")->sum('total_price');
                $cs = $value->username;

                $out['cs'] = $cs;
                $out['sales'] = $pencapaian;

                $data_cs[] = $out;

            }
        }
        $data['customer_service'] = $data_cs;

        return response()->json(['data' => $data]);

    }


    public function reportDashboard()
    {

        $data['orders'] = Order::where('special', '=', "false")->count();
        $data['omzet'] = Order::where('special', '=', "false")->sum('total_price');
        $data['customers'] = Customer::all()->count();


        return response()->json(['data' => $data]);

    }
}
