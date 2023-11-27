<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\CustomerController;
use App\Models\Address;
use App\Models\Checkout;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\History;
use App\Models\OrderPhoto;
use App\Models\OrderReport;
use App\Models\Return_order;
use App\Models\Sending;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function indexOrder(Request $request)
    {
        // $hal = $page ?? 1;
        // $page = $request->query('page', $hal);
        // $size = 20;
        $user = $request->user();

        if ($user->role->name == 'Customer Service') {
            $pesanan = Order::where('user_id', $user->id)
                ->with('customer')
                ->with('payment')
                ->with('sending')
                ->with('address')
                ->with('product')
                ->OrderBy('id', 'desc')
                ->get()
                // ->paginate($size,['*'], 'page', $page);
            ;

        } else {
            $pesanan = Order::with('customer')
                ->with('payment')
                ->with('sending')
                ->with('address')
                ->with('product')
                ->OrderBy('id', 'desc')
                ->get()
                // ->paginate($size,['*'], 'page', $page);
            ;
        }

        $data = [];

        foreach ($pesanan as $orde) {
            $order['id'] = $orde?->id;
            $order['order_code'] = $orde?->order_code;
            $order['sub_total'] = $orde?->sub_total;
            $order['ongkos_kirim'] = $orde?->ongkos_kirim;
            $order['potongan_ongkir'] = $orde?->potongan_ongkir;
            $order['final_ongkir'] = $orde?->final_ongkir;
            $order['total_price'] = $orde?->total_price;
            $order['description'] = $orde?->description;
            $order['nomor_resi'] = $orde?->nomor_resi;
            $order['tanggal_resi'] = $orde?->tanggal_resi;
            $order['note'] = $orde?->note;
            $order['special'] = $orde?->special;
            $order['status'] = $orde?->status;
            $order['created_at'] = date("Y-m-d", strtotime($orde?->created_at));
            $order['modified_at'] = date("Y-m-d", strtotime($orde?->updated_at));
            $order['createdby'] = User::where('id', History::where('order_id', $orde?->id)->first()?->user_id)->first()?->username;
            // $order['modifiedby'] = User::where('id', History::where('order_id', $orde?->id)->last()->user_id)->first()->username;
            $order['modifiedby'] = User::where('id', function ($query) use ($orde) {
                $query->select('user_id')
                    ->from('histories')
                    ->where('order_id', $orde?->id)
                    ->latest()
                    ->limit(1);
            })->value('username');

            $order['customer'] = [
                'surename' => $orde?->customer->surename,
                'first_name' => $orde?->customer->first_name,
                'last_name' => $orde?->customer->last_name,
                'email' => $orde?->customer->email,
                'phone' => $orde?->customer->phone,
                'second_phone' => $orde?->customer->second_phone,
            ];
            $order['payment'] = $orde?->payment->method;
            $order['sending'] = $orde?->sending->sender;
            $order['address'] = [
                'full_address' => $orde?->address->full_address,
                'province' => $orde?->address->province,
                'city' => $orde?->address->city,
                'district' => $orde?->address->district,
                'postal_code' => $orde?->address->postal_code,
            ];

            if ($orde?->coupon == null) {
                $order['coupon'] = $orde?->coupon;
            } else {
                $order['coupon'] = $orde?->coupon;
            }

            // Product

            $product = Checkout::where('order_code', $orde?->order_code)->get();


            $product_out = [];
            foreach ($product as $prod) {
                $product_in['product_id'] = $prod->product->id;
                $product_in['name'] = $prod->product->name;
                $product_in['quantity'] = $prod->quantity;
                $product_in['price'] = $prod->product->price;

                $product_out[] = $product_in;
            }

            $order['product'] = $product_out;
            $data[] = $order;
        }

        $total_data_orderan = Order::all()->count();
        return response()->json(['data' => $data, 'total' => $total_data_orderan]);
    }

    public function indexOrderPaginate(Request $request, $page)
    {
        try {
            $hal = $page ?? 1;
            $page = $request->query('page', $hal);
            $size = 20;
            $user = $request->user();

            if ($user->role->name == 'Customer Service') {
                $pesanan = Order::where('user_id', $user->id)
                    ->with('customer')
                    ->with('payment')
                    ->with('sending')
                    ->with('address')
                    ->with('product')
                    ->OrderBy('created_at', 'desc')
                    ->paginate($size, ['*'], 'page', $page);
                ;

            } else {
                $pesanan = Order::with('customer')
                    ->with('payment')
                    ->with('sending')
                    ->with('address')
                    ->with('product')
                    ->OrderBy('created_at', 'desc')
                    ->paginate($size, ['*'], 'page', $page);
                ;
            }
            // dd($pesanan);
            $data = [];

            foreach ($pesanan as $orde) {
                $order['id'] = $orde?->id;
                $order['order_code'] = $orde?->order_code;
                $order['sub_total'] = $orde?->sub_total;
                $order['ongkos_kirim'] = $orde?->ongkos_kirim;
                $order['potongan_ongkir'] = $orde?->potongan_ongkir;
                $order['final_ongkir'] = $orde?->final_ongkir;
                $order['total_price'] = $orde?->total_price;
                $order['description'] = $orde?->description;
                $order['nomor_resi'] = $orde?->nomor_resi;
                $order['tanggal_resi'] = $orde?->tanggal_resi;
                $order['note'] = $orde?->note;
                $order['special'] = $orde?->special;
                $order['status'] = $orde?->status;
                $order['created_at'] = date("Y-m-d", strtotime($orde?->created_at));
                $order['modified_at'] = date("Y-m-d", strtotime($orde?->updated_at));
                $order['createdby'] = User::where('id', History::where('order_id', $orde?->id)->first()?->user_id)->first()?->username;
                // $order['modifiedby'] = User::where('id', History::where('order_id', $orde?->id)->last()->user_id)->first()->username;
                $order['modifiedby'] = User::where('id', function ($query) use ($orde) {
                    $query->select('user_id')
                        ->from('histories')
                        ->where('order_id', $orde?->id)
                        ->latest()
                        ->limit(1);
                })->value('username');

                $order['customer'] = [
                    'surename' => $orde?->customer->surename,
                    'first_name' => $orde?->customer->first_name,
                    'last_name' => $orde?->customer->last_name,
                    'email' => $orde?->customer->email,
                    'phone' => $orde?->customer->phone,
                    'second_phone' => $orde?->customer->second_phone,
                ];
                $order['payment'] = $orde?->payment->method;
                $order['sending'] = $orde?->sending->sender;
                $order['address'] = [
                    'full_address' => $orde?->address->full_address,
                    'province' => $orde?->address->province,
                    'city' => $orde?->address->city,
                    'district' => $orde?->address->district,
                    'postal_code' => $orde?->address->postal_code,
                ];

                if ($orde?->coupon == null) {
                    $order['coupon'] = $orde?->coupon;
                } else {
                    $order['coupon'] = $orde?->coupon;
                }

                // Product

                $product = Checkout::where('order_code', $orde?->order_code)->get();

                $product_out = [];

                if ($product->count() > 0) {
                    foreach ($product as $prod) {
                        $product_in['product_id'] = $prod->product->id;
                        $product_in['name'] = $prod->product->name;
                        $product_in['quantity'] = $prod->quantity;
                        $product_in['price'] = $prod->product->price;

                        $product_out[] = $product_in;
                    }
                }


                $order['product'] = $product_out;
                $data[] = $order;
            }

            $total_data_orderan = Order::count();
            return response()->json(['data' => $data, 'total' => $total_data_orderan]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e], 500);
        }
    }

    public function searchOrderPaginate(Request $request, $page)
    {
        $hal = $page ?? 1;
        $page = $request->query('page', $hal);
        $size = 20;
        $user = $request->user();
        // dd($request->status);
        if ($request->startOfDate && $request->endOfDate) {
            $startOfDate = Carbon::parse($request->startOfDate);
            $endOfDate = Carbon::parse($request->endOfDate)->addDays(1);
            if ($user->role->name == 'Customer Service') {
                $pesanan = Order::select(
                    'orders.id',
                    'orders.order_code',
                    'orders.user_id',
                    'orders.customer_id',
                    'orders.address_id',
                    'orders.coupon_id',
                    'orders.sub_total',
                    'orders.ongkos_kirim',
                    'orders.potongan_ongkir',
                    'orders.final_ongkir',
                    'orders.total_price',
                    'orders.payment_id',
                    'orders.sending_id',
                    'orders.description',
                    'orders.nomor_resi',
                    'orders.tanggal_resi',
                    'orders.nama_rekening',
                    'orders.special',
                    'orders.sumber_lead',
                    'orders.jenis_lead',
                    'orders.kode',
                    'orders.note',
                    'orders.status',
                    'orders.created_at',
                    'orders.updated_at',
                    'orders.expedition',
                )
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('orders.user_id', $user->id)
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->whereBetween('orders.created_at', [$startOfDate, $endOfDate])
                    ->with(['customer', 'payment', 'sending', 'address', 'product'])
                    ->orderBy('orders.id', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                $total_data_orderan = Order::join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('orders.user_id', $user->id)
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->whereBetween('orders.created_at', [$startOfDate, $endOfDate])
                    ->count();

            } else {

                $pesanan = Order::select(
                    'orders.id',
                    'orders.order_code',
                    'orders.user_id',
                    'orders.customer_id',
                    'orders.address_id',
                    'orders.coupon_id',
                    'orders.sub_total',
                    'orders.ongkos_kirim',
                    'orders.potongan_ongkir',
                    'orders.final_ongkir',
                    'orders.total_price',
                    'orders.payment_id',
                    'orders.sending_id',
                    'orders.description',
                    'orders.nomor_resi',
                    'orders.tanggal_resi',
                    'orders.nama_rekening',
                    'orders.special',
                    'orders.sumber_lead',
                    'orders.jenis_lead',
                    'orders.kode',
                    'orders.note',
                    'orders.status',
                    'orders.created_at',
                    'orders.updated_at',
                    'orders.expedition',
                )
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->whereBetween('orders.created_at', [$startOfDate, $endOfDate])
                    ->with(['customer', 'payment', 'sending', 'address', 'product'])
                    ->orderBy('orders.id', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                $total_data_orderan = Order::join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->whereBetween('orders.created_at', [$startOfDate, $endOfDate])
                    ->count();
            }
        } else {
            if ($user->role->name == 'Customer Service') {
                $pesanan = Order::select(
                    'orders.id',
                    'orders.order_code',
                    'orders.user_id',
                    'orders.customer_id',
                    'orders.address_id',
                    'orders.coupon_id',
                    'orders.sub_total',
                    'orders.ongkos_kirim',
                    'orders.potongan_ongkir',
                    'orders.final_ongkir',
                    'orders.total_price',
                    'orders.payment_id',
                    'orders.sending_id',
                    'orders.description',
                    'orders.nomor_resi',
                    'orders.tanggal_resi',
                    'orders.nama_rekening',
                    'orders.special',
                    'orders.sumber_lead',
                    'orders.jenis_lead',
                    'orders.kode',
                    'orders.note',
                    'orders.status',
                    'orders.created_at',
                    'orders.updated_at',
                    'orders.expedition',
                )
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('orders.user_id', $user->id)
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->with(['customer', 'payment', 'sending', 'address', 'product'])
                    ->orderBy('orders.id', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                $total_data_orderan = Order::join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('orders.user_id', $user->id)
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")->count();

            } else {

                $pesanan = Order::select(
                    'orders.id',
                    'orders.order_code',
                    'orders.user_id',
                    'orders.customer_id',
                    'orders.address_id',
                    'orders.coupon_id',
                    'orders.sub_total',
                    'orders.ongkos_kirim',
                    'orders.potongan_ongkir',
                    'orders.final_ongkir',
                    'orders.total_price',
                    'orders.payment_id',
                    'orders.sending_id',
                    'orders.description',
                    'orders.nomor_resi',
                    'orders.tanggal_resi',
                    'orders.nama_rekening',
                    'orders.special',
                    'orders.sumber_lead',
                    'orders.jenis_lead',
                    'orders.kode',
                    'orders.note',
                    'orders.status',
                    'orders.created_at',
                    'orders.updated_at',
                    'orders.expedition',
                )
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")
                    ->with(['customer', 'payment', 'sending', 'address', 'product'])
                    ->orderBy('orders.id', 'desc')
                    ->paginate($size, ['*'], 'page', $page);

                $total_data_orderan = Order::join('customers', 'orders.customer_id', 'customers.id')
                    ->join('users', 'orders.user_id', 'users.id')
                    ->where('users.username', 'LIKE', "%$request->created_by%")
                    ->where('customers.surename', 'LIKE', "%$request->customer%")
                    ->where('orders.order_code', 'LIKE', "%$request->order_code%")
                    ->where('orders.status', 'LIKE', "%$request->status%")->count();
            }
        }
        $data = [];
        // dd($pesanan);
        foreach ($pesanan as $orde) {
            $order['id'] = $orde?->id;
            $order['order_code'] = $orde?->order_code;
            $order['sub_total'] = $orde?->sub_total;
            $order['ongkos_kirim'] = $orde?->ongkos_kirim;
            $order['potongan_ongkir'] = $orde?->potongan_ongkir;
            $order['final_ongkir'] = $orde?->final_ongkir;
            $order['total_price'] = $orde?->total_price;
            $order['description'] = $orde?->description;
            $order['nomor_resi'] = $orde?->nomor_resi;
            $order['tanggal_resi'] = $orde?->tanggal_resi;
            $order['note'] = $orde?->note;
            $order['special'] = $orde?->special;
            $order['status'] = $orde?->status;
            $order['created_at'] = date("Y-m-d", strtotime($orde?->created_at));
            $order['modified_at'] = date("Y-m-d", strtotime($orde?->updated_at));
            $order['createdby'] = User::where('id', History::where('order_id', $orde?->id)->first()?->user_id)->first()?->username;
            // $order['modifiedby'] = User::where('id', History::where('order_id', $orde?->id)->last()->user_id)->first()->username;
            $order['modifiedby'] = User::where('id', function ($query) use ($orde) {
                $query->select('user_id')
                    ->from('histories')
                    ->where('order_id', $orde?->id)
                    ->latest()
                    ->limit(1);
            })->value('username');

            $order['customer'] = [
                'surename' => $orde?->customer->surename,
                'first_name' => $orde?->customer->first_name,
                'last_name' => $orde?->customer->last_name,
                'email' => $orde?->customer->email,
                'phone' => $orde?->customer->phone,
                'second_phone' => $orde?->customer->second_phone,
            ];
            $order['payment'] = $orde?->payment->method;
            $order['sending'] = $orde?->sending->sender;
            $order['address'] = [
                'full_address' => $orde?->address->full_address,
                'province' => $orde?->address->province,
                'city' => $orde?->address->city,
                'district' => $orde?->address->district,
                'postal_code' => $orde?->address->postal_code,
            ];

            if ($orde?->coupon == null) {
                $order['coupon'] = $orde?->coupon;
            } else {
                $order['coupon'] = $orde?->coupon;
            }

            // Product

            $product = Checkout::where('order_code', $orde?->order_code)->get();


            $product_out = [];
            foreach ($product as $prod) {
                $product_in['product_id'] = $prod->product->id;
                $product_in['name'] = $prod->product->name;
                $product_in['quantity'] = $prod->quantity;
                $product_in['price'] = $prod->product->price;

                $product_out[] = $product_in;
            }

            $order['product'] = $product_out;
            $data[] = $order;
        }

        // $total_data_orders = Order::count();

        return response()->json(['data' => $data, 'total' => $total_data_orderan, 'total_order' => $total_data_orderan]);
    }

    public function oneOrder(Request $request, $id)
    {

        $orde = Order::where('id', $id)->first();


        $data = [];

        // foreach($pesanan as $orde){
        $order['order_code'] = $orde?->order_code;
        $order['sub_total'] = $orde?->sub_total;
        $order['ongkos_kirim'] = $orde?->ongkos_kirim;
        $order['potongan_ongkir'] = $orde?->potongan_ongkir;
        $order['final_ongkir'] = $orde?->final_ongkir;
        $order['total_price'] = $orde?->total_price;
        $order['ekspedisi'] = $orde?->expedition;
        $order['nomor_resi'] = $orde?->nomor_resi;
        $order['tanggal_resi'] = $orde?->tanggal_resi;

        if ($orde?->special == 'false') {
            $order['special'] = false;
        } else {
            $order['special'] = true;
        }

        if ($orde?->description == null) {
            $order['description'] = null;
        } else {
            $order['description'] = $orde?->description;
        }

        if ($orde?->note == null) {
            $order['note'] = null;
        } else {
            $order['note'] = $orde?->note;
        }

        $order['status'] = $orde?->status;
        $order['customer'] = [
            'surename' => $orde?->customer->surename,
            'first_name' => $orde?->customer->first_name,
            'last_name' => $orde?->customer->last_name,
            'email' => $orde?->customer->email,
            'phone' => $orde?->customer->phone,
            'second_phone' => $orde?->customer->second_phone,
        ];
        $order['payment'] = $orde?->payment->method;
        $order['payment_id'] = $orde?->payment_id;
        $order['sending'] = $orde?->sending->sender;
        $order['sending_id'] = $orde?->sending_id;
        $order['sumber_lead'] = $orde?->sumber_lead;
        $order['jenis_lead'] = $orde?->jenis_lead;
        $order['address'] = [
            'full_address' => $orde?->address->full_address,
            'province' => $orde?->address->province,
            'city' => $orde?->address->city,
            'district' => $orde?->address->district,
            'province_id' => $orde?->address->province_id,
            'city_id' => $orde?->address->city_id,
            'district_id' => $orde?->address->district_id,
            'postal_code' => $orde?->address->postal_code,
        ];

        // Product

        $product = Checkout::where('order_code', $orde?->order_code)->with('product')->get();

        $product_out = [];

        $total_weight = 0;
        foreach ($product as $prod) {
            $product_in['product_id'] = $prod->product->id;
            $product_in['name'] = $prod->product->name;
            $product_in['quantity'] = $prod->quantity;
            $product_in['price'] = $prod->product->price;
            $product_in['image'] = $prod->product->image;
            $product_in['item_id'] = "item[" . $prod->product->id . "][product_id]";
            $product_in['item_quantity'] = "item[" . $prod->product->id . "][quantity]";

            $product_out[] = $product_in;

            $total_weight += ($prod->product->weight * $prod->quantity);
        }
        $order['total_weight'] = $total_weight;
        $order['product'] = $product_out;
        $data[] = $order;
        // }

        return response()->json(['data' => $order]);
    }


    public function createOrder(Request $request)
    {

        // Customer Service ID
        $user = $request->user();
        $data['user_id'] = $user->id;

        $data['kode'] = $user->id;

        // Create or Detect Customer
        if (substr($request->phone, 0, 2) === "08") {
            // Menghapus angka 0 pertama dan menggantinya dengan "62"
            $phone = "62" . substr($request->phone, 1);
        } else {
            $phone = $request->phone;
        }

        if ($request->last_name) {
            $last_name = $request->last_name;
        } else {
            $last_name = $request->district;
        }
        if ($request->email) {
            $customer = Customer::where('email', $request->email)->
                where('first_name', $request->first_name)->
                where('last_name', $last_name)->
                where('phone', $phone)->first();
        } else {
            $customer = Customer::where('first_name', $request->first_name)->
                where('last_name', $last_name)->
                where('phone', $phone)->first();
        }

        if (!$customer) {
            $customerController = new CustomerController();
            $customer = $customerController->createCustomer($request);
        }

        $data['customer_id'] = $customer->id;


        // Create or Detect Address
        $address = Address::where('customer_id', $customer->id)->
            where('full_address', $request->full_address)->
            where('province', $request->province)->
            where('city', $request->city)->
            where('district', $request->district)->first();

        if (!$address) {
            $alamat['full_address'] = $request->full_address;
            $alamat['province'] = $request->province;
            $alamat['city'] = $request->city;
            $alamat['district'] = $request->district;
            $alamat['province_id'] = $request->province_id;
            $alamat['city_id'] = $request->city_id;
            $alamat['district_id'] = $request->district_id;
            if ($request->postal_code) {
                $alamat['postal_code'] = $request->postal_code;
            }
            $alamat['customer_id'] = $customer->id;

            $address = Address::create($alamat);
        }

        $data['address_id'] = $address->id;


        // generate order_code
        // $latest = Order::orderBy('id', 'desc')->get();
        // $latest = Order::latest()->first();

        // if($latest != null){
        // $angkaSetelahDipotong = substr($latest->order_code, 8);
        // $angkaSetelahDipotong = ltrim($angkaSetelahDipotong, '0');
        // $hasilInteger = intval($angkaSetelahDipotong);
        // $number = $hasilInteger+1;
        $cek_id = Order::where('id', Order::max('id'))->get();
        $cek_id_out = $cek_id[0]['id'];
        // return response()->json(['data' => $cek_id_out], 500);

        $number = $cek_id_out + 1;
        $data['order_code'] = date("dmY$number");
        $barang['order_code'] = date("dmY$number");

        $cek = Order::where('order_code', $data['order_code'])->first();
        if ($cek != null) {
            $data['order_code'] = date("dmY$number") . "1";
            $barang['order_code'] = date("dmY$number") . "1";

            $cek = Order::where('order_code', $data['order_code'])->first();
            if ($cek != null) {
                $data['order_code'] = date("dmY$number") . "2";
                $barang['order_code'] = date("dmY$number") . "2";

                $cek = Order::where('order_code', $data['order_code'])->first();
                if ($cek != null) {
                    $data['order_code'] = date("dmY$number") . "3";
                    $barang['order_code'] = date("dmY$number") . "3";

                    $cek = Order::where('order_code', $data['order_code'])->first();
                    if ($cek != null) {
                        $data['order_code'] = date("dmY$number") . "4";
                        $barang['order_code'] = date("dmY$number") . "4";

                        $cek = Order::where('order_code', $data['order_code'])->first();
                        if ($cek != null) {
                            $data['order_code'] = date("dmY$number") . "5";
                            $barang['order_code'] = date("dmY$number") . "5";

                        }
                    }
                }
            }
        }
        // }
        // else {
        //     $data['order_code'] = date("dmY1");
        // }



        // payment and sender
        $data['payment_id'] = $request->payment_id;
        $data['nama_rekening'] = $request->nama_rekening;
        $data['status'] = $request->status;
        $data['upgrade_price'] = 0;
        $data['expedition'] = $request->ekspedisi;
        $data['created_at'] = $request->date;

        // Lead Data

        $data['jenis_lead'] = $request->jenis_lead;
        $data['sumber_lead'] = $request->sumber_lead;

        if ($request->sumber_lead == "Shopee" || $request->sumber_lead == "Tokopedia" || $request->sumber_lead == "TikTok Shop") {
            $data['special'] = "true";
        } else {
            $data['special'] = "false";
        }

        $sending = Sending::where('sender', $request->sending)->first();

        if ($sending == null) {
            $data_sending['sender'] = $request->sending;
            $sending_id = Sending::create($data_sending);
            $data['sending_id'] = $sending_id->id;
        } else {
            $data['sending_id'] = $sending->id;
        }

        if ($request->description) {
            $data['description'] = $request->description;
        }

        if ($request->note) {
            $data['note'] = $request->note;
        }
        // Insert Data Product Order

        // $barang['order_code'] = ;
        $items = $request->item;

        $sub_total = 0;
        // if($items->count() <= 0){
        //     return response()->json(['message' => 'data product kosong'], 500);
        // }

        foreach ($items as $item) {

            $product = Product::Where('id', $item['product_id'])->first();

            if ($product == null) {
                continue;
            } else {
                $barang['product_id'] = $item['product_id'];
                $barang['quantity'] = $item['quantity'];
                $barang['price'] = $product->price * $item['quantity'];

                Checkout::create($barang);
                $sub_total += $product->price * $item['quantity'];
            }
        }


        $ongkir_awal = $request->ongkir;

        // Coupon
        if ($request->coupon) {

            $coupon = Coupon::where('code', $request->coupon)->first();
            $cek_coupon_uses = Order::where('coupon_id', $coupon->id)->count();
            $cek_customer_uses = Order::where('customer_id', $customer->id)->where('coupon_id', $coupon->id)->count();
            $date = Carbon::now();

            if ($coupon && $coupon->coupon_uses > $cek_coupon_uses && $coupon->customer_uses > $cek_customer_uses && $coupon->date_start <= $date && $coupon->date_end >= $date) {
                $data['coupon_id'] = $coupon->id;
                if ($coupon->category == 'Price') {
                    if ($coupon->type == 'Percentage') {
                        $sub_total_out = $sub_total - ($sub_total * $coupon->discount / 100);

                        $ongkir_after_disc = $ongkir_awal;

                    } elseif ($coupon->type == 'Fixed Amount') {
                        $sub_total_out = $sub_total - $coupon->discount;

                        $ongkir_after_disc = $ongkir_awal;

                    }

                } elseif ($coupon->category == 'Ongkos Kirim') {
                    if ($coupon->type == 'Percentage') {
                        $ongkir_after_disc = $ongkir_awal - ($ongkir_awal * $coupon->discount / 100);

                        $sub_total_out = $sub_total;

                    } elseif ($coupon->type == 'Fixed Amount') {
                        $ongkir_after_disc = $ongkir_awal - $coupon->discount;

                        $sub_total_out = $sub_total;

                    }
                }
            } else {
                $sub_total_out = $sub_total;
                $ongkir_after_disc = $ongkir_awal;
            }

        } else {
            $sub_total_out = $sub_total;
            $ongkir_after_disc = $ongkir_awal;
        }

        $data['sub_total'] = $sub_total_out;

        if ($request->potongan_ongkir) {
            $data['potongan_ongkir'] = $request->potongan_ongkir;
            $ongkir_after_cut = $ongkir_after_disc - $request->potongan_ongkir;
            if ($ongkir_after_cut <= 0) {
                $ongkir_after_cut = 0;
            }
            $total_price = $sub_total_out + $ongkir_after_cut;
        } else {
            $ongkir_after_cut = $ongkir_after_disc;
            $total_price = $sub_total_out + $ongkir_after_cut;
        }

        $data['ongkos_kirim'] = $request->ongkir;
        $data['final_ongkir'] = $ongkir_after_cut;

        $data['total_price'] = $total_price;


        // Store Data Order
        $order = Order::create($data);

        /// Buat Notifikasi
        $data_history['status'] = $order->status;
        $data_history['order_id'] = $order->id;
        $data_history['user_id'] = $request->user()->id;


        History::create($data_history);

        return response()->json(['message' => 'berhasil menambahkan pesanan', 'data' => $order], 200);
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();

        $customer = Customer::where('id', $order->customer_id)->first();
        $data_customer['surename'] = $request->surename;
        $data_customer['first_name'] = $request->first_name;
        $data_customer['last_name'] = $request->last_name;
        $data_customer['phone'] = $request->phone;
        $data_customer['email'] = $request->email;
        $data_customer['second_phone'] = $request->second_phone;
        $customer->update($data_customer);

        $address = Address::where('id', $order->address_id)->first();
        $data_address['province'] = $request->province;
        $data_address['city'] = $request->city;
        $data_address['district'] = $request->district;
        $data_address['province_id'] = $request->province_id;
        $data_address['city_id'] = $request->city_id;
        $data_address['district_id'] = $request->district_id;
        if ($request->postal_code) {
            $data_address['postal_code'] = $request->postal_code;
        }
        $data_address['full_address'] = $request->full_address;
        $address->update($data_address);

        $checkout = Checkout::where('order_code', $order->order_code)->delete();

        $items = $request->item;
        $barang['order_code'] = $order->order_code;
        $sub_total = 0;
        foreach ($items as $item) {

            $product = Product::Where('id', $item['product_id'])->first();

            if ($product == null) {
                continue;
            } else {
                $barang['product_id'] = $item['product_id'];
                $barang['quantity'] = $item['quantity'];
                $barang['price'] = $product->price * $item['quantity'];

                Checkout::create($barang);
                $sub_total += $product->price * $item['quantity'];
            }
        }


        $ongkir_awal = $request->ongkir;

        // Coupon
        if ($request->coupon) {

            $coupon = Coupon::where('code', $request->coupon)->first();
            $cek_coupon_uses = Order::where('coupon_id', $coupon->id)->count();
            $cek_customer_uses = Order::where('customer_id', $customer->id)->where('coupon_id', $coupon->id)->count();
            $date = Carbon::now();

            if ($coupon && $coupon->coupon_uses > $cek_coupon_uses && $coupon->customer_uses > $cek_customer_uses && $coupon->date_start <= $date && $coupon->date_end >= $date) {
                $data['coupon_id'] = $coupon->id;
                if ($coupon->category == 'Price') {
                    if ($coupon->type == 'Percentage') {
                        $sub_total_out = $sub_total - ($sub_total * $coupon->discount / 100);

                        $ongkir_after_disc = $ongkir_awal;

                    } elseif ($coupon->type == 'Fixed Amount') {
                        $sub_total_out = $sub_total - $coupon->discount;

                        $ongkir_after_disc = $ongkir_awal;

                    }

                } elseif ($coupon->category == 'Ongkos Kirim') {
                    if ($coupon->type == 'Percentage') {
                        $ongkir_after_disc = $ongkir_awal - ($ongkir_awal * $coupon->discount / 100);

                        $sub_total_out = $sub_total;

                    } elseif ($coupon->type == 'Fixed Amount') {
                        $ongkir_after_disc = $ongkir_awal - $coupon->discount;

                        $sub_total_out = $sub_total;

                    }
                }
            } else {
                $sub_total_out = $sub_total;
                $ongkir_after_disc = $ongkir_awal;
            }

        } else {
            $sub_total_out = $sub_total;
            $ongkir_after_disc = $ongkir_awal;
        }

        $data['sub_total'] = $sub_total_out;
        $data['payment_id'] = $request->payment_id;
        $data['status'] = $request->status;
        $data['expedition'] = $request->ekspedisi;

        // Lead Data
        if ($request->jenis_lead) {
            $data['jenis_lead'] = $request->jenis_lead;
        }
        if ($request->sumber_lead) {
            $data['sumber_lead'] = $request->sumber_lead;
        }

        $sending = Sending::where('sender', $request->sending)->first();

        if ($sending == null) {
            $data_sending['sender'] = $request->sending;
            $sending_id = Sending::create($data_sending);
            $data['sending_id'] = $sending_id->id;
        } else {
            $data['sending_id'] = $sending->id;
        }

        if ($request->description) {
            $data['description'] = $request->description;
        }

        if ($request->note) {
            $data['note'] = $request->note;
        }

        if ($request->potongan_ongkir) {
            $data['potongan_ongkir'] = $request->potongan_ongkir;
            $ongkir_after_cut = $ongkir_after_disc - $request->potongan_ongkir;
            if ($ongkir_after_cut <= 0) {
                $ongkir_after_cut = 0;
            }
            $total_price = $sub_total_out + $ongkir_after_cut;
        } else {
            $ongkir_after_cut = $ongkir_after_disc;
            $total_price = $sub_total_out + $ongkir_after_cut;
        }

        $data['ongkos_kirim'] = $request->ongkir;
        $data['final_ongkir'] = $ongkir_after_cut;

        $data['total_price'] = $total_price;
        $data['upgrade_price'] = $total_price - $order->total_price;

        if ($request->special == "false" && $order->special == "true") {
            $data['special'] = "false";
            $data['created_at'] = Carbon::now();
        } elseif ($request->special == "false") {
            $data['special'] = "false";
        } elseif ($request->special == "true") {
            $data['special'] = "true";
        }

        if ($request->nomor_resi) {
            $data['nomor_resi'] = $request->nomor_resi;
            $data['tanggal_resi'] = Carbon::now();
        }

        $order->update($data);


        /// Buat Notifikasi
        // if(History::where('order_id',$order->id)->where('status', $order->status)->first() == null){
        $data_history['status'] = $order->status;
        $data_history['order_id'] = $order->id;
        $data_history['user_id'] = $request->user()->id;

        History::create($data_history);
        // }


        return response()->json(['message' => 'berhasil memperbaharui']);

    }

    public function deleteOrder(Request $request, $id)
    {
        $order = Order::where('id', $id)->first();

        $return = Return_order::where('order_code', $order->order_code)->first();
        if ($return != null) {
            $return->delete();
        }

        $checkout = Checkout::where('order_code', $order->order_code)->delete();
        $checkout = OrderPhoto::where('order_id', $order->id)->delete();


        $order->delete();

        return response()->json(['message' => 'berhasil dihapus'], 200);
    }


    public function searchOrder(Request $request)
    {

        $get_data = Order::where('order_code', $request->order_code)->with('customer')->first();

        if ($get_data == null) {
            return response()->json(['message' => 'tidak ada data'], 401);
        } elseif ($get_data->customer->phone == $request->phone) {
            $pesanan = Order::where('order_code', $request->order_code)->get();
        } elseif ($get_data->customer->second_phone == $request->phone) {
            $pesanan = Order::where('order_code', $request->order_code)->get();
        } else {
            return response()->json(['message' => 'tidak ada data'], 401);
        }

        $data = [];

        foreach ($pesanan as $orde) {
            $order['order_code'] = $orde->order_code;
            $order['sub_total'] = $orde->sub_total;
            $order['potongan_ongkir'] = $orde->potongan_ongkir;
            $order['total_price'] = $orde->total_price;
            $order['description'] = $orde->description;
            $order['status'] = $orde->status;
            $order['date_order'] = $orde->created_at->format('d/m/Y');
            $order['customer'] = [
                'surename' => $orde->customer->surename,
                'first_name' => $orde->customer->first_name,
                'last_name' => $orde->customer->last_name,
                'email' => $orde->customer->email,
                'phone' => $orde->customer->phone,
                'second_phone' => $orde->customer->second_phone,
            ];
            $order['payment'] = $orde->payment->method;
            $order['sending'] = $orde->sending->sender;
            $order['address'] = [
                'full_address' => $orde->address->full_address,
                'province' => $orde->address->province,
                'city' => $orde->address->city,
                'district' => $orde->address->district,
                'postal_code' => $orde->address->postal_code,
            ];

            // Product

            $product = Checkout::where('order_code', $orde->order_code)->get();

            $product_out = [];
            foreach ($product as $prod) {
                $product_in['product_id'] = $prod->product->id;
                $product_in['name'] = $prod->product->name;
                $product_in['quantity'] = $prod->quantity;
                $product_in['price'] = $prod->price;
                $product_in['item_id'] = "item[" . $prod->product->id . "][product_id]";
                $product_in['item_quantity'] = "item[" . $prod->product->id . "][quantity]";

                $product_out[] = $product_in;
            }

            $order['product'] = $product_out;
            $data[] = $order;
        }

        return response()->json(['data' => $data]);
    }

    public function updateStatusOrder(Request $request)
    {
        $data['status'] = $request->status;

        $orders = $request->orders;

        foreach ($orders as $order_code) {
            $order_change_status = Order::where('order_code', $order_code)->first();
            $order_change_status->update($data);

            /// Buat Notifikasi
            // if(History::where('order_id',$order_change_status->id)->where('status', $order_change_status->status)->first() == null){
            $data_history['status'] = $order_change_status->status;
            $data_history['order_id'] = $order_change_status->id;
            $data_history['user_id'] = $request->user()->id;

            History::create($data_history);
            // }


        }

        return response()->json(['message' => 'success'], 200);
    }

    public function packingStatusOrder(Request $request)
    {
        $order_code = $request->order_code;
        $data['status'] = 'Kirim';
        $data['nomor_resi'] = $request->nomor_resi;
        $data['tanggal_resi'] = Carbon::now();

        $order = Order::where('order_code', $order_code)->update($data);

        $order = Order::where('order_code', $order_code)->with('user')->with('customer')->with('address')->first();

        $checkouts = Checkout::where('order_code', $order->order_code)->get();

        $products = [];

        foreach ($checkouts as $checkout) {
            $product['name'] = $checkout->product->name;
            $product['quantity'] = $checkout->quantity;

            $products[] = $product;
        }

        $order['product'] = $products;

        /// Buat Notifikasi
        $data_history['status'] = 'Kirim';
        $data_history['order_id'] = $order->id;
        $data_history['user_id'] = $request->user()->id;

        History::create($data_history);




        return response()->json(['data' => $order], 200);
    }



    public function printShippingOrder(Request $request)
    {
        $order_code = $request->order_code;

        $out = [];
        foreach ($order_code as $order_code) {
            $order = Order::where('order_code', $order_code)->with('user')->with('customer')->with('address')->first();

            $checkouts = Checkout::where('order_code', $order->order_code)->get();

            $products = [];

            foreach ($checkouts as $checkout) {
                $product['name'] = $checkout->product->name;
                $product['quantity'] = $checkout->quantity;

                $products[] = $product;
            }

            $order['product'] = $products;

            $out[] = $order;
        }

        return response()->json(['data' => $out], 200);
    }


    public function updateNomorResi(Request $request, $id)
    {
        $data['nomor_resi'] = $request->nomor_resi;
        $data['tanggal_resi'] = Carbon::now();
        $order = Order::where('id', $id)->first();
        $order->update($data);
        return response()->json(['message' => 'Success'], 200);
    }
}
