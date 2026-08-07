<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\MaintenanceReport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@arms.test',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        $staff = User::create([
            'name' => 'Staff Officer',
            'email' => 'staff@arms.test',
            'password' => Hash::make('password'),
            'role' => 'Staff',
        ]);

        $user1 = User::create([
            'name' => 'General User',
            'email' => 'user@arms.test',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);

        $user2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@arms.test',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);

        $user3 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@arms.test',
            'password' => Hash::make('password'),
            'role' => 'User',
        ]);

        // 2. Assets
        $assetsData = [
            ['asset_code' => 'AST-0001', 'name' => 'Projector Epson EB-X400', 'category' => 'Electronics', 'condition' => 'Good', 'location' => 'Audio Visual Room'],
            ['asset_code' => 'AST-0002', 'name' => 'Dell Latitude 5420 Laptop', 'category' => 'Computer', 'condition' => 'Good', 'location' => 'Server Room'],
            ['asset_code' => 'AST-0003', 'name' => 'Yamaha Stage Sound System', 'category' => 'Audio', 'condition' => 'Needs Repair', 'location' => 'Main Auditorium'],
            ['asset_code' => 'AST-0004', 'name' => 'Canon EOS 80D Camera', 'category' => 'Multimedia', 'condition' => 'Good', 'location' => 'Media Center'],
            ['asset_code' => 'AST-0005', 'name' => 'HP LaserJet Pro Printer', 'category' => 'Office Equipment', 'condition' => 'Good', 'location' => 'Administration Office'],
            ['asset_code' => 'AST-0006', 'name' => 'Cisco Catalyst Switch 2960', 'category' => 'Networking', 'condition' => 'Good', 'location' => 'IT Server Rack'],
            ['asset_code' => 'AST-0007', 'name' => 'Smart TV Samsung 65 Inch', 'category' => 'Electronics', 'condition' => 'Good', 'location' => 'Executive Meeting Room'],
            ['asset_code' => 'AST-0008', 'name' => 'Air Conditioner Daikin 2HP', 'category' => 'HVAC', 'condition' => 'Damaged', 'location' => 'Conference Hall A'],
            ['asset_code' => 'AST-0009', 'name' => 'Shure Wireless Microphone Set', 'category' => 'Audio', 'condition' => 'Good', 'location' => 'Main Auditorium'],
            ['asset_code' => 'AST-0010', 'name' => 'Logitech Rally Bar Video Conf', 'category' => 'Multimedia', 'condition' => 'Good', 'location' => 'Executive Meeting Room'],
        ];

        $assets = [];
        foreach ($assetsData as $data) {
            $assets[] = Asset::create($data);
        }

        // 3. Rooms
        $roomsData = [
            ['room_code' => 'RM-001', 'name' => 'Main Auditorium', 'capacity' => 200, 'status' => 'Available'],
            ['room_code' => 'RM-002', 'name' => 'Executive Meeting Room', 'capacity' => 20, 'status' => 'Available'],
            ['room_code' => 'RM-003', 'name' => 'Conference Hall A', 'capacity' => 60, 'status' => 'Occupied'],
            ['room_code' => 'RM-004', 'name' => 'Workshop Room 101', 'capacity' => 35, 'status' => 'Available'],
            ['room_code' => 'RM-005', 'name' => 'Media Studio Center', 'capacity' => 15, 'status' => 'Under Maintenance'],
        ];

        $rooms = [];
        foreach ($roomsData as $data) {
            $rooms[] = Room::create($data);
        }

        // 4. Reservations
        $reservationsData = [
            [
                'reservation_code' => 'RSV-20260801-001',
                'user_id' => $user1->id,
                'room_id' => $rooms[0]->id,
                'reservation_date' => '2026-08-10',
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
                'purpose' => 'Annual Organizational General Assembly',
                'status' => 'Approved',
            ],
            [
                'reservation_code' => 'RSV-20260801-002',
                'user_id' => $user2->id,
                'room_id' => $rooms[1]->id,
                'reservation_date' => '2026-08-12',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00',
                'purpose' => 'Department Coordination Meeting',
                'status' => 'Pending',
            ],
            [
                'reservation_code' => 'RSV-20260802-001',
                'user_id' => $user3->id,
                'room_id' => $rooms[2]->id,
                'reservation_date' => '2026-08-14',
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'purpose' => 'Technical Workshop Session',
                'status' => 'Approved',
            ],
            [
                'reservation_code' => 'RSV-20260802-002',
                'user_id' => $user1->id,
                'room_id' => $rooms[3]->id,
                'reservation_date' => '2026-08-15',
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
                'purpose' => 'UKK Practice Exam Preparation',
                'status' => 'Pending',
            ],
            [
                'reservation_code' => 'RSV-20260803-001',
                'user_id' => $user2->id,
                'room_id' => $rooms[4]->id,
                'reservation_date' => '2026-08-16',
                'start_time' => '14:00:00',
                'end_time' => '16:00:00',
                'purpose' => 'Video Production Shoot',
                'status' => 'Rejected',
            ],
            [
                'reservation_code' => 'RSV-20260803-002',
                'user_id' => $user3->id,
                'room_id' => $rooms[1]->id,
                'reservation_date' => '2026-08-18',
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'purpose' => 'Client Orientation Briefing',
                'status' => 'Approved',
            ],
            [
                'reservation_code' => 'RSV-20260804-001',
                'user_id' => $user1->id,
                'room_id' => $rooms[0]->id,
                'reservation_date' => '2026-08-20',
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'purpose' => 'Graduation Ceremony Rehearsal',
                'status' => 'Pending',
            ],
            [
                'reservation_code' => 'RSV-20260804-002',
                'user_id' => $user2->id,
                'room_id' => $rooms[3]->id,
                'reservation_date' => '2026-08-22',
                'start_time' => '08:30:00',
                'end_time' => '11:30:00',
                'purpose' => 'Staff Evaluation Briefing',
                'status' => 'Approved',
            ],
            [
                'reservation_code' => 'RSV-20260804-003',
                'user_id' => $user3->id,
                'room_id' => $rooms[2]->id,
                'reservation_date' => '2026-08-25',
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'purpose' => 'Community Seminar',
                'status' => 'Pending',
            ],
            [
                'reservation_code' => 'RSV-20260804-004',
                'user_id' => $user1->id,
                'room_id' => $rooms[1]->id,
                'reservation_date' => '2026-08-28',
                'start_time' => '15:00:00',
                'end_time' => '17:00:00',
                'purpose' => 'Strategic Planning Meeting',
                'status' => 'Pending',
            ],
        ];

        foreach ($reservationsData as $data) {
            Reservation::create($data);
        }

        // 5. Maintenance Reports
        $maintenanceData = [
            [
                'report_code' => 'MNT-20260801-001',
                'user_id' => $user1->id,
                'asset_id' => $assets[2]->id,
                'description' => 'Left channel audio speaker produces crackling noise during high volume.',
                'status' => 'Pending',
            ],
            [
                'report_code' => 'MNT-20260801-002',
                'user_id' => $user2->id,
                'asset_id' => $assets[7]->id,
                'description' => 'AC unit is not cooling and leaking water onto the floor.',
                'status' => 'Pending',
            ],
            [
                'report_code' => 'MNT-20260802-001',
                'user_id' => $user3->id,
                'asset_id' => $assets[0]->id,
                'description' => 'Projector lamp brightness is flickering intermittently.',
                'status' => 'Completed',
            ],
            [
                'report_code' => 'MNT-20260802-002',
                'user_id' => $user1->id,
                'asset_id' => $assets[4]->id,
                'description' => 'Paper jam issue in main tray and low toner alert.',
                'status' => 'Completed',
            ],
            [
                'report_code' => 'MNT-20260803-001',
                'user_id' => $user2->id,
                'asset_id' => $assets[1]->id,
                'description' => 'Battery health degraded, laptop turns off when unplugged.',
                'status' => 'Pending',
            ],
            [
                'report_code' => 'MNT-20260803-002',
                'user_id' => $user3->id,
                'asset_id' => $assets[8]->id,
                'description' => 'Wireless mic transmitter antenna broken.',
                'status' => 'Completed',
            ],
            [
                'report_code' => 'MNT-20260804-001',
                'user_id' => $user1->id,
                'asset_id' => $assets[5]->id,
                'description' => 'Port 12 on network switch shows high packet drop rate.',
                'status' => 'Pending',
            ],
            [
                'report_code' => 'MNT-20260804-002',
                'user_id' => $user2->id,
                'asset_id' => $assets[3]->id,
                'description' => 'Lens autofocus mechanism sticking occasionally.',
                'status' => 'Completed',
            ],
            [
                'report_code' => 'MNT-20260804-003',
                'user_id' => $user3->id,
                'asset_id' => $assets[6]->id,
                'description' => 'HDMI input port 2 loose contact.',
                'status' => 'Pending',
            ],
            [
                'report_code' => 'MNT-20260804-004',
                'user_id' => $user1->id,
                'asset_id' => $assets[9]->id,
                'description' => 'Firmware update required for camera tracking feature.',
                'status' => 'Completed',
            ],
        ];

        foreach ($maintenanceData as $data) {
            MaintenanceReport::create($data);
        }
    }
}
