<?php

namespace App\Exports;

use App\Customs\ExcelCellTracker;
use App\Helpers\ExcelHelper;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Checkout;
use App\Models\DataLead;
use App\Models\ProductCategory;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummaryExport implements FromCollection, ShouldAutoSize, WithEvents, WithStrictNullComparison
{

    public static array $cellTrackers;
    protected $user;
    protected $startOfDate;
    protected $endOfDate;

    public function __construct($user, $startOfDate, $endOfDate)
    {
        $this->user = $user;
        $this->startOfDate = $startOfDate;
        $this->endOfDate = $endOfDate;
    }

    public function collection()
    {

        $user = $this->user;
        $role = Role::where('id', $user->role_id)->value('name');

        $startOfDate = Carbon::parse($this->startOfDate);
        $endOfDate = Carbon::parse($this->endOfDate)->addDays(1);

        $tanggal_awal[] = 'Tanggal Awal'; // ['Tanggal Awal', 'nilai', 'nilai'] 
        $tanggal_awal[] = $this->startOfDate;
        $tanggal_akhir[] = 'Tanggal Akhir';
        $tanggal_akhir[] = $this->endOfDate;

        $isCustomerService = $role == 'Customer Service';

        $total_lead = DataLead::when($isCustomerService, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereBetween('created_at', [$startOfDate, $endOfDate])->sum('jumlah_lead');

        $lead[] = 'Total Leads';
        $lead[] = $total_lead;

        $order_query = Order::when($isCustomerService, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->whereBetween('created_at', [$startOfDate, $endOfDate])->where('special', 'false');

        $total_closing = $order_query->count();
        $closing[] = 'Total Closing';
        $closing[] = $total_closing;

        if ($total_closing == 0 || $total_lead == 0) {
            $closing_rate = 0;
        } else {
            $closing_rate = (100 / $total_lead) * $total_closing;
        }

        $cr[] = 'Closing Rate';
        $cr[] = round($closing_rate) . "%";

        $sales = $order_query->sum('sub_total');
        $omset[] = 'Total Omset';
        $omset[] = $sales;

        $avg[] = 'Avg Order Value';
        if ($total_closing == 0 || $sales == 0) {
            $avg[] = 0;
        } else {
            $avg[] = round($sales / $total_closing);
        }

        $total_ongkir = $order_query->sum('ongkos_kirim');
        $ongkir[] = 'Total Ongkir';
        $ongkir[] = $total_ongkir;

        $potongan_ongkir = $order_query->sum('potongan_ongkir');
        $pot_ongkir[] = 'Pot. Ongkir';
        $pot_ongkir[] = $potongan_ongkir;


        $checkouts = Checkout::when($isCustomerService, function ($query) use ($user) {
            $query->where('orders.user_id', '=', $user->id);
        })
            ->select('checkouts.order_code as order_code', 'checkouts.product_id as product_id', 'checkouts.quantity as quantity', 'checkouts.price as price', 'checkouts.created_at as created_at')
            ->join('orders', 'checkouts.order_code', '=', 'orders.order_code')
            ->whereBetween('orders.created_at', [$startOfDate, $endOfDate])
            ->where('orders.special', '=', 'false')
            ->get()
            ->groupBy('product_id');

        $total_product = 0;
        $total_frame = 0;
        $total_price = 0;
        $frames = [];
        $products = [];

        foreach ($checkouts as $checkout) {
            $product_qty = $checkout->sum('quantity');
            $product_price = $checkout->sum('price');
            $product_name = Product::where('id', $checkout[0]->product_id)->first()->name;

            $total_price += $product_price;

            $categories = ProductCategory::where('product_id', $checkout[0]->product_id)->with('category')->get();
            foreach ($categories as $category) {
                $cek = $category->category->name;

                if (isset($frames["$cek"])) {
                    $frames["$cek"]['qty'] += $category->quantity * $product_qty;
                } else {
                    $frm['name'] = $category->category->name;
                    $frm['qty'] = $category->quantity * $product_qty;

                    $frames[$cek] = $frm;
                }

                $total_frame += $category->quantity * $product_qty;
            }

            $total_product += $product_qty;

            $product['name'] = $product_name;
            $product['qty'] = $product_qty;
            $product['price'] = $product_price;
            $products[] = $product;
        }

        $space[] = '';

        // Colder than snow
        $arrayIndexStartOffset = -1;



        // First Table
        $startCellColumn = 1;
        $startCellRow = 1;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['tanggal_awal'] = $tanggal_awal;
        $out_data['tanggal_akhir'] = $tanggal_akhir;
        $out_data['space1'] = $space;

        $cellTracker->addRow(count($out_data) - 1 - $startCellRow);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Second Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2; // ini plus dua karna yang plus pertama adalah spasi dan plus kedua adalah posisi baris sebelumnya, semoga dapat dipahami wkwk 
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['lead'] = $lead;
        $out_data['closing'] = $closing;
        $out_data['cr'] = $cr;
        $out_data['omset'] = $omset;
        $out_data['avg'] = $avg;
        $out_data['space2'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Third Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['ongkir'] = $ongkir;
        $out_data['potongan'] = $pot_ongkir;
        $out_data['space3'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;





        // Fourth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['total_frame'] = ['Total Bingkai', $total_frame];
        $out_data['space4'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;



        // Fifth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['bingkai'] = ['Bingkai', 'Qty'];
        foreach ($frames as $frame_out) {
            $frame_name = $frame_out['name'];
            $frame_qty = $frame_out['qty'];
            $out_data["$frame_name"] = [$frame_name, $frame_qty];
        }
        $out_data['space5'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;





        // Sixth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['total_paket'] = ['Total Paket', $total_product, $total_price];
        $out_data['space6'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;





        // Seventh Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['paket'] = ['Paket', 'Qty', 'Omset'];
        foreach ($products as $product) {
            $prodct_name = $product['name'];
            $prdct_qty = $product['qty'];
            $price = $product['price'];
            $out_data["product$prodct_name"] = [$prodct_name, $prdct_qty, $price];
        }
        $out_data['space9'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;





        // Eighth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['leads2'] = ['Sumber Leads', 'Leads', 'Closing', '%CR', 'Omset'];
        $out_data['space10'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;





        // Nineth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $data_lead = DataLead::when($isCustomerService, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->select(
                'sumber_lead',
                \DB::raw('SUM(jumlah_lead) as jumlah_lead')
            )
            ->whereBetween('created_at', [$startOfDate, $endOfDate])
            ->groupBy('sumber_lead')
            ->get()
            ->groupBy('sumber_lead');

        $nombor = 1;
        foreach ($data_lead as $key => $lead) {
            $order = Order::whereBetween('created_at', [$startOfDate, $endOfDate])->where('sumber_lead', $key)->where('special', '=', "false")->get();

            $closing = $order->count();

            if ($lead[0]->jumlah_lead == 0) {
                $cr = 0;
            } else {
                $cr = (100 / $lead[0]->jumlah_lead) * $order->count();
            }

            $omset = $order->sum('sub_total');

            $out_data[$nombor . " - $lead[0]->sumber_lead"] = [
                $lead[0]->sumber_lead,
                $lead[0]->jumlah_lead,
                $closing,
                round($cr) . '%',
                $omset,
            ];
            $nombor++;
        }

        $out_data['space11'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Tenth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['leads3'] = ['CS', 'Leads', 'Closing', '%CR', 'Lead Baru', 'Follow Up', 'Ongkir', 'Potongan', 'Omset'];
        $out_data['space12'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Eleventh Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $data_lead = DataLead::when($isCustomerService, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->select(
                'user_id',
                \DB::raw('SUM(jumlah_lead) as jumlah_lead')
            )
            ->whereBetween('created_at', [$startOfDate, $endOfDate])
            ->with('user')
            ->groupBy('user_id')
            ->get()
            ->groupBy('user_id');

        $nombor = 1;
        foreach ($data_lead as $lead) {

            $order = Order::whereBetween('created_at', [$startOfDate, $endOfDate])->where('user_id', $lead[0]->user_id)->where('special', '=', "false")->get();

            // $order = Order::whereDate('created_at', $lead->created_at->toDateString())->where('user_id', $lead->user_id)->where('sumber_lead', $lead->sumber_lead)->where('special', '=', "false")->get();

            $closing = $order->count();

            if ($lead[0]->jumlah_lead == 0) {
                $cr = 0;
            } else {
                $cr = (100 / $lead[0]->jumlah_lead) * $order->count();
            }
            $baru = $order->where('jenis_lead', 'Lead Baru')->count();
            $lama = $order->where('jenis_lead', 'Lead Follow Up')->count();
            $ongkir = $order->sum('ongkos_kirim');
            $potongan = $order->sum('potongan_ongkir');
            $omset = $order->sum('sub_total');

            $out_data[$nombor . $lead[0]->user->username] = [
                $lead[0]->user->username,
                $lead[0]->jumlah_lead,
                $closing,
                round($cr) . '%',
                $baru,
                $lama,
                $ongkir,
                $potongan,
                $omset,

            ];
            $nombor++;
        }

        $out_data['space13'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Twelveth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $out_data['leads'] = ['Tanggal', 'CS', 'Sumber Leads', 'Leads', 'Closing', '%CR', 'Lead Baru', 'Follow Up', 'Ongkir', 'Potongan', 'Omset'];
        $out_data['space14'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;




        // Thirteenth Table
        $startCellColumn = $cellTracker->currentColumnTrackerStartCell;
        $startCellRow = $cellTracker->currentRowTrackerEndCell + 2;
        $cellTracker = new ExcelCellTracker($startCellColumn, $startCellRow);

        $data_lead = DataLead::when($isCustomerService, function ($query) use ($user) {
            $user->where('user_id', $user->id);
        })->whereBetween('created_at', [$startOfDate, $endOfDate])->with('user')->orderBy('created_at')->get();
        
        $nombor = 1;
        foreach ($data_lead as $lead) {

            $tgl = Carbon::parse($lead->created_at)->translatedFormat('d M Y');

            $order = Order::whereDate('created_at', $lead->created_at->toDateString())->where('user_id', $lead->user_id)->where('sumber_lead', $lead->sumber_lead)->where('special', '=', "false")->get();

            $closing = $order->count();

            if ($lead->jumlah_lead == 0) {
                $cr = 0;
            } else {
                $cr = (100 / $lead->jumlah_lead) * $order->count();
            }
            $baru = $order->where('jenis_lead', 'Lead Baru')->count();
            $lama = $order->where('jenis_lead', 'Lead Follow Up')->count();
            $ongkir = $order->sum('ongkos_kirim');
            $potongan = $order->sum('potongan_ongkir');
            $omset = $order->sum('sub_total');

            $out_data[$nombor . $lead->user->username . " - $tgl - $lead->sumber_lead"] = [
                $tgl,
                $lead->user->username,
                $lead->sumber_lead,
                $lead->jumlah_lead,
                $closing,
                round($cr) . '%',
                $baru,
                $lama,
                $ongkir,
                $potongan,
                $omset,

            ];
            $nombor++;
        }
        $out_data['space15'] = $space;

        $cellTracker->setEndRow(count($out_data) - 1);
        $maxColumnCount = ExcelHelper::getHighestColumnFromMultiRow(array_slice($out_data, $startCellColumn + $arrayIndexStartOffset, count($out_data)));
        $cellTracker->setEndColumn($maxColumnCount);
        self::$cellTrackers[] = $cellTracker;


        return collect($out_data);

    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $backgroundColor = 'b4c7dc';
                foreach (self::$cellTrackers as $cellTracker) {
                    $cellCoordinate = implode(':', [$cellTracker->getStartCellInExcelNotation(), $cellTracker->getEndCellInExcelNotation()]);
                    $sheet->getStyle($cellCoordinate)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($backgroundColor);
                }
                self::$cellTrackers = [];
            }
        ];
    }
}
