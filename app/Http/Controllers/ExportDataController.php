<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Exports\BingkaiExport;
use App\Exports\CustomerOrderExport;
use App\Exports\CustomerPhoneExport;
use Illuminate\Http\Request;
use App\Exports\DataExport;
use App\Models\Order;
use App\Models\Role;
use App\Models\ProductCategory;
use App\Models\DataLead;
use Carbon\Carbon;
use App\Exports\DataFrameExport;
use App\Exports\DataLeadExport;
use App\Exports\DataProductSaleExport;
use App\Exports\DataFrameSaleExport;
use App\Exports\OrderReportExport;
use App\Exports\OrderReport;
use App\Exports\ResiReportExport;
use App\Exports\SummaryExport;
use App\Models\Checkout;
use App\Models\OrderReport as OrderReportAlias;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\ShippingReportExport;
use App\Models\Product;

class ExportDataController extends Controller
{
    public function sales(Request $request)
    {

        $user = $request->user();

        return Excel::download(new DataExport($user), 'Sales.xlsx');
    }

    public function indexSales(Request $request)
    {

        $user = $request->user();

        if ($user->role->name == 'Administrator') {
            $order = Order::with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                ->get()
            ;
        } else {
            $order = Order::where('user_id', $user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                ->get()
            ;
        }

        $out_data = [];
        foreach ($order as $data) {
            $product = "";
            foreach ($data->product as $prod) {
                $product .= $prod->name . ', ';
            }
            $sales['tanggal'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['nama_cs'] = $data->user->username;
            $sales['sumber_lead'] = $data->sumber_lead;
            $sales['paket'] = $product;
            $sales['harga'] = $data->sub_total;
            $sales['ongkir'] = $data->final_ongkir;
            $sales['total_price'] = $data->total_price;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);

    }

    public function indexSalesPaginate(Request $request, $page)
    {

        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;


        if ($user->role->name == 'Administrator') {
            $order = Order::with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page)
                // ->get()
            ;
        } else {
            $order = Order::where('user_id', $user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page)
                // ->get()
            ;
        }

        $out_data = [];
        foreach ($order as $data) {
            $product = "";
            foreach ($data->product as $prod) {
                $product .= $prod->name . ', ';
            }
            $sales['tanggal'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['nama_cs'] = $data->user->username;
            $sales['sumber_lead'] = $data->sumber_lead;
            $sales['paket'] = $product;
            $sales['harga'] = $data->sub_total;
            $sales['ongkir'] = $data->final_ongkir;
            $sales['total_price'] = $data->total_price;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);

    }

    public function filterSalesPaginate(Request $request, $page)
    {

        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        if ($user->role->name == 'Administrator') {
            $order = Order::with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->filter($request)->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page)
                // ->toSql()
                // ->get()
            ;
            $total = Order::filter($request)->count();

        } else {
            $order = Order::where('user_id', $user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->filter($request)->orderBy('created_at', 'desc')
                ->paginate($size, ['*'], 'page', $page)
                // ->get()
            ;

            $total = Order::where('user_id', $user->id)->filter($request)->count();
        }

        $out_data = [];
        foreach ($order as $data) {
            $product = "";
            foreach ($data->product as $prod) {
                $product .= $prod->name . ', ';
            }
            $sales['tanggal'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['nama_cs'] = $data->user->username;
            $sales['sumber_lead'] = $data->sumber_lead;
            $sales['paket'] = $product;
            $sales['harga'] = $data->sub_total;
            $sales['ongkir'] = $data->final_ongkir;
            $sales['total_price'] = $data->total_price;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data, $total], 200);

    }

    public function leads(Request $request)
    {

        $user = $request->user();

        $startOfDate = $request->startOfDate;
        $endOfDate = $request->endOfDate;

        return Excel::download(new DataLeadExport($user, $startOfDate, $endOfDate), 'Leads.xlsx');
    }

    public function indexLeads(Request $request)
    {
        $user = $request->user();

        $startDate = $request->start_date ?? Carbon::today()->subDays(7);
        $endDate = $request->end_date ?? Carbon::now();


        if ($user->role->name == "Administrator") {
            // $data_leads = DataLead::whereBetween('created_at', [$startDate, $endDate])->with('user')->orderBy('id', 'desc')->get();
            $data_leads = DataLead::with('user')->orderBy('id', 'desc')->get();

        } else {
            // $data_leads = DataLead::where('user_id', $user->id)->with('user')->orderBy('id', 'desc')->get();
            $data_leads = DataLead::where('user_id', $user->id)->with('user')->orderBy('id', 'desc')->get();
        }

        $out_data = [];
        foreach ($data_leads as $data) {
            $order = Order::whereDate('created_at', $data->created_at->toDateString())->where('user_id', $data->user_id)->where('sumber_lead', $data->sumber_lead)->get();
            $leads['id'] = $data->id;
            $leads['tanggal'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $leads['nama_cs'] = $data->user->username;
            $leads['sumber_lead'] = $data->sumber_lead;
            $leads['jumlah_lead'] = $data->jumlah_lead;
            $leads['closing'] = $order->count();
            if ($data->jumlah_lead == 0) {
                $leads['%cr'] = 0;
            } else {
                $leads['%cr'] = round((100 / $data->jumlah_lead) * $order->count());
            }
            $leads['omset'] = $order->sum('total_price');

            $out_data[] = $leads;
        }

        return response()->json(['data' => $out_data], 200);

    }

    public function indexLeadsPaginate(Request $request, $page)
    {
        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        $startDate = $request->start_date ?? Carbon::today()->subDays(7);
        $endDate = $request->end_date ?? Carbon::now();


        if ($user->role->name == "Administrator") {
            // $data_leads = DataLead::whereBetween('created_at', [$startDate, $endDate])->with('user')->orderBy('id', 'desc')->get();
            $data_leads = DataLead::with('user')->orderBy('id', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page);

        } else {
            // $data_leads = DataLead::where('user_id', $user->id)->with('user')->orderBy('id', 'desc')->get();
            $data_leads = DataLead::where('user_id', $user->id)->with('user')->orderBy('id', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page)
            ;
        }

        $out_data = [];
        foreach ($data_leads as $data) {
            $order = Order::whereDate('created_at', $data->created_at->toDateString())->where('user_id', $data->user_id)->where('sumber_lead', $data->sumber_lead)->get();
            $leads['id'] = $data->id;
            $leads['tanggal'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $leads['nama_cs'] = $data->user->username;
            $leads['sumber_lead'] = $data->sumber_lead;
            $leads['jumlah_lead'] = $data->jumlah_lead;
            $leads['closing'] = $order->count();
            if ($data->jumlah_lead == 0) {
                $leads['%cr'] = 0;
            } else {
                $leads['%cr'] = round((100 / $data->jumlah_lead) * $order->count());
            }
            $leads['omset'] = $order->sum('total_price');

            $out_data[] = $leads;
        }

        return response()->json(['data' => $out_data], 200);

    }


    public function frames(Request $request)
    {
        $user = $request->user();

        return Excel::download(new BingkaiExport($user), 'Rekap_Bingkai.xlsx');

    }

    public function indexFrames(Request $request)
    {

        $user = $request->user();
        $role = Role::where('id', $user->role_id)->first()->name;

        if ($role == 'Customer Service') {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.user_id', '=', $user->id)
                ->where('orders.special', '=', 'false')
                ->get()
                ->groupBy(['date', 'product_id']);
        } else {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.special', '=', 'false')
                ->get()
                ->groupBy(['date', 'product_id']);
        }
        $frames = [];
        foreach ($out as $data) {


            foreach ($data as $data_child1) {
                $quantity_product = $data_child1->sum('quantity');

                $categories = ProductCategory::where('product_id', $data_child1[0]->product_id)->with('category')->get();
                foreach ($categories as $category) {
                    $cek = $data_child1[0]->date . "-" . $category->category->name;
                    if (isset($frames["$cek"])) {
                        $frames["$cek"]['qty'] += $category->quantity * $quantity_product;
                    } else {
                        $frm['date'] = Carbon::parse($data_child1[0]->created_at)->translatedFormat('Y-m-d');
                        $frm['name'] = $category->category->name;
                        $frm['qty'] = $category->quantity * $quantity_product;

                        $frames[$cek] = $frm;
                    }

                }
            }
        }

        $data_out = [];
        foreach ($frames as $data1) {
            $data_out[] = $data1;
        }

        return response()->json(['data' => $data_out], 200);
    }

    public function indexFramesPaginate(Request $request, $page)
    {

        $user = $request->user();
        $role = Role::where('id', $user->role_id)->first()->name;
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        if ($role == 'Customer Service') {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.user_id', '=', $user->id)
                ->where('orders.special', '=', 'false')
                // ->get()
                ->groupBy(['date', 'product_id'])
                ->paginate($size, ['*'], 'page', $page);
        } else {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.special', '=', 'false')
                // ->get()
                ->groupBy(['date', 'product_id'])
                ->paginate($size, ['*'], 'page', $page);
        }
        $frames = [];
        foreach ($out as $data) {


            foreach ($data as $data_child1) {
                $quantity_product = $data_child1->sum('quantity');

                $categories = ProductCategory::where('product_id', $data_child1[0]->product_id)->with('category')->get();
                foreach ($categories as $category) {
                    $cek = $data_child1[0]->date . "-" . $category->category->name;
                    if (isset($frames["$cek"])) {
                        $frames["$cek"]['qty'] += $category->quantity * $quantity_product;
                    } else {
                        $frm['date'] = Carbon::parse($data_child1[0]->created_at)->translatedFormat('Y-m-d');
                        $frm['name'] = $category->category->name;
                        $frm['qty'] = $category->quantity * $quantity_product;

                        $frames[$cek] = $frm;
                    }

                }
            }
        }

        $data_out = [];
        foreach ($frames as $data1) {
            $data_out[] = $data1;
        }

        return response()->json(['data' => $data_out], 200);
    }

    public function filterFramesPaginate(Request $request, $page)
    {

        $user = $request->user();
        $role = Role::where('id', $user->role_id)->first()->name;
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        if ($role == 'Customer Service') {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                ->where('orders.user_id', '=', $user->id)
                ->where('orders.special', '=', 'false')
                // ->get()
                ->groupBy(['date', 'product_id'])
                ->paginate($size, ['*'], 'page', $page);
        } else {
            $out = Checkout::select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.created_at as created_at', \DB::raw('DATE(checkouts.created_at) as date'))
                // ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
                // ->where('orders.special', '=', 'false')
                ->whereHas('order', function ($query) {
                    $query->where('special', 'false');
                })
                // ->get()
                ->filter($request)
                ->groupBy(['date', 'product_id', 'order_code', 'quantity', 'created_at'])
                ->paginate($size, ['*'], 'page', $page);
            return response()->json(['data' => $out], 200);

        }
        $frames = [];
        foreach ($out as $data) {


            foreach ($data as $data_child1) {
                $quantity_product = $data_child1->sum('quantity');

                $categories = ProductCategory::where('product_id', $data_child1[0]->product_id)->with('category')->get();
                foreach ($categories as $category) {
                    $cek = $data_child1[0]->date . "-" . $category->category->name;
                    if (isset($frames["$cek"])) {
                        $frames["$cek"]['qty'] += $category->quantity * $quantity_product;
                    } else {
                        $frm['date'] = Carbon::parse($data_child1[0]->created_at)->translatedFormat('Y-m-d');
                        $frm['name'] = $category->category->name;
                        $frm['qty'] = $category->quantity * $quantity_product;

                        $frames[$cek] = $frm;
                    }

                }
            }
        }

        $data_out = [];
        foreach ($frames as $data1) {
            $data_out[] = $data1;
        }

        return response()->json(['data' => $data_out], 200);
    }

    public function summary(Request $request)
    {
        return Excel::download(new SummaryExport($request->user(), $request->startOfDate, $request->endOfDate), 'Summary.xlsx');
    }


    public function orderReport(Request $request)
    {
        $user = $request->user();
        $startOfDate = $request->startOfDate;
        $endOfDate = $request->endOfDate;

        return Excel::download(new OrderReportExport($user, $startOfDate, $endOfDate), 'Order_Reports.xlsx');
    }

    public function indexOrderReport(Request $request)
    {
        $user = $request->user();

        if ($user->role->name == 'Administrator') {
            $orders = Order::with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::where('user_id', $user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')->get();
        }

        $out_data = [];
        foreach ($orders as $data) {
            $product = [];
            $checkouts = Checkout::where('order_code', $data->order_code)->with('product')->get();
            foreach ($checkouts as $checkout) {
                $out['name'] = $checkout->product->name;
                $out['quantity'] = $checkout->quantity;

                $product[] = $out;
            }

            $sales['created'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['modified'] = Carbon::parse($data->modified_at)->translatedFormat('Y-m-d');
            $sales['CS_Name'] = $user->username;
            $sales['customer'] = $data->customer->surename;
            $sales['status'] = $data->status;
            $sales['product'] = $product;
            $sales['ongkir'] = $data->final_ongkir;
            $sales['total_price'] = $data->total_price;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);
    }

    public function indexOrderReportPaginate(Request $request)
    {
        $user = $request->user();
        $defaultPage = 1;
        $page = $request->query('page', $defaultPage);
        $size = 20;

        if ($user->role->name == 'Administrator') {
            $orders = Order::with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page);
        } else {
            $orders = Order::where('user_id', $user->id)->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page);
        }

        $out_data = [];
        foreach ($orders as $data) {
            $product = [];
            $checkouts = Checkout::where('order_code', $data->order_code)->with('product')->get();
            foreach ($checkouts as $checkout) {
                $out['name'] = $checkout->product->name;
                $out['quantity'] = $checkout->quantity;

                $product[] = $out;
            }

            $sales['created'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['modified'] = Carbon::parse($data->modified_at)->translatedFormat('Y-m-d');
            $sales['CS_Name'] = $user->username;
            $sales['customer'] = $data->customer->surename;
            $sales['status'] = $data->status;
            $sales['product'] = $product;
            $sales['ongkir'] = $data->final_ongkir;
            $sales['total_price'] = $data->total_price;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);
    }

    public function resiReport(Request $request)
    {

        $user = $request->user();

        return Excel::download(new ResiReportExport($user), 'Resi_Reports.xlsx');
    }


    public function indexResiReport(Request $request)
    {
        $user = $request->user();

        if ($user->role->name == 'Administrator') {
            $orders = Order::whereNotNull('nomor_resi')->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')->get();
        } else {
            $orders = Order::where('user_id', $user->id)->whereNotNull('nomor_resi')->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')->get();
        }

        $out_data = [];
        foreach ($orders as $data) {
            $weight = 0;
            $checkouts = Checkout::where('order_code', $data->order_code)->with('product')->get();
            foreach ($checkouts as $checkout) {
                $product_nett = $checkout->product->weight;
                $nett = ($product_nett * $checkout->quantity) / 1000;
                $weight += $nett;
            }

            $sales['created'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['modified'] = Carbon::parse($data->modified_at)->translatedFormat('Y-m-d');
            $sales['surename'] = $data->customer->surename;
            $sales['phone'] = $data->customer->phone;
            $sales['province'] = $data->address->province;
            $sales['city'] = $data->address->city;
            $sales['nomor_resi'] = $data->nomor_resi;
            $sales['weight'] = $weight;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);
    }

    public function indexResiReportPaginate(Request $request, $page)
    {
        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        if ($user->role->name == 'Administrator') {
            $orders = Order::whereNotNull('nomor_resi')->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page);
        } else {
            $orders = Order::where('user_id', $user->id)->whereNotNull('nomor_resi')->with('user')->with('customer')->with('product')->with('address')->with('checkout')->with('payment')->where('created_at', '>', '2023-09-01')->orderBy('created_at', 'desc')
                // ->get()
                ->paginate($size, ['*'], 'page', $page);
        }

        $out_data = [];
        foreach ($orders as $data) {
            $weight = 0;
            $checkouts = Checkout::where('order_code', $data->order_code)->with('product')->get();
            foreach ($checkouts as $checkout) {
                $product_nett = $checkout->product->weight;
                $nett = ($product_nett * $checkout->quantity) / 1000;
                $weight += $nett;
            }

            $sales['created'] = Carbon::parse($data->created_at)->translatedFormat('Y-m-d');
            $sales['modified'] = Carbon::parse($data->modified_at)->translatedFormat('Y-m-d');
            $sales['surename'] = $data->customer->surename;
            $sales['phone'] = $data->customer->phone;
            $sales['province'] = $data->address->province;
            $sales['city'] = $data->address->city;
            $sales['nomor_resi'] = $data->nomor_resi;
            $sales['weight'] = $weight;

            $out_data[] = $sales;
        }

        return response()->json(['data' => $out_data], 200);
    }

    public function IndexShippingReport(Request $request)
    {

        $user = $request->user();


        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.order_code',
                'orders.ongkos_kirim',
                'orders.potongan_ongkir',
                'orders.final_ongkir',
                'orders.status',
                \DB::raw('DATE(orders.created_at) as created_date'),
                \DB::raw('DATE(orders.updated_at) as updated_date'),
                'users.username',
                'addresses.province',
                'addresses.city',
                'addresses.district',
                'addresses.postal_code',
                'addresses.full_address',
                'customers.surename',
                'customers.phone',
                'customers.email'
            )
            ->where('orders.created_at', '>', '2023-09-01')->orderBy('orders.created_at', 'desc')->get();

        return response()->json(['order' => $orders], 200);
    }

    public function IndexShippingReportPaginate(Request $request, $page)
    {

        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        $orders = Order::join('users', 'orders.user_id', '=', 'users.id')
            ->join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.order_code',
                'orders.ongkos_kirim',
                'orders.potongan_ongkir',
                'orders.final_ongkir',
                'orders.status',
                \DB::raw('DATE(orders.created_at) as created_date'),
                \DB::raw('DATE(orders.updated_at) as updated_date'),
                'users.username',
                'addresses.province',
                'addresses.city',
                'addresses.district',
                'addresses.postal_code',
                'addresses.full_address',
                'customers.surename',
                'customers.phone',
                'customers.email'
            )
            ->where('orders.created_at', '>', '2023-09-01')->orderBy('orders.created_at', 'desc')->paginate($size, ['*'], 'page', $page);

        return response()->json(['order' => $orders], 200);
    }

    public function customerOrderReport()
    {
        return Excel::download(new CustomerOrderExport(), 'Customer_Orders_Report.xlsx');
    }

    public function IndexCustomerOrderReport(Request $request)
    {

        $user = $request->user();

        $customerOrders = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('checkouts', 'orders.order_code', '=', 'checkouts.order_code')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'customers.surename',
                'customers.phone',
                'customers.email',
                'customers.id',
                'users.username',
                'orders.status',
                \DB::raw('SUM(checkouts.quantity) as total_product'),
                \DB::raw('SUM(orders.total_price) as total_price'),
            )
            ->groupBy('customers.surename', 'customers.phone', 'customers.email', 'customers.id', 'orders.status', 'users.username')
            ->get();

        foreach ($customerOrders as $customerOrder) {
            $orders = Order::where('customer_id', $customerOrder->id)->get();

            $tanggal = [];
            foreach ($orders as $order) {
                $tanggal[] = Carbon::parse($order->created_at)->translatedFormat('Y-m-d');
            }
            $customerOrder['total_order'] = (string) Order::where('customer_id', $customerOrder->id)->count();
            $customerOrder['date'] = $tanggal;

        }

        $customerOrders = $customerOrders->sortByDesc('id');
        $out_customerOrders = [];
        foreach ($customerOrders as $cstmrordr) {
            $out_customerOrders[] = $cstmrordr;

        }


        return response()->json(['data' => $out_customerOrders], 200);

    }

    public function IndexCustomerOrderReportPaginate(Request $request, $page)
    {

        $user = $request->user();
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;

        $customerOrders = Order::join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('checkouts', 'orders.order_code', '=', 'checkouts.order_code')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'customers.surename',
                'customers.phone',
                'customers.email',
                'customers.id',
                'users.username',
                'orders.status',
                \DB::raw('SUM(checkouts.quantity) as total_product'),
                \DB::raw('SUM(orders.total_price) as total_price'),
            )
            ->groupBy('customers.surename', 'customers.phone', 'customers.email', 'customers.id', 'orders.status', 'users.username')
            ->paginate($size, ['*'], 'page', $page);

        foreach ($customerOrders as $customerOrder) {
            $orders = Order::where('customer_id', $customerOrder->id)->get();

            $tanggal = [];
            foreach ($orders as $order) {
                $tanggal[] = Carbon::parse($order->created_at)->translatedFormat('Y-m-d');
            }
            $customerOrder['total_order'] = (string) Order::where('customer_id', $customerOrder->id)->count();
            $customerOrder['date'] = $tanggal;

        }

        $customerOrders = $customerOrders->sortByDesc('id');
        $out_customerOrders = [];
        foreach ($customerOrders as $cstmrordr) {
            $out_customerOrders[] = $cstmrordr;

        }


        return response()->json(['data' => $out_customerOrders], 200);

    }


    public function ShippingReport(Request $request)
    {
        $user = $request->user();
        return Excel::download(new ShippingReportExport($user), 'shipping_report.xlsx');
    }

    public function CustomerPhone(Request $request)
    {
        return Excel::download(new CustomerPhoneExport, 'customer_report.xlsx');

    }

    public function ProductReport(Request $request)
    {
        $products = Product::select('products.id', 'products.name', DB::raw('COUNT(checkouts.product_id) as total_order'), DB::raw('SUM(checkouts.quantity) as total_item'))
            ->join('checkouts', 'products.id', '=', 'checkouts.product_id')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_order', 'desc')
            ->get();

        return response()->json(['data' => $products], 200);

    }

    public function DetailProductReport(Request $request, $id)
    {
        $products = Product::select('customers.surename as customer_name', 'customers.phone as phone', DB::raw('COUNT(checkouts.product_id) as total_order'), DB::raw('SUM(checkouts.quantity) as total_item'))
            ->join('checkouts', 'products.id', '=', 'checkouts.product_id')
            ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('products.id', $id)
            ->groupBy('customer_name', 'phone')
            ->orderBy('total_item', 'desc')
            ->get();
        $infoproducts = Product::select('products.id', 'products.name', DB::raw('COUNT(checkouts.product_id) as total_order'), DB::raw('SUM(checkouts.quantity) as total_item'))
            ->join('checkouts', 'products.id', '=', 'checkouts.product_id')
            ->where('products.id', $id)
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_order', 'desc')
            ->get();
        $data['info'] = $infoproducts;
        $data['products'] = $products;
        return response()->json(['data' => $data], 200);
    }
}
