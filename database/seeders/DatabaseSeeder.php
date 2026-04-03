<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Job;
use App\Models\Part;
use App\Models\Payment;
use App\Models\RepairItem;
use App\Models\RepairType;
use App\Models\Voucher;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ────────────────────────────────────────────────────
        $cats = [
            ['name'=>'iPhone',  'slug'=>'iphone',  'color'=>'#0A84FF','icon'=>'🍎'],
            ['name'=>'Samsung', 'slug'=>'samsung', 'color'=>'#1428A0','icon'=>'📱'],
            ['name'=>'Huawei',  'slug'=>'huawei',  'color'=>'#CF0A2C','icon'=>'📲'],
            ['name'=>'Xiaomi',  'slug'=>'xiaomi',  'color'=>'#FF6900','icon'=>'🔶'],
            ['name'=>'OnePlus', 'slug'=>'oneplus', 'color'=>'#F5010C','icon'=>'⚡'],
            ['name'=>'Other',   'slug'=>'other',   'color'=>'#636366','icon'=>'📟'],
        ];
        foreach ($cats as $c) Category::create($c);

        $iphone  = Category::where('slug','iphone')->first();
        $samsung = Category::where('slug','samsung')->first();
        $xiaomi  = Category::where('slug','xiaomi')->first();

        // ── Repair Types ──────────────────────────────────────────────────
        $types = [
            ['name'=>'Screen Replacement','icon'=>'🖥️','color'=>'#0A84FF'],
            ['name'=>'Battery',           'icon'=>'🔋','color'=>'#FF453A'],
            ['name'=>'Charging Port',     'icon'=>'🔌','color'=>'#FF9F0A'],
            ['name'=>'Speaker',           'icon'=>'🔊','color'=>'#30D158'],
            ['name'=>'Back Glass',        'icon'=>'🪟','color'=>'#636366'],
            ['name'=>'Camera',            'icon'=>'📷','color'=>'#BF5AF2'],
            ['name'=>'Water Damage',      'icon'=>'💧','color'=>'#32ADE6'],
            ['name'=>'Other',             'icon'=>'🔩','color'=>'#8E8E93'],
        ];
        foreach ($types as $t) RepairType::create($t);

        $rtScreen   = RepairType::where('name','Screen Replacement')->first();
        $rtBattery  = RepairType::where('name','Battery')->first();
        $rtCharging = RepairType::where('name','Charging Port')->first();

        // ── Parts ─────────────────────────────────────────────────────────
        $parts = [
            ['category_id'=>$iphone->id,  'name'=>'iPhone 15 Pro Max Screen','part_type'=>'Screen',        'quality'=>'Original',   'stock'=>3,'cost_price'=>42,'sell_price'=>110],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 15 Pro Screen',    'part_type'=>'Screen',        'quality'=>'Original',   'stock'=>4,'cost_price'=>38,'sell_price'=>100],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 15 Screen',        'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>5,'cost_price'=>22,'sell_price'=>70],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 14 Pro Screen',    'part_type'=>'Screen',        'quality'=>'Original',   'stock'=>2,'cost_price'=>40,'sell_price'=>105],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 14 Screen',        'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>6,'cost_price'=>20,'sell_price'=>65],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 13 Screen',        'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>4,'cost_price'=>18,'sell_price'=>60],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 15 Battery',       'part_type'=>'Battery',       'quality'=>'Original',   'stock'=>4,'cost_price'=>18,'sell_price'=>45],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 14 Battery',       'part_type'=>'Battery',       'quality'=>'Original',   'stock'=>5,'cost_price'=>15,'sell_price'=>40],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 13 Battery',       'part_type'=>'Battery',       'quality'=>'Compatible', 'stock'=>6,'cost_price'=>10,'sell_price'=>30],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 15 USB-C Port',    'part_type'=>'Charging Port', 'quality'=>'Original',   'stock'=>3,'cost_price'=>12,'sell_price'=>35],
            ['category_id'=>$iphone->id,  'name'=>'iPhone 14 Lightning Port','part_type'=>'Charging Port', 'quality'=>'Compatible', 'stock'=>4,'cost_price'=>8, 'sell_price'=>25],
            ['category_id'=>$samsung->id, 'name'=>'Galaxy S24 Ultra Screen', 'part_type'=>'Screen',        'quality'=>'Original',   'stock'=>2,'cost_price'=>55,'sell_price'=>130],
            ['category_id'=>$samsung->id, 'name'=>'Galaxy S24 Screen',       'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>3,'cost_price'=>30,'sell_price'=>80],
            ['category_id'=>$samsung->id, 'name'=>'Galaxy A54 Screen',       'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>4,'cost_price'=>22,'sell_price'=>60],
            ['category_id'=>$samsung->id, 'name'=>'Galaxy S24 Battery',      'part_type'=>'Battery',       'quality'=>'Compatible', 'stock'=>3,'cost_price'=>12,'sell_price'=>35],
            ['category_id'=>$xiaomi->id,  'name'=>'Redmi Note 12 Screen',    'part_type'=>'Screen',        'quality'=>'Compatible', 'stock'=>4,'cost_price'=>20,'sell_price'=>55],
        ];
        foreach ($parts as $p) Part::create($p);

        $p1 = Part::where('name','iPhone 15 Pro Max Screen')->first();
        $p2 = Part::where('name','iPhone 14 Pro Screen')->first();
        $p3 = Part::where('name','iPhone 14 Lightning Port')->first();
        $p4 = Part::where('name','Galaxy S24 Ultra Screen')->first();
        $p5 = Part::where('name','iPhone 13 Battery')->first();
        $p6 = Part::where('name','iPhone 15 Pro Screen')->first();
        $p7 = Part::where('name','Galaxy S24 Battery')->first();
        $p8 = Part::where('name','iPhone 15 USB-C Port')->first();

        // ── Customers ─────────────────────────────────────────────────────
        $customers = [
            ['name'=>'James Wilson',  'phone'=>'07700 900111','email'=>'james@email.com'],
            ['name'=>'Sarah Ahmed',   'phone'=>'07700 900222','email'=>'sarah@email.com'],
            ['name'=>'Daniel Carter', 'phone'=>'07700 900333','email'=>null],
            ['name'=>'Priya Patel',   'phone'=>'07700 900444','email'=>'priya@email.com'],
            ['name'=>'Tom Hughes',    'phone'=>'07700 900555','email'=>null],
            ['name'=>'Aisha Rahman',  'phone'=>'07700 900666','email'=>'aisha@email.com'],
        ];
        foreach ($customers as $c) Customer::create($c);

        // ── Vouchers ──────────────────────────────────────────────────────
        Voucher::create(['code'=>'SAVE10','type'=>'percent','value'=>10,'min_spend'=>50, 'is_active'=>true,'notes'=>'10% off orders over £50']);
        Voucher::create(['code'=>'FLAT5', 'type'=>'fixed',  'value'=>5, 'min_spend'=>0,  'is_active'=>true,'notes'=>'£5 off any repair']);
        Voucher::create(['code'=>'VIP20', 'type'=>'percent','value'=>20,'min_spend'=>100,'is_active'=>true,'customer_id'=>2,'notes'=>'VIP 20% off for Sarah']);

        // ── Helper to create device + repairs ─────────────────────────────
        $makeDevice = function(int $jobId, string $name, ?string $note, array $repairs, int $order = 0) use ($rtScreen, $rtBattery, $rtCharging): void {
            $device = Device::create(['job_id'=>$jobId,'name'=>$name,'note'=>$note,'sort_order'=>$order]);
            foreach ($repairs as $r) {
                RepairItem::create([
                    'device_id'      => $device->id,
                    'repair_type_id' => $r['repair_type_id'] ?? null,
                    'part_id'        => $r['part_id'] ?? null,
                    'issue'          => $r['issue'] ?? null,
                    'price'          => $r['price'],
                    'status'         => $r['status'] ?? 'In Progress',
                ]);
            }
        };

        // ── Jobs ──────────────────────────────────────────────────────────

        // Job 1: James — iPhone 15 Pro Max screen replacement
        $j1 = Job::create(['customer_id'=>1,'date_in'=>'2025-03-01','date_out'=>'2025-03-01','status'=>'Completed']);
        $makeDevice($j1->id, 'iPhone 15 Pro Max', null, [
            ['repair_type_id'=>$rtScreen->id,'part_id'=>$p1->id,'issue'=>'Cracked front glass','price'=>110,'status'=>'Completed'],
        ]);
        Payment::create(['job_id'=>$j1->id,'payment_type'=>'Cash','amount'=>110]);

        // Job 2: Sarah — 2 devices: iPhone 14 Pro (screen) + iPhone 14 (charging port), £5 discount
        $j2 = Job::create(['customer_id'=>2,'date_in'=>'2025-03-02','date_out'=>'2025-03-02','status'=>'Completed','discount_type'=>'fixed','discount_value'=>5]);
        $makeDevice($j2->id, 'iPhone 14 Pro', 'Back cover also cracked', [
            ['repair_type_id'=>$rtScreen->id,  'part_id'=>$p2->id,'issue'=>'Black screen after drop','price'=>105,'status'=>'Completed'],
        ], 0);
        $makeDevice($j2->id, 'iPhone 14', null, [
            ['repair_type_id'=>$rtCharging->id,'part_id'=>$p3->id,'issue'=>'Not charging','price'=>25,'status'=>'Completed'],
        ], 1);
        Payment::create(['job_id'=>$j2->id,'payment_type'=>'Card','amount'=>125]);

        // Job 3: Daniel — Samsung S24 Ultra, 2 repairs on same device (screen + speaker)
        $rtSpeaker = RepairType::where('name','Speaker')->first();
        $j3 = Job::create(['customer_id'=>3,'date_in'=>'2025-03-03','status'=>'In Progress']);
        $makeDevice($j3->id, 'Samsung Galaxy S24 Ultra', 'Customer dropped in water briefly', [
            ['repair_type_id'=>$rtScreen->id,  'part_id'=>$p4->id, 'issue'=>'Shattered screen','price'=>130,'status'=>'In Progress'],
            ['repair_type_id'=>$rtSpeaker->id, 'part_id'=>null,    'issue'=>'Speaker crackling after water','price'=>35,'status'=>'Waiting Parts'],
        ]);

        // Job 4: Priya — iPhone 13 battery, voucher applied
        $j4 = Job::create(['customer_id'=>4,'date_in'=>'2025-03-04','status'=>'Waiting Parts','voucher_code'=>'FLAT5','voucher_amount'=>5]);
        $makeDevice($j4->id, 'iPhone 13', null, [
            ['repair_type_id'=>$rtBattery->id,'part_id'=>$p5->id,'issue'=>'Battery drains in 2 hours','price'=>30,'status'=>'Waiting Parts'],
        ]);

        // Job 5: Tom — 2 devices, 10% discount
        $j5 = Job::create(['customer_id'=>5,'date_in'=>'2025-03-05','date_out'=>'2025-03-05','status'=>'Completed','discount_type'=>'percent','discount_value'=>10]);
        $makeDevice($j5->id, 'iPhone 15 Pro', null, [
            ['repair_type_id'=>$rtScreen->id,  'part_id'=>$p6->id,'issue'=>'Cracked display','price'=>100,'status'=>'Completed'],
        ], 0);
        $makeDevice($j5->id, 'Samsung Galaxy A54', 'Swollen battery noticed', [
            ['repair_type_id'=>$rtBattery->id, 'part_id'=>$p7->id,'issue'=>'Battery swollen','price'=>35,'status'=>'Completed'],
        ], 1);
        Payment::create(['job_id'=>$j5->id,'payment_type'=>'Cash','amount'=>121.50]);

        // Job 6: Aisha — 3 repairs on 1 device (screen + charging + battery)
        $j6 = Job::create(['customer_id'=>6,'date_in'=>'2025-03-06','date_out'=>'2025-03-06','status'=>'Completed']);
        $makeDevice($j6->id, 'iPhone 15', 'Multiple issues from same drop', [
            ['repair_type_id'=>$rtScreen->id,   'part_id'=>null,  'issue'=>'Cracked screen, customer has own part','price'=>40,'status'=>'Completed'],
            ['repair_type_id'=>$rtCharging->id, 'part_id'=>$p8->id,'issue'=>'USB-C port bent after drop','price'=>35,'status'=>'Completed'],
            ['repair_type_id'=>$rtBattery->id,  'part_id'=>null,  'issue'=>'Battery health at 68%','price'=>45,'status'=>'Completed'],
        ]);
        Payment::create(['job_id'=>$j6->id,'payment_type'=>'Trade','amount'=>120,'notes'=>'Customer traded old Samsung']);
    }
}
